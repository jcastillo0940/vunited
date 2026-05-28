<?php

namespace Tests\Feature\Admin\Audit;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Audit\Models\AuditLog;
use App\Domain\Menus\Models\Menu;
use App\Domain\Menus\Models\MenuItem;
use App\Domain\News\Models\NewsArticle;
use App\Domain\News\Models\NewsCategory;
use App\Domain\Pages\Models\Page;
use App\Domain\Pages\Models\PageSection;
use App\Domain\Settings\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_settings_creates_an_audit_log_with_actor_and_context(): void
    {
        $adminUser = $this->createAdminWithPermissions(['settings.view', 'settings.update']);
        $settings = SiteSetting::factory()->create([
            'site_name' => 'Club Original',
            'contact_email' => 'old@veraguas.test',
        ]);

        $this->withHeaders(['User-Agent' => 'PhaseHTest/1.0'])
            ->withServerVariables(['REMOTE_ADDR' => '127.0.0.10'])
            ->actingAs($adminUser, 'admin')
            ->put('/admin/settings', [
                'site_name' => 'Veraguas United FC',
                'site_tagline' => 'Orgullo de Veraguas',
                'primary_logo_path' => null,
                'secondary_logo_path' => null,
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
            ])
            ->assertRedirect('/admin/settings');

        $log = AuditLog::query()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertSame($adminUser->id, $log->admin_user_id);
        $this->assertSame('settings', $log->module);
        $this->assertSame('updated', $log->action);
        $this->assertSame(SiteSetting::class, $log->auditable_type);
        $this->assertSame($settings->id, $log->auditable_id);
        $this->assertSame('Club Original', $log->old_values['site_name']);
        $this->assertSame('Veraguas United FC', $log->new_values['site_name']);
        $this->assertSame('127.0.0.10', $log->ip_address);
        $this->assertSame('PhaseHTest/1.0', $log->user_agent);
    }

    public function test_creating_a_menu_and_updating_a_menu_item_create_audit_logs(): void
    {
        $adminUser = $this->createAdminWithPermissions(['menus.view', 'menus.manage']);

        $this->actingAs($adminUser, 'admin')
            ->post('/admin/menus', [
                'name' => 'Header Principal',
                'location' => 'header',
                'is_active' => true,
            ])
            ->assertRedirect('/admin/menus');

        $menu = Menu::query()->where('name', 'Header Principal')->firstOrFail();
        $menuCreatedLog = AuditLog::query()
            ->where('module', 'menus')
            ->where('action', 'created')
            ->where('auditable_type', Menu::class)
            ->where('auditable_id', $menu->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($menuCreatedLog);
        $this->assertSame('Header Principal', $menuCreatedLog->new_values['name']);

        $this->actingAs($adminUser, 'admin')
            ->post("/admin/menus/{$menu->id}/items", [
                'parent_id' => null,
                'label' => 'Noticias',
                'url' => '/noticias',
                'target' => '_self',
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->assertRedirect("/admin/menus/{$menu->id}/edit");

        $menuItem = MenuItem::query()->where('menu_id', $menu->id)->firstOrFail();

        $this->actingAs($adminUser, 'admin')
            ->put("/admin/menus/{$menu->id}/items/{$menuItem->id}", [
                'parent_id' => null,
                'label' => 'Plantilla',
                'url' => '/plantilla',
                'target' => '_blank',
                'sort_order' => 4,
                'is_active' => false,
            ])
            ->assertRedirect("/admin/menus/{$menu->id}/edit");

        $menuItemUpdatedLog = AuditLog::query()
            ->where('module', 'menus')
            ->where('action', 'updated')
            ->where('auditable_type', MenuItem::class)
            ->where('auditable_id', $menuItem->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($menuItemUpdatedLog);
        $this->assertSame('Noticias', $menuItemUpdatedLog->old_values['label']);
        $this->assertSame('Plantilla', $menuItemUpdatedLog->new_values['label']);
    }

    public function test_creating_and_updating_a_page_with_sections_creates_audit_logs(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);

        $this->actingAs($adminUser, 'admin')
            ->post('/admin/pages', [
                'title' => 'Historia',
                'slug' => 'historia',
                'excerpt' => 'Resumen',
                'status' => 'draft',
                'published_at' => null,
                'seo_title' => 'Historia',
                'seo_description' => 'Historia del club',
                'is_home' => false,
                'sections' => [
                    [
                        'section_key' => 'hero',
                        'type' => 'banner',
                        'title' => 'Hero',
                        'body' => 'Contenido hero',
                        'payload' => ['cta_label' => 'Ver mas'],
                        'sort_order' => 1,
                        'is_active' => true,
                        'image_path' => null,
                    ],
                ],
            ])
            ->assertRedirect('/admin/pages');

        $page = Page::query()->where('slug', 'historia')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'module' => 'pages',
            'action' => 'created',
            'auditable_type' => Page::class,
            'auditable_id' => $page->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'module' => 'pages',
            'action' => 'created',
            'auditable_type' => PageSection::class,
        ]);

        $existingSection = $page->sections()->firstOrFail();

        $this->actingAs($adminUser, 'admin')
            ->put("/admin/pages/{$page->id}", [
                'title' => 'Historia Oficial',
                'slug' => 'historia-oficial',
                'excerpt' => 'Resumen actualizado',
                'status' => 'published',
                'published_at' => now()->toDateTimeString(),
                'seo_title' => 'Historia Oficial',
                'seo_description' => 'Nueva historia del club',
                'is_home' => true,
                'sections' => [
                    [
                        'section_key' => 'hero',
                        'type' => 'banner',
                        'title' => 'Hero actualizado',
                        'body' => 'Contenido hero actualizado',
                        'payload' => ['cta_label' => 'Comprar'],
                        'sort_order' => 2,
                        'is_active' => true,
                        'image_path' => null,
                    ],
                ],
            ])
            ->assertRedirect('/admin/pages');

        $pageUpdatedLog = AuditLog::query()
            ->where('module', 'pages')
            ->where('action', 'updated')
            ->where('auditable_type', Page::class)
            ->where('auditable_id', $page->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($pageUpdatedLog);
        $this->assertSame('Historia', $pageUpdatedLog->old_values['title']);
        $this->assertSame('Historia Oficial', $pageUpdatedLog->new_values['title']);

        $sectionDeletedLog = AuditLog::query()
            ->where('module', 'pages')
            ->where('action', 'deleted')
            ->where('auditable_type', PageSection::class)
            ->where('auditable_id', $existingSection->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($sectionDeletedLog);
        $this->assertSame('Hero', $sectionDeletedLog->old_values['title']);
    }

    public function test_creating_and_deleting_news_article_creates_audit_logs(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);
        $category = NewsCategory::factory()->create();

        $this->actingAs($adminUser, 'admin')
            ->post('/admin/news', [
                'news_category_id' => $category->id,
                'title' => 'Victoria en casa',
                'slug' => 'victoria-en-casa',
                'summary' => 'Resumen',
                'body' => 'Contenido completo',
                'featured_image_path' => null,
                'status' => 'published',
                'published_at' => now()->toDateTimeString(),
                'is_featured' => true,
                'seo_title' => 'Victoria',
                'seo_description' => 'Detalle',
            ])
            ->assertRedirect('/admin/news');

        $article = NewsArticle::query()->where('slug', 'victoria-en-casa')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'module' => 'news',
            'action' => 'created',
            'auditable_type' => NewsArticle::class,
            'auditable_id' => $article->id,
        ]);

        $this->actingAs($adminUser, 'admin')
            ->delete("/admin/news/{$article->id}")
            ->assertRedirect('/admin/news');

        $newsDeletedLog = AuditLog::query()
            ->where('module', 'news')
            ->where('action', 'deleted')
            ->where('auditable_type', NewsArticle::class)
            ->where('auditable_id', $article->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($newsDeletedLog);
        $this->assertSame('Victoria en casa', $newsDeletedLog->old_values['title']);
        $this->assertNull($newsDeletedLog->new_values);
    }

    public function test_deleting_a_page_creates_an_audit_log(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);
        $page = Page::factory()->create([
            'title' => 'Estadio',
            'slug' => 'estadio',
        ]);

        $this->actingAs($adminUser, 'admin')
            ->delete("/admin/pages/{$page->id}")
            ->assertRedirect('/admin/pages');

        $pageDeletedLog = AuditLog::query()
            ->where('module', 'pages')
            ->where('action', 'deleted')
            ->where('auditable_type', Page::class)
            ->where('auditable_id', $page->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($pageDeletedLog);
        $this->assertSame('Estadio', $pageDeletedLog->old_values['title']);
        $this->assertNull($pageDeletedLog->new_values);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $adminUser = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'audit-role-'.count($permissionNames).'-'.fake()->unique()->slug(),
            'label' => 'Audit Role',
        ]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['label' => str($permissionName)->replace('.', ' ')->title()->toString()],
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $adminUser->roles()->syncWithoutDetaching([$role->id]);

        return $adminUser;
    }
}
