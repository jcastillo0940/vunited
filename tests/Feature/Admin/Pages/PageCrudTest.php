<?php

namespace Tests\Feature\Admin\Pages;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Pages\Models\Page;
use App\Domain\Pages\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_pages_view_permission_can_view_pages_index(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view']);

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/pages');

        $response
            ->assertOk()
            ->assertSee('Pages');
    }

    public function test_admin_without_pages_view_permission_cannot_view_pages_index(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/pages');

        $response->assertForbidden();
    }

    public function test_admin_with_pages_manage_permission_can_create_a_page(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/pages', [
            'title' => 'Historia',
            'slug' => 'historia',
            'excerpt' => 'Resumen institucional',
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'seo_title' => 'Historia del club',
            'seo_description' => 'Conoce la historia del club.',
            'is_home' => false,
        ]);

        $response->assertRedirect('/admin/pages');

        $this->assertDatabaseHas('pages', [
            'title' => 'Historia',
            'slug' => 'historia',
            'status' => 'published',
        ]);
    }

    public function test_admin_with_pages_manage_permission_can_update_a_page(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);
        $page = Page::factory()->create([
            'title' => 'Mision',
            'slug' => 'mision',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($adminUser, 'admin')->put("/admin/pages/{$page->id}", [
            'title' => 'Mision y Vision',
            'slug' => 'mision-y-vision',
            'excerpt' => 'Actualizado',
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'seo_title' => 'Mision y Vision',
            'seo_description' => 'Texto actualizado',
            'is_home' => true,
        ]);

        $response->assertRedirect('/admin/pages');

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Mision y Vision',
            'slug' => 'mision-y-vision',
            'is_home' => 1,
        ]);
    }

    public function test_admin_with_pages_manage_permission_can_create_page_with_sections(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/pages', [
            'title' => 'Academia',
            'slug' => 'academia',
            'excerpt' => 'Formacion',
            'status' => 'draft',
            'published_at' => null,
            'seo_title' => 'Academia',
            'seo_description' => 'Detalle de academia',
            'is_home' => false,
            'sections' => [
                [
                    'section_key' => 'hero',
                    'type' => 'banner',
                    'title' => 'Hero principal',
                    'body' => 'Contenido inicial',
                    'payload' => [
                        'cta_label' => 'Ver mas',
                    ],
                    'sort_order' => 1,
                    'is_active' => true,
                    'image_path' => 'pages/hero.jpg',
                ],
                [
                    'section_key' => 'content',
                    'type' => 'rich_text',
                    'title' => 'Bloque de contenido',
                    'body' => 'Texto secundario',
                    'payload' => [
                        'columns' => 2,
                    ],
                    'sort_order' => 2,
                    'is_active' => false,
                    'image_path' => null,
                ],
            ],
        ]);

        $response->assertRedirect('/admin/pages');

        $page = Page::query()->where('slug', 'academia')->first();

        $this->assertNotNull($page);
        $this->assertCount(2, $page->sections);
        $this->assertSame(1, $page->sections()->first()?->sort_order);
        $this->assertSame(['cta_label' => 'Ver mas'], $page->sections()->first()?->payload);
    }

    public function test_slug_must_be_unique(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);

        Page::factory()->create(['slug' => 'historia']);

        $response = $this->from('/admin/pages/create')
            ->actingAs($adminUser, 'admin')
            ->post('/admin/pages', [
                'title' => 'Otra Historia',
                'slug' => 'historia',
                'status' => 'draft',
            ]);

        $response
            ->assertRedirect('/admin/pages/create')
            ->assertSessionHasErrors('slug');
    }

    public function test_scheduled_status_requires_published_at(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);

        $response = $this->from('/admin/pages/create')
            ->actingAs($adminUser, 'admin')
            ->post('/admin/pages', [
                'title' => 'Agenda',
                'slug' => 'agenda',
                'status' => 'scheduled',
                'published_at' => null,
            ]);

        $response
            ->assertRedirect('/admin/pages/create')
            ->assertSessionHasErrors('published_at');
    }

    public function test_admin_without_pages_manage_permission_cannot_create_or_update_pages(): void
    {
        $adminUser = $this->createAdminWithPermissions(['pages.view']);
        $page = Page::factory()->create();

        $createResponse = $this->actingAs($adminUser, 'admin')->post('/admin/pages', [
            'title' => 'Bloqueada',
            'slug' => 'bloqueada',
            'status' => 'draft',
        ]);

        $createResponse->assertForbidden();

        $updateResponse = $this->actingAs($adminUser, 'admin')->put("/admin/pages/{$page->id}", [
            'title' => 'No permitida',
            'slug' => 'no-permitida',
            'status' => 'draft',
        ]);

        $updateResponse->assertForbidden();
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $adminUser = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'page-role-'.count($permissionNames).'-'.fake()->unique()->slug(),
            'label' => 'Page Role',
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
