<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\ChatAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Chat client : mode IA (assistant) ou mode manuel (conversation reprise par l'équipe dans l'admin). */
class ChatController extends Controller
{
    protected function conversation(?string $uuid, bool $create = true): ?ChatConversation
    {
        $conv = $uuid ? ChatConversation::where('uuid', $uuid)->first() : null;
        if (! $conv && $create) {
            $conv = ChatConversation::create(['uuid' => (string) Str::uuid(), 'locale' => app()->getLocale(), 'last_message_at' => now()]);
        }

        return $conv;
    }

    protected function payload($messages): array
    {
        return $messages->map(fn ($m) => ['id' => $m->id, 'sender' => $m->sender, 'body' => $m->body])->values()->all();
    }

    public function message(Request $request, ChatAssistant $assistant): JsonResponse
    {
        $data = $request->validate([
            'uuid' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:600'],
            'mode' => ['nullable', 'in:ai,human'],
        ]);

        $conv = $this->conversation($data['uuid'] ?? null);
        $conv->messages()->create(['sender' => 'visitor', 'body' => $data['message']]);
        $conv->update(['last_message_at' => now()]);

        // Conversation reprise par l'équipe : on enregistre sans réponse automatique.
        if ($conv->mode === 'human') {
            $conv->update(['status' => 'waiting_agent']);

            return response()->json(['uuid' => $conv->uuid, 'messages' => []]);
        }

        $reply = $conv->messages()->create(['sender' => 'bot', 'body' => $assistant->reply($data['message'], $conv)]);

        return response()->json(['uuid' => $conv->uuid, 'messages' => $this->payload(collect([$reply]))]);
    }

    public function history(Request $request): JsonResponse
    {
        $conv = $this->conversation($request->query('uuid'), false);
        if (! $conv) {
            return response()->json(['messages' => [], 'mode' => 'ai']);
        }

        return response()->json(['mode' => $conv->mode, 'messages' => $this->payload($conv->messages()->orderBy('id')->get())]);
    }

    public function handoff(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uuid' => ['nullable', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:190'],
        ]);

        $conv = $this->conversation($data['uuid'] ?? null);
        $conv->update(['mode' => 'human', 'status' => 'waiting_agent', 'visitor_name' => $data['name'], 'visitor_email' => $data['email'], 'last_message_at' => now()]);
        $conv->messages()->create(['sender' => 'bot', 'body' => t('chat.handoff_ok')]);

        return response()->json(['uuid' => $conv->uuid]);
    }

    /** Sondage (5 s) des réponses de l'équipe pendant le mode manuel. */
    public function poll(Request $request): JsonResponse
    {
        $conv = $this->conversation($request->query('uuid'), false);
        if (! $conv) {
            return response()->json(['messages' => []]);
        }

        return response()->json(['messages' => $this->payload($conv->messages()->where('sender', 'agent')->where('id', '>', (int) $request->query('after'))->orderBy('id')->get())]);
    }
}
