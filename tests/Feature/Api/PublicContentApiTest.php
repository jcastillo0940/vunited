<?php

namespace Tests\Feature\Api;

use App\Domain\Menus\Models\Menu;
use App\Domain\Menus\Models\MenuItem;
use App\Domain\News\Models\NewsArticle;
use App\Domain\News\Models\NewsCategory;
use App\Domain\Pages\Models\Page;
use App\Domain\Pages\Models\PageSection;
use App\Domain\Settings\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_settings_endpoint_returns_saved_settings_without_authentication(): void
    {
        SiteSetting::factory()->create([
            'site_name' => 'Veraguas United FC',
            'site_tagline' => 'Orgullo de Veraguas',
            'primary_logo_path' => 'logos/primary.png',
            'secondary_logo_path' => 'logos/secondary.png',
            'primary_color' => '#123ABC',
            'accent_color' => '#abcdef',
            'contact_email' => 'hola@veraguasunited.test',
            'contact_phone' => '+507 6000-0000',
            'social_links' => [
                'instagram' => 'https://instagram.com/veraguasunited',
            ],
            'global_seo_title' => 'Veraguas United FC',
            'global_seo_description' => 'Sitio oficial del club.',
            'maintenance_mode' => true,
        ]);

        $response = $this->getJson('/api/site-settings');

        $response->assertOk()->assertJson([
            'data' => [
                'site_name' => 'Veraguas United FC',
                'site_tagline' => 'Orgullo de Veraguas',
                'primary_logo_url' => '/storage/logos/primary.png',
                'secondary_logo_url' => '/storage/logos/secondary.png',
                'primary_color' => '#123ABC',
                'accent_color' => '#abcdef',
                'contact_email' => 'hola@veraguasunited.test',
                'contact_phone' => '+507 6000-0000',
                'social_links' => [
                    'instagram' => 'https://instagram.com/veraguasunited',
                ],
                'global_seo_title' => 'Veraguas United FC',
                'global_seo_description' => 'Sitio oficial del club.',
                'maintenance_mode' => true,
            ],
        ]);
    }

    public function test_header_menu_endpoint_returns_only_active_items_ordered_with_children(): void
    {
        $menu = Menu::factory()->create([
            'location' => 'header',
            'is_active' => true,
        ]);

        $first = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Noticias',
            'sort_order' => 1,
            'is_active' => true,
            'parent_id' => null,
        ]);
        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Inactivo',
            'sort_order' => 2,
            'is_active' => false,
            'parent_id' => null,
        ]);
        $second = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Club',
            'sort_order' => 3,
            'is_active' => true,
            'parent_id' => null,
        ]);
        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Historia',
            'sort_order' => 1,
            'is_active' => true,
            'parent_id' => $second->id,
        ]);
        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Directiva inactiva',
            'sort_order' => 2,
            'is_active' => false,
            'parent_id' => $second->id,
        ]);

        $response = $this->getJson('/api/menu/header');

        $response->assertOk()
            ->assertJsonPath('data.location', 'header')
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.items.0.label', 'Noticias')
            ->assertJsonPath('data.items.0.sort_order', 1)
            ->assertJsonPath('data.items.1.label', 'Club')
            ->assertJsonPath('data.items.1.children.0.label', 'Historia');

        $this->assertSame($first->id, $menu->fresh()->items()->first()->id);
    }

    public function test_footer_menu_endpoint_returns_only_active_items_ordered(): void
    {
        $menu = Menu::factory()->create([
            'location' => 'footer',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Privacidad',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Terminos',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/menu/footer');

        $response->assertOk()
            ->assertJsonPath('data.location', 'footer')
            ->assertJsonPath('data.items.0.label', 'Terminos')
            ->assertJsonPath('data.items.1.label', 'Privacidad');
    }

    public function test_page_detail_endpoint_returns_published_page_with_active_sections_ordered(): void
    {
        $page = Page::factory()->create([
            'title' => 'Historia',
            'slug' => 'historia',
            'excerpt' => 'Resumen institucional',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'seo_title' => 'Historia del club',
            'seo_description' => 'Conoce la historia del club.',
        ]);

        PageSection::factory()->create([
            'page_id' => $page->id,
            'section_key' => 'content',
            'title' => 'Contenido',
            'sort_order' => 2,
            'is_active' => true,
            'image_path' => 'pages/content.png',
        ]);
        PageSection::factory()->create([
            'page_id' => $page->id,
            'section_key' => 'hero',
            'title' => 'Hero',
            'sort_order' => 1,
            'is_active' => true,
            'payload' => ['cta_label' => 'Ver mas'],
            'image_path' => 'pages/hero.png',
        ]);
        PageSection::factory()->create([
            'page_id' => $page->id,
            'section_key' => 'hidden',
            'title' => 'Oculta',
            'sort_order' => 3,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/pages/historia');

        $response->assertOk()
            ->assertJsonPath('data.title', 'Historia')
            ->assertJsonPath('data.slug', 'historia')
            ->assertJsonPath('data.status', 'published')
            ->assertJsonCount(2, 'data.sections')
            ->assertJsonPath('data.sections.0.section_key', 'hero')
            ->assertJsonPath('data.sections.0.payload.cta_label', 'Ver mas')
            ->assertJsonPath('data.sections.0.image_url', '/storage/pages/hero.png')
            ->assertJsonPath('data.sections.1.section_key', 'content');
    }

    public function test_page_detail_endpoint_does_not_return_draft_archived_or_scheduled_future_pages(): void
    {
        Page::factory()->create([
            'slug' => 'borrador',
            'status' => 'draft',
        ]);
        Page::factory()->create([
            'slug' => 'archivada',
            'status' => 'archived',
        ]);
        Page::factory()->create([
            'slug' => 'programada',
            'status' => 'scheduled',
            'published_at' => now()->addDay(),
        ]);

        $this->getJson('/api/pages/borrador')->assertNotFound();
        $this->getJson('/api/pages/archivada')->assertNotFound();
        $this->getJson('/api/pages/programada')->assertNotFound();
    }

    public function test_news_index_returns_only_public_news_articles(): void
    {
        $category = NewsCategory::factory()->create([
            'name' => 'Primer Equipo',
            'slug' => 'primer-equipo',
        ]);

        NewsArticle::factory()->create([
            'news_category_id' => $category->id,
            'title' => 'Publicada',
            'slug' => 'publicada',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'is_featured' => true,
            'featured_image_path' => 'news/publicada.png',
        ]);
        NewsArticle::factory()->create([
            'title' => 'Borrador',
            'slug' => 'borrador',
            'status' => 'draft',
        ]);
        NewsArticle::factory()->create([
            'title' => 'Archivada',
            'slug' => 'archivada',
            'status' => 'archived',
        ]);
        NewsArticle::factory()->create([
            'title' => 'Programada futura',
            'slug' => 'programada-futura',
            'status' => 'scheduled',
            'published_at' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/news');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Publicada')
            ->assertJsonPath('data.0.slug', 'publicada')
            ->assertJsonPath('data.0.is_featured', true)
            ->assertJsonPath('data.0.featured_image_url', '/storage/news/publicada.png')
            ->assertJsonPath('data.0.category.name', 'Primer Equipo');
    }

    public function test_news_detail_returns_published_article_and_hides_non_public_articles(): void
    {
        $published = NewsArticle::factory()->withCategory()->create([
            'title' => 'Victoria en casa',
            'slug' => 'victoria-en-casa',
            'summary' => 'Resumen breve',
            'body' => 'Contenido completo de la noticia.',
            'status' => 'published',
            'published_at' => now()->subMinutes(30),
            'is_featured' => true,
            'seo_title' => 'Victoria en casa',
            'seo_description' => 'Detalle de la victoria',
            'featured_image_path' => 'news/victoria.png',
        ]);

        NewsArticle::factory()->create([
            'slug' => 'draft-news',
            'status' => 'draft',
        ]);
        NewsArticle::factory()->create([
            'slug' => 'archived-news',
            'status' => 'archived',
        ]);
        NewsArticle::factory()->create([
            'slug' => 'scheduled-news',
            'status' => 'scheduled',
            'published_at' => now()->addHour(),
        ]);

        $this->getJson('/api/news/victoria-en-casa')
            ->assertOk()
            ->assertJsonPath('data.title', 'Victoria en casa')
            ->assertJsonPath('data.slug', 'victoria-en-casa')
            ->assertJsonPath('data.body', 'Contenido completo de la noticia.')
            ->assertJsonPath('data.featured_image_url', '/storage/news/victoria.png')
            ->assertJsonPath('data.category.name', $published->category?->name);

        $this->getJson('/api/news/draft-news')->assertNotFound();
        $this->getJson('/api/news/archived-news')->assertNotFound();
        $this->getJson('/api/news/scheduled-news')->assertNotFound();
    }
}
