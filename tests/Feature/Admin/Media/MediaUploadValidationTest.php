<?php

namespace Tests\Feature\Admin\Media;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Media\Models\Media;
use App\Domain\News\Models\NewsArticle;
use App\Domain\Pages\Models\Page;
use App\Domain\Pages\Models\PageSection;
use App\Domain\Settings\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_image_larger_than_5120_kb(): void
    {
        Storage::fake('public');
        $adminUser = $this->createAdminWithPermissions(['settings.view', 'settings.update']);

        $response = $this->from('/admin/settings')
            ->actingAs($adminUser, 'admin')
            ->put('/admin/settings', [
                'site_name' => 'Veraguas United FC',
                'primary_logo' => UploadedFile::fake()->image('large.jpg')->size(5121),
            ]);

        $response
            ->assertRedirect('/admin/settings')
            ->assertSessionHasErrors('primary_logo');
    }

    public function test_rejects_non_image_upload(): void
    {
        Storage::fake('public');
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);

        $response = $this->from('/admin/news/create')
            ->actingAs($adminUser, 'admin')
            ->post('/admin/news', [
                'title' => 'Documento invalido',
                'slug' => 'documento-invalido',
                'body' => 'Contenido',
                'status' => 'draft',
                'is_featured' => false,
                'featured_image' => UploadedFile::fake()->create('brochure.pdf', 200, 'application/pdf'),
            ]);

        $response
            ->assertRedirect('/admin/news/create')
            ->assertSessionHasErrors('featured_image');
    }

    public function test_accepts_valid_image_uploads_and_creates_media_for_settings(): void
    {
        Storage::fake('public');
        $adminUser = $this->createAdminWithPermissions(['settings.view', 'settings.update']);

        $response = $this->actingAs($adminUser, 'admin')->put('/admin/settings', [
            'site_name' => 'Veraguas United FC',
            'primary_logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertRedirect('/admin/settings');

        $settings = SiteSetting::query()->first();
        $media = Media::query()->where('collection', 'primary_logo')->first();

        $this->assertNotNull($settings);
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
        $this->assertSame('public', $media->disk);
        $this->assertSame(SiteSetting::class, $media->mediable_type);
        $this->assertSame($settings->id, $media->mediable_id);
        $this->assertSame($media->path, $settings->primary_logo_path);
        $this->assertNotNull($media->original_name);
        $this->assertNotNull($media->mime_type);
        $this->assertGreaterThan(0, $media->size);
    }

    public function test_accepts_valid_image_uploads_and_creates_media_for_news(): void
    {
        Storage::fake('public');
        $adminUser = $this->createAdminWithPermissions(['news.view', 'news.manage']);

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/news', [
            'title' => 'Nueva portada',
            'slug' => 'nueva-portada',
            'body' => 'Contenido',
            'status' => 'draft',
            'is_featured' => true,
            'featured_image' => UploadedFile::fake()->image('featured.webp'),
        ]);

        $response->assertRedirect('/admin/news');

        $article = NewsArticle::query()->where('slug', 'nueva-portada')->first();
        $media = Media::query()->where('collection', 'featured_image')->first();

        $this->assertNotNull($article);
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
        $this->assertSame(NewsArticle::class, $media->mediable_type);
        $this->assertSame($article->id, $media->mediable_id);
        $this->assertSame($media->path, $article->featured_image_path);
    }

    public function test_accepts_valid_image_uploads_and_creates_media_for_page_and_section(): void
    {
        Storage::fake('public');
        $adminUser = $this->createAdminWithPermissions(['pages.view', 'pages.manage']);

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/pages', [
            'title' => 'Academia Visual',
            'slug' => 'academia-visual',
            'status' => 'draft',
            'page_image' => UploadedFile::fake()->image('page.jpg'),
            'sections' => [
                [
                    'section_key' => 'hero',
                    'type' => 'banner',
                    'title' => 'Hero',
                    'body' => 'Texto',
                    'payload' => ['cta' => 'Ver'],
                    'sort_order' => 1,
                    'is_active' => true,
                    'image' => UploadedFile::fake()->image('section.png'),
                ],
            ],
        ]);

        $response->assertRedirect('/admin/pages');

        $page = Page::query()->where('slug', 'academia-visual')->first();
        $section = PageSection::query()->where('section_key', 'hero')->first();
        $pageMedia = Media::query()->where('collection', 'page_image')->first();
        $sectionMedia = Media::query()->where('collection', 'section_image')->first();

        $this->assertNotNull($page);
        $this->assertNotNull($section);
        $this->assertNotNull($pageMedia);
        $this->assertNotNull($sectionMedia);
        $this->assertSame(Page::class, $pageMedia->mediable_type);
        $this->assertSame($page->id, $pageMedia->mediable_id);
        $this->assertSame(PageSection::class, $sectionMedia->mediable_type);
        $this->assertSame($section->id, $sectionMedia->mediable_id);
        $this->assertSame($sectionMedia->path, $section->image_path);
        Storage::disk('public')->assertExists($pageMedia->path);
        Storage::disk('public')->assertExists($sectionMedia->path);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $adminUser = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'media-role-'.count($permissionNames).'-'.fake()->unique()->slug(),
            'label' => 'Media Role',
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
