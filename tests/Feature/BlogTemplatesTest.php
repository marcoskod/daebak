<?php

namespace Tests\Feature;

use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\EditBlogPost;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BlogTemplatesTest extends TestCase
{
    use RefreshDatabase;

    private function mk(array $attrs = []): BlogPost
    {
        return BlogPost::create($attrs + [
            'slug' => 'demo', 'category' => 'culture', 'author' => 'Dae', 'is_published' => true, 'published_at' => now()->subDay(),
            'tags' => ['a'], 'products' => [],
            'translations' => ['en' => ['title' => 'Demo', 'excerpt' => 'Ex', 'content' => "Intro text.\n\n## One\nHello **world**\n- a\n- b\n\n## Two\n> quote\n\nMore."]],
        ]);
    }

    public function test_unknown_template_falls_back_to_classic(): void
    {
        $this->assertSame('classic', $this->mk(['template' => 'nope'])->template);
        $this->assertSame('classic', $this->mk(['slug' => 'x2', 'template' => null])->template);
    }

    public function test_every_template_renders(): void
    {
        foreach (array_keys(config('blog_templates.templates')) as $key) {
            $post = $this->mk(['slug' => "p-$key", 'template' => $key]);
            $html = $this->get(route('blog.show', $post->slug))->assertOk()->getContent();
            $this->assertStringContainsString('<h1 itemprop="headline">Demo</h1>', $html, $key);
            $this->assertStringContainsString('application/ld+json', $html, $key);
        }
    }

    public function test_tpl_query_is_admin_only(): void
    {
        $post = $this->mk();
        $this->get(route('blog.show', [$post->slug, 'tpl' => 'timeline']))->assertOk()->assertDontSee('post--timeline', false);

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get(route('blog.show', [$post->slug, 'tpl' => 'timeline']))->assertOk()->assertSee('post--timeline', false);
    }

    public function test_admin_can_create_and_edit_with_template(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin);

        Livewire::test(CreateBlogPost::class)
            ->assertFormSet(['template' => 'classic'])
            ->fillForm(['template' => 'cards', 'slug' => 'new-one', 'category' => 'news', 'translations' => ['en' => ['title' => 'New', 'content' => 'Body']]])
            ->call('create')->assertHasNoFormErrors();
        $post = BlogPost::where('slug', 'new-one')->firstOrFail();
        $this->assertSame('cards', $post->template);

        Livewire::test(EditBlogPost::class, ['record' => $post->getKey()])
            ->assertFormSet(['template' => 'cards'])
            ->fillForm(['template' => 'magazine'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('magazine', $post->fresh()->template);
    }
}
