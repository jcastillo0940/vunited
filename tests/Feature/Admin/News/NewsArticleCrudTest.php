<?php

namespace Tests\Feature\Admin\News;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\News\Models\NewsArticle;
use App\Domain\News\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_news_view_permission_can_view_news_index(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view']);

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/news');

        $response
            ->assertOk()
            ->assertSee('News');
    }

    public function test_admin_without_news_view_permission_cannot_view_news_index(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/news');

        $response->assertForbidden();
    }

    public function test_admin_with_news_manage_permission_can_create_news_article(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);
        $category = NewsCategory::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/news', [
            'news_category_id' => $category->id,
            'title' => 'Victoria en casa',
            'slug' => 'victoria-en-casa',
            'summary' => 'Resumen breve',
            'body' => 'Contenido completo de la noticia.',
            'featured_image_path' => 'news/victoria.jpg',
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'is_featured' => true,
            'seo_title' => 'Victoria en casa',
            'seo_description' => 'Detalle de la victoria',
        ]);

        $response->assertRedirect('/admin/news');

        $this->assertDatabaseHas('news_articles', [
            'title' => 'Victoria en casa',
            'slug' => 'victoria-en-casa',
            'news_category_id' => $category->id,
            'is_featured' => 1,
        ]);
    }

    public function test_admin_with_news_manage_permission_can_update_news_article(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);
        $article = NewsArticle::factory()->create([
            'title' => 'Previa',
            'slug' => 'previa',
            'status' => 'draft',
            'is_featured' => false,
        ]);

        $response = $this->actingAs($adminUser, 'admin')->put("/admin/news/{$article->id}", [
            'news_category_id' => null,
            'title' => 'Previa Oficial',
            'slug' => 'previa-oficial',
            'summary' => 'Texto actualizado',
            'body' => 'Cuerpo actualizado',
            'featured_image_path' => 'news/previa.jpg',
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'is_featured' => true,
            'seo_title' => 'Previa Oficial',
            'seo_description' => 'Descripcion actualizada',
        ]);

        $response->assertRedirect('/admin/news');

        $this->assertDatabaseHas('news_articles', [
            'id' => $article->id,
            'title' => 'Previa Oficial',
            'slug' => 'previa-oficial',
            'is_featured' => 1,
        ]);
    }

    public function test_admin_with_news_manage_permission_can_delete_news_article(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);
        $article = NewsArticle::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->delete("/admin/news/{$article->id}");

        $response->assertRedirect('/admin/news');
        $this->assertDatabaseMissing('news_articles', ['id' => $article->id]);
    }

    public function test_slug_must_be_unique_for_news_articles(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);

        NewsArticle::factory()->create(['slug' => 'clasico']);

        $response = $this->from('/admin/news/create')
            ->actingAs($adminUser, 'admin')
            ->post('/admin/news', [
                'title' => 'Otro clasico',
                'slug' => 'clasico',
                'body' => 'Texto',
                'status' => 'draft',
                'is_featured' => false,
            ]);

        $response
            ->assertRedirect('/admin/news/create')
            ->assertSessionHasErrors('slug');
    }

    public function test_scheduled_status_requires_published_at(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);

        $response = $this->from('/admin/news/create')
            ->actingAs($adminUser, 'admin')
            ->post('/admin/news', [
                'title' => 'Agenda de partido',
                'slug' => 'agenda-de-partido',
                'body' => 'Texto',
                'status' => 'scheduled',
                'published_at' => null,
                'is_featured' => false,
            ]);

        $response
            ->assertRedirect('/admin/news/create')
            ->assertSessionHasErrors('published_at');
    }

    public function test_admin_without_news_manage_permission_cannot_create_or_edit_news(): void
    {
        $adminUser = $this->createAdminWithPermissions(['news.view']);
        $article = NewsArticle::factory()->create();

        $createResponse = $this->actingAs($adminUser, 'admin')->post('/admin/news', [
            'title' => 'Bloqueada',
            'slug' => 'bloqueada',
            'body' => 'Texto',
            'status' => 'draft',
            'is_featured' => false,
        ]);

        $createResponse->assertForbidden();

        $updateResponse = $this->actingAs($adminUser, 'admin')->put("/admin/news/{$article->id}", [
            'title' => 'No permitida',
            'slug' => 'no-permitida',
            'body' => 'Texto',
            'status' => 'draft',
            'is_featured' => false,
        ]);

        $updateResponse->assertForbidden();
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $adminUser = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'news-role-'.count($permissionNames).'-'.fake()->unique()->slug(),
            'label' => 'News Role',
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
