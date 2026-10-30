<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Assistant du chat client. Si ANTHROPIC_API_KEY est défini, le modèle IA répond (avec le contexte boutique) ;
 * sinon (ou en cas d'erreur) une base de connaissances locale multilingue prend le relais (lang/{locale}/chat.json).
 */
class ChatAssistant
{
    public function reply(string $message, ChatConversation $conversation): string
    {
        if (filled(config('services.anthropic.key'))) {
            try {
                if ($answer = $this->askModel($message, $conversation)) {
                    return $answer;
                }
            } catch (\Throwable $e) {
                Log::warning('Chat AI failed: '.$e->getMessage());
            }
        }

        return $this->localAnswer($message);
    }

    protected function askModel(string $message, ChatConversation $conversation): ?string
    {
        $history = $conversation->messages()->latest('id')->take(10)->get()->reverse()
            ->map(fn ($m) => ['role' => $m->sender === 'visitor' ? 'user' : 'assistant', 'content' => $m->body])->values()->all();
        $history[] = ['role' => 'user', 'content' => $message];
        // L'API exige une alternance user/assistant commençant par user : on fusionne les doublons consécutifs.
        $clean = [];
        foreach ($history as $m) {
            if ($clean && end($clean)['role'] === $m['role']) {
                $clean[count($clean) - 1]['content'] .= "\n".$m['content'];
            } else {
                $clean[] = $m;
            }
        }
        while ($clean && $clean[0]['role'] !== 'user') {
            array_shift($clean);
        }

        $products = Product::where('is_active', true)->take(30)->get()
            ->map(fn ($p) => '- '.$p->localized_name.' ('.money($p->current_price).') /produit/'.$p->slug)->implode("\n");
        $system = t('chat.system_prompt', ['brand' => site('brand.name'), 'agent' => site('chat.agent_name'), 'threshold' => money(site('shipping.free_threshold'))])
            ."\n\nCatalog:\n".$products;

        $response = Http::withHeaders(['x-api-key' => config('services.anthropic.key'), 'anthropic-version' => '2023-06-01'])
            ->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model' => site('chat.ai_model'), 'max_tokens' => 400, 'system' => $system, 'messages' => $clean,
            ])->throw()->json();

        return trim((string) data_get($response, 'content.0.text')) ?: null;
    }

    protected function localAnswer(string $message): string
    {
        $text = Str::lower($message);

        // 1) Intentions FAQ (mots-clés dans la langue courante + anglais)
        $intents = array_merge(tg('chat.intents'), I18nFallback::intents());
        foreach ($intents as $intent) {
            foreach ($intent['keywords'] ?? [] as $kw) {
                if (str_contains($text, Str::lower($kw))) {
                    return $intent['answer'];
                }
            }
        }

        // 2) Recherche produit
        $tokens = collect(preg_split('/[\s,.;:!?\'"()]+/u', $text))->filter(fn ($w) => mb_strlen($w) >= 3)->unique()->take(6);
        if ($tokens->isNotEmpty()) {
            $found = Product::where('is_active', true)->where(function ($q) use ($tokens) {
                foreach ($tokens as $w) {
                    $q->orWhere('translations', 'like', "%{$w}%")->orWhere('keywords', 'like', "%{$w}%");
                }
            })->take(30)->get()
                ->sortByDesc(fn ($p) => $tokens->filter(fn ($w) => str_contains(Str::lower(json_encode($p->translations, JSON_UNESCAPED_UNICODE).json_encode($p->keywords)), $w))->count() * 1000 + $p->reviews_count)
                ->take(3)->values();

            if ($found->isNotEmpty()) {
                return t('chat.found')."\n".$found->map(fn ($p) => '• '.$p->localized_name.' — '.money($p->current_price)."\n".route('shop.produit', $p->slug, false))->implode("\n");
            }
        }

        return t('chat.fallback');
    }
}
