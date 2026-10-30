<?php

namespace Tests\Feature;

use App\Filament\Pages\ShopPages;
use App\Models\PageSetting;
use App\Models\User;
use App\Support\PageEditor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShopPagesEditorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    public function test_guests_cannot_open_the_editor(): void
    {
        $this->get('/admin/shop-pages')->assertRedirect();
    }

    public function test_text_override_is_rendered_and_reset_restores_default(): void
    {
        $this->actingAs($this->admin());

        Livewire::withQueryParams(['page' => 'how'])->test(ShopPages::class)
            ->fillForm(['texts' => ['en' => ['how__title' => 'Custom title']]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get(route('pages.how'))->assertSee('<h1>Custom title</h1>', false);

        Livewire::withQueryParams(['page' => 'how'])->test(ShopPages::class)->callAction('reset');

        $this->get(route('pages.how'))->assertDontSee('Custom title')->assertSee('<h1>How it works</h1>', false);
    }

    public function test_styles_are_scoped_to_the_page_and_sanitised(): void
    {
        PageSetting::create(['page_key' => 'how', 'styles' => [
            'vars' => ['brand' => '#1d4ed8', 'accent' => 'red;}body{display:none'],
            'hero' => ['background_color' => '#fde68a', 'border_width' => '3', 'border_color' => '#b45309', 'radius' => '8'],
        ]]);

        $css = PageEditor::css('how');
        $this->assertStringContainsString('body[data-page="how"] .hero', $css);
        $this->assertStringContainsString('border:3px solid #b45309', $css);
        $this->assertStringContainsString('--brand-solid:#1d4ed8', $css);
        $this->assertStringNotContainsString('display:none', $css);
        $this->assertSame('', PageEditor::css('faq'));
    }

    public function test_hero_image_override_is_used_by_page_hero(): void
    {
        PageSetting::create(['page_key' => 'how', 'images' => ['hero' => 'uploads/pages/x.png']]);

        $this->get(route('pages.how'))->assertSee('uploads/pages/x.png', false);
        $this->get(route('pages.faq'))->assertDontSee('uploads/pages/x.png', false);
    }
}
