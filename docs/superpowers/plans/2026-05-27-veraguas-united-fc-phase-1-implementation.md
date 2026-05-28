# Veraguas United FC Phase 1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a secure Phase 1 modular-monolith foundation in Laravel 13 with separate admin authentication, a Blade backoffice, editable CMS modules, base media and audit support, and a public REST API consumed by React.

**Architecture:** The application remains a single Laravel 13 codebase structured as a modular monolith by domain. Public users and backoffice administrators are fully separated through distinct tables, providers, guards, route files, middleware usage, password brokers, and controllers. Admin CRUD modules write to relational tables and the public React app reads the published content from stable JSON endpoints.

**Tech Stack:** Laravel 13, PHP 8.3+, Blade, React/Inertia (existing public frontend bootstrap), SQLite for local development, PHPUnit 12, Eloquent ORM, Laravel Form Requests, Laravel Policies / custom permission middleware, filesystem storage.

---

## File Structure

### Create

- `app/Domain/AdminUsers/Models/AdminUser.php`
- `app/Domain/AccessControl/Models/Role.php`
- `app/Domain/AccessControl/Models/Permission.php`
- `app/Domain/Settings/Models/SiteSetting.php`
- `app/Domain/Menus/Models/Menu.php`
- `app/Domain/Menus/Models/MenuItem.php`
- `app/Domain/Pages/Models/Page.php`
- `app/Domain/Pages/Models/PageSection.php`
- `app/Domain/News/Models/NewsCategory.php`
- `app/Domain/News/Models/NewsArticle.php`
- `app/Domain/Media/Models/Media.php`
- `app/Domain/Audit/Models/AuditLog.php`
- `app/Domain/Payments/Contracts/PaymentProvider.php`
- `app/Http/Controllers/Admin/Auth/AdminAuthenticatedSessionController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/AdminUserController.php`
- `app/Http/Controllers/Admin/RoleController.php`
- `app/Http/Controllers/Admin/SiteSettingController.php`
- `app/Http/Controllers/Admin/MenuController.php`
- `app/Http/Controllers/Admin/PageController.php`
- `app/Http/Controllers/Admin/PageSectionController.php`
- `app/Http/Controllers/Admin/NewsArticleController.php`
- `app/Http/Controllers/Api/SiteSettingController.php`
- `app/Http/Controllers/Api/MenuController.php`
- `app/Http/Controllers/Api/PageController.php`
- `app/Http/Controllers/Api/NewsController.php`
- `app/Http/Middleware/EnsureAdminHasPermission.php`
- `app/Http/Requests/Admin/Auth/AdminLoginRequest.php`
- `app/Http/Requests/Admin/AdminUser/AdminUserStoreRequest.php`
- `app/Http/Requests/Admin/AdminUser/AdminUserUpdateRequest.php`
- `app/Http/Requests/Admin/Role/RoleStoreRequest.php`
- `app/Http/Requests/Admin/Role/RoleUpdateRequest.php`
- `app/Http/Requests/Admin/Settings/UpdateSiteSettingRequest.php`
- `app/Http/Requests/Admin/Menu/MenuStoreRequest.php`
- `app/Http/Requests/Admin/Menu/MenuUpdateRequest.php`
- `app/Http/Requests/Admin/Menu/MenuItemStoreRequest.php`
- `app/Http/Requests/Admin/Menu/MenuItemUpdateRequest.php`
- `app/Http/Requests/Admin/Page/PageStoreRequest.php`
- `app/Http/Requests/Admin/Page/PageUpdateRequest.php`
- `app/Http/Requests/Admin/Page/PageSectionStoreRequest.php`
- `app/Http/Requests/Admin/Page/PageSectionUpdateRequest.php`
- `app/Http/Requests/Admin/News/NewsArticleStoreRequest.php`
- `app/Http/Requests/Admin/News/NewsArticleUpdateRequest.php`
- `app/Http/Resources/SiteSettingResource.php`
- `app/Http/Resources/MenuResource.php`
- `app/Http/Resources/PageResource.php`
- `app/Http/Resources/NewsArticleResource.php`
- `app/Providers/AdminRouteServiceProvider.php`
- `app/Support/Audit/AuditablePayload.php`
- `app/Support/Audit/RecordsAdminAudit.php`
- `database/factories/AdminUserFactory.php`
- `database/factories/RoleFactory.php`
- `database/factories/PermissionFactory.php`
- `database/factories/SiteSettingFactory.php`
- `database/factories/MenuFactory.php`
- `database/factories/MenuItemFactory.php`
- `database/factories/PageFactory.php`
- `database/factories/PageSectionFactory.php`
- `database/factories/NewsCategoryFactory.php`
- `database/factories/NewsArticleFactory.php`
- `database/factories/MediaFactory.php`
- `database/migrations/2026_05_27_000100_create_admin_users_table.php`
- `database/migrations/2026_05_27_000110_create_roles_table.php`
- `database/migrations/2026_05_27_000120_create_permissions_table.php`
- `database/migrations/2026_05_27_000130_create_admin_user_role_table.php`
- `database/migrations/2026_05_27_000140_create_permission_role_table.php`
- `database/migrations/2026_05_27_000150_create_admin_password_reset_tokens_table.php`
- `database/migrations/2026_05_27_000200_create_site_settings_table.php`
- `database/migrations/2026_05_27_000300_create_menus_table.php`
- `database/migrations/2026_05_27_000310_create_menu_items_table.php`
- `database/migrations/2026_05_27_000400_create_pages_table.php`
- `database/migrations/2026_05_27_000410_create_page_sections_table.php`
- `database/migrations/2026_05_27_000500_create_news_categories_table.php`
- `database/migrations/2026_05_27_000510_create_news_articles_table.php`
- `database/migrations/2026_05_27_000600_create_media_table.php`
- `database/migrations/2026_05_27_000700_create_audit_logs_table.php`
- `database/seeders/AccessControlSeeder.php`
- `database/seeders/AdminUserSeeder.php`
- `database/seeders/SiteSettingSeeder.php`
- `resources/views/layouts/admin/app.blade.php`
- `resources/views/admin/auth/login.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/admin-users/index.blade.php`
- `resources/views/admin/admin-users/create.blade.php`
- `resources/views/admin/admin-users/edit.blade.php`
- `resources/views/admin/roles/index.blade.php`
- `resources/views/admin/roles/create.blade.php`
- `resources/views/admin/roles/edit.blade.php`
- `resources/views/admin/settings/edit.blade.php`
- `resources/views/admin/menus/index.blade.php`
- `resources/views/admin/menus/create.blade.php`
- `resources/views/admin/menus/edit.blade.php`
- `resources/views/admin/pages/index.blade.php`
- `resources/views/admin/pages/create.blade.php`
- `resources/views/admin/pages/edit.blade.php`
- `resources/views/admin/news/index.blade.php`
- `resources/views/admin/news/create.blade.php`
- `resources/views/admin/news/edit.blade.php`
- `routes/admin.php`
- `routes/admin_auth.php`
- `routes/api.php`
- `tests/Feature/Admin/Auth/AdminAuthenticationTest.php`
- `tests/Feature/Admin/Auth/AdminAuthorizationBoundaryTest.php`
- `tests/Feature/Admin/Dashboard/AdminDashboardTest.php`
- `tests/Feature/Admin/AccessControl/AdminPermissionTest.php`
- `tests/Feature/Admin/Settings/SiteSettingCrudTest.php`
- `tests/Feature/Admin/Menus/MenuCrudTest.php`
- `tests/Feature/Admin/Pages/PageCrudTest.php`
- `tests/Feature/Admin/News/NewsArticleCrudTest.php`
- `tests/Feature/Admin/Media/MediaUploadValidationTest.php`
- `tests/Feature/Admin/Audit/AuditLogTest.php`
- `tests/Feature/Api/PublicContentApiTest.php`
- `docs/api/public-content-phase-1.md`

### Modify

- `bootstrap/app.php`
- `config/auth.php`
- `app/Providers/AppServiceProvider.php`
- `app/Models/User.php`
- `database/seeders/DatabaseSeeder.php`
- `routes/web.php`
- `routes/auth.php`
- `resources/views/app.blade.php`
- `phpunit.xml`

### Responsibility Notes

- `app/Domain/*/Models`: domain entities only; no cross-domain HTTP concerns
- `app/Http/Controllers/Admin/*`: admin surface only; never reference `App\Models\User`
- `app/Http/Controllers/Api/*`: public content read-model endpoints only
- `app/Http/Requests/Admin/*`: validation and authorization boundaries for admin inputs
- `app/Support/Audit/*`: central audit payload normalization and reusable recording helpers
- `resources/views/admin/*`: backoffice Blade screens
- `routes/admin*.php`: complete admin surface separation
- `tests/Feature/Admin/*`: auth, authorization, CRUD, audit
- `tests/Feature/Api/*`: stable public API behavior

---

## Proposed Folder Structure

```text
app/
  Domain/
    AccessControl/
      Models/
    AdminUsers/
      Models/
    Audit/
      Models/
    Media/
      Models/
    Menus/
      Models/
    News/
      Models/
    Pages/
      Models/
    Payments/
      Contracts/
    Settings/
      Models/
    Shared/
  Http/
    Controllers/
      Admin/
        Auth/
      Api/
      Auth/
    Middleware/
    Requests/
      Admin/
        AdminUser/
        Auth/
        Menu/
        News/
        Page/
        Role/
        Settings/
    Resources/
  Support/
    Audit/
database/
  factories/
  migrations/
  seeders/
docs/
  api/
  superpowers/
    plans/
    specs/
resources/
  views/
    admin/
      admin-users/
      auth/
      menus/
      news/
      pages/
      roles/
      settings/
    layouts/
      admin/
routes/
  admin.php
  admin_auth.php
  api.php
  auth.php
  web.php
tests/
  Feature/
    Admin/
    Api/
```

---

## Migration List

1. `2026_05_27_000100_create_admin_users_table.php`
   Creates isolated administrative identities with login credentials, remember token, and verification timestamps.
2. `2026_05_27_000110_create_roles_table.php`
   Stores backoffice roles.
3. `2026_05_27_000120_create_permissions_table.php`
   Stores granular backoffice permissions.
4. `2026_05_27_000130_create_admin_user_role_table.php`
   Pivot between admins and roles.
5. `2026_05_27_000140_create_permission_role_table.php`
   Pivot between roles and permissions.
6. `2026_05_27_000150_create_admin_password_reset_tokens_table.php`
   Separate password reset broker persistence for `admin_users`.
7. `2026_05_27_000200_create_site_settings_table.php`
   Singleton-style site configuration.
8. `2026_05_27_000300_create_menus_table.php`
   Named menus, including header and footer.
9. `2026_05_27_000310_create_menu_items_table.php`
   Menu tree items with parent-child nesting.
10. `2026_05_27_000400_create_pages_table.php`
    CMS pages with publication state and SEO.
11. `2026_05_27_000410_create_page_sections_table.php`
    Ordered editable sections for pages.
12. `2026_05_27_000500_create_news_categories_table.php`
    Optional taxonomy for news.
13. `2026_05_27_000510_create_news_articles_table.php`
    News content with publishing flags and SEO.
14. `2026_05_27_000600_create_media_table.php`
    Polymorphic image attachment metadata.
15. `2026_05_27_000700_create_audit_logs_table.php`
    Admin audit trail for content mutations.

---

## Model List

1. `App\Models\User`
   Public user only; remains under guard `web`.
2. `App\Domain\AdminUsers\Models\AdminUser`
   Administrative identity; authenticatable under guard `admin`.
3. `App\Domain\AccessControl\Models\Role`
   Backoffice role aggregate.
4. `App\Domain\AccessControl\Models\Permission`
   Backoffice permission aggregate.
5. `App\Domain\Settings\Models\SiteSetting`
   Singleton site configuration.
6. `App\Domain\Menus\Models\Menu`
   Header/footer menu container.
7. `App\Domain\Menus\Models\MenuItem`
   Ordered nested menu item.
8. `App\Domain\Pages\Models\Page`
   CMS page entity.
9. `App\Domain\Pages\Models\PageSection`
   Ordered editable section linked to a page.
10. `App\Domain\News\Models\NewsCategory`
    Optional category for news articles.
11. `App\Domain\News\Models\NewsArticle`
    News post entity.
12. `App\Domain\Media\Models\Media`
    Image metadata and polymorphic attachment holder.
13. `App\Domain\Audit\Models\AuditLog`
    Administrative audit trail entry.

---

## API Route List

1. `GET /api/site-settings`
   Returns site branding, contact, social, SEO, maintenance flag, and public media URLs.
2. `GET /api/menu/header`
   Returns ordered header menu tree.
3. `GET /api/menu/footer`
   Returns ordered footer menu tree.
4. `GET /api/pages/{slug}`
   Returns a published page by slug with ordered published sections.
5. `GET /api/news`
   Returns paginated or ordered published news listing.
6. `GET /api/news/{slug}`
   Returns a published news article by slug.

### Internal Admin Web Route Groups

1. `/admin/login`
2. `/admin/logout`
3. `/admin`
4. `/admin/admin-users/*`
5. `/admin/roles/*`
6. `/admin/settings`
7. `/admin/menus/*`
8. `/admin/pages/*`
9. `/admin/pages/{page}/sections/*`
10. `/admin/news/*`

---

## Blade Screen List

1. `Admin Login`
   Route: `/admin/login`
   Purpose: isolated login for administrative users.
2. `Admin Dashboard`
   Route: `/admin`
   Purpose: initial landing page after admin login.
3. `Admin Users Index`
   Route: `/admin/admin-users`
   Purpose: list administrators and assigned roles.
4. `Admin Users Create`
   Route: `/admin/admin-users/create`
   Purpose: create new admin accounts.
5. `Admin Users Edit`
   Route: `/admin/admin-users/{adminUser}/edit`
   Purpose: update basic account data and roles.
6. `Roles Index`
   Route: `/admin/roles`
   Purpose: list roles and permission counts.
7. `Roles Create`
   Route: `/admin/roles/create`
   Purpose: create roles and attach permissions.
8. `Roles Edit`
   Route: `/admin/roles/{role}/edit`
   Purpose: maintain role permissions.
9. `Site Settings Edit`
   Route: `/admin/settings`
   Purpose: singleton CMS form for site-wide settings.
10. `Menus Index`
    Route: `/admin/menus`
    Purpose: list header/footer menus.
11. `Menus Create`
    Route: `/admin/menus/create`
    Purpose: create menu containers when needed.
12. `Menus Edit`
    Route: `/admin/menus/{menu}/edit`
    Purpose: edit menu metadata and items.
13. `Pages Index`
    Route: `/admin/pages`
    Purpose: list and filter pages.
14. `Pages Create`
    Route: `/admin/pages/create`
    Purpose: create pages and sections.
15. `Pages Edit`
    Route: `/admin/pages/{page}/edit`
    Purpose: update page content and sections.
16. `News Index`
    Route: `/admin/news`
    Purpose: list news articles.
17. `News Create`
    Route: `/admin/news/create`
    Purpose: create news article.
18. `News Edit`
    Route: `/admin/news/{newsArticle}/edit`
    Purpose: update news article.

---

## Test List

1. `AdminAuthenticationTest`
   Covers:
   - admin login form is accessible at `/admin/login`
   - admin can log in with `admin_users`
   - public `users` credentials cannot log into admin guard
   - admin logout invalidates admin session
2. `AdminAuthorizationBoundaryTest`
   Covers:
   - guest redirected from `/admin`
   - authenticated public user cannot access `/admin`
   - admin-only middleware protects all admin routes
3. `AdminDashboardTest`
   Covers:
   - authenticated admin sees dashboard
4. `AdminPermissionTest`
   Covers:
   - admin without permission gets forbidden/redirected from protected module
   - superadmin role bypass or full permission coverage works
5. `SiteSettingCrudTest`
   Covers:
   - authorized admin updates settings
   - validation rejects invalid logo upload, invalid email, oversized images
   - audit entry recorded
6. `MenuCrudTest`
   Covers:
   - authorized admin creates menu items
   - authorized admin updates sort order and active state
   - public API returns correct nested order
   - audit entry recorded
7. `PageCrudTest`
   Covers:
   - authorized admin creates page
   - sections can be created and ordered
   - unpublished page not exposed in public API
   - audit entry recorded
8. `NewsArticleCrudTest`
   Covers:
   - authorized admin creates news article
   - publish states respected
   - slug uniqueness validation works
   - audit entry recorded
9. `MediaUploadValidationTest`
   Covers:
   - only approved image MIME types accepted
   - oversized uploads rejected
   - media metadata persisted
10. `AuditLogTest`
    Covers:
    - update/create actions on settings, menus, pages, news write audit records with actor and IP
11. `PublicContentApiTest`
    Covers:
    - site settings endpoint returns expected fields
    - header/footer endpoints return active ordered items
    - page endpoint returns published page and active sections
    - news list/detail endpoints return only published articles

---

## Implementation Phases

### Phase A: Project Wiring and Admin Auth Boundary

**Files:**
- Modify: `bootstrap/app.php`
- Modify: `config/auth.php`
- Modify: `routes/web.php`
- Modify: `routes/auth.php`
- Create: `routes/admin.php`
- Create: `routes/admin_auth.php`
- Create: `app/Domain/AdminUsers/Models/AdminUser.php`
- Create: `app/Http/Controllers/Admin/Auth/AdminAuthenticatedSessionController.php`
- Create: `app/Http/Controllers/Admin/DashboardController.php`
- Create: `app/Http/Requests/Admin/Auth/AdminLoginRequest.php`
- Create: `database/migrations/2026_05_27_000100_create_admin_users_table.php`
- Create: `database/migrations/2026_05_27_000150_create_admin_password_reset_tokens_table.php`
- Create: `database/factories/AdminUserFactory.php`
- Create: `resources/views/layouts/admin/app.blade.php`
- Create: `resources/views/admin/auth/login.blade.php`
- Create: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/Admin/Auth/AdminAuthenticationTest.php`
- Test: `tests/Feature/Admin/Auth/AdminAuthorizationBoundaryTest.php`
- Test: `tests/Feature/Admin/Dashboard/AdminDashboardTest.php`

- [ ] **Step 1: Write the failing admin auth boundary tests**

```php
<?php

use App\Domain\AdminUsers\Models\AdminUser;
use App\Models\User;

it('redirects guests from admin dashboard', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('does not allow a public user into the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->get('/admin')
        ->assertForbidden();
});

it('allows an admin user to access the admin dashboard', function () {
    $admin = AdminUser::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get('/admin')
        ->assertOk();
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/Auth/AdminAuthenticationTest.php tests/Feature/Admin/Auth/AdminAuthorizationBoundaryTest.php tests/Feature/Admin/Dashboard/AdminDashboardTest.php`
Expected: FAIL because admin guard, routes, model, and views do not exist yet.

- [ ] **Step 3: Add admin auth config and minimal implementation**

Implement:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admin_users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'admin_users' => [
        'driver' => 'eloquent',
        'model' => App\Domain\AdminUsers\Models\AdminUser::class,
    ],
],

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
    'admin_users' => [
        'provider' => 'admin_users',
        'table' => 'admin_password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

and route mounting:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

and in `routes/web.php`:

```php
require __DIR__.'/auth.php';
require __DIR__.'/admin_auth.php';
require __DIR__.'/admin.php';
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/Auth/AdminAuthenticationTest.php tests/Feature/Admin/Auth/AdminAuthorizationBoundaryTest.php tests/Feature/Admin/Dashboard/AdminDashboardTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

Record:
- files created/modified
- tests added
- commands run
- open auth-related follow-ups

### Phase B: Access Control and Admin User Management

**Files:**
- Create: `app/Domain/AccessControl/Models/Role.php`
- Create: `app/Domain/AccessControl/Models/Permission.php`
- Create: `app/Http/Middleware/EnsureAdminHasPermission.php`
- Create: `app/Http/Controllers/Admin/AdminUserController.php`
- Create: `app/Http/Controllers/Admin/RoleController.php`
- Create: `database/migrations/2026_05_27_000110_create_roles_table.php`
- Create: `database/migrations/2026_05_27_000120_create_permissions_table.php`
- Create: `database/migrations/2026_05_27_000130_create_admin_user_role_table.php`
- Create: `database/migrations/2026_05_27_000140_create_permission_role_table.php`
- Create: `database/seeders/AccessControlSeeder.php`
- Create: `database/seeders/AdminUserSeeder.php`
- Create: `tests/Feature/Admin/AccessControl/AdminPermissionTest.php`

- [ ] **Step 1: Write the failing permission tests**

```php
<?php

use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;

it('forbids admins without the required permission', function () {
    $admin = AdminUser::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get('/admin/settings')
        ->assertForbidden();
});

it('allows admins with the required permission', function () {
    $permission = Permission::factory()->create(['name' => 'settings.view']);
    $role = Role::factory()->create();
    $role->permissions()->attach($permission);

    $admin = AdminUser::factory()->create();
    $admin->roles()->attach($role);

    $this->actingAs($admin, 'admin')
        ->get('/admin/settings')
        ->assertOk();
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/AccessControl/AdminPermissionTest.php`
Expected: FAIL because permission middleware and role models do not exist.

- [ ] **Step 3: Implement minimal RBAC**

Implement:

```php
public function hasPermission(string $permission): bool
{
    return $this->roles()
        ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
        ->exists();
}
```

and middleware:

```php
if (! $request->user('admin') || ! $request->user('admin')->hasPermission($permission)) {
    abort(403);
}
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/AccessControl/AdminPermissionTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase C: Site Settings CMS

**Files:**
- Create: `app/Domain/Settings/Models/SiteSetting.php`
- Create: `app/Http/Controllers/Admin/SiteSettingController.php`
- Create: `app/Http/Requests/Admin/Settings/UpdateSiteSettingRequest.php`
- Create: `database/migrations/2026_05_27_000200_create_site_settings_table.php`
- Create: `database/factories/SiteSettingFactory.php`
- Create: `database/seeders/SiteSettingSeeder.php`
- Create: `tests/Feature/Admin/Settings/SiteSettingCrudTest.php`

- [ ] **Step 1: Write the failing settings CRUD tests**

```php
<?php

use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Settings\Models\SiteSetting;

it('lets an authorized admin update site settings', function () {
    $admin = adminWithPermission('settings.update');
    $settings = SiteSetting::factory()->create();

    $response = $this->actingAs($admin, 'admin')->put('/admin/settings', [
        'site_name' => 'Veraguas United FC',
        'contact_email' => 'hola@veraguasunited.test',
    ]);

    $response->assertRedirect();

    expect($settings->fresh()->site_name)->toBe('Veraguas United FC');
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/Settings/SiteSettingCrudTest.php`
Expected: FAIL because settings model, route, and request do not exist.

- [ ] **Step 3: Implement singleton settings CRUD**

Implement:

```php
$settings->update($request->validated());
```

with a singleton fetch pattern:

```php
$settings = SiteSetting::query()->firstOrCreate([], [
    'site_name' => 'Veraguas United FC',
]);
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/Settings/SiteSettingCrudTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase D: Menus

**Files:**
- Create: `app/Domain/Menus/Models/Menu.php`
- Create: `app/Domain/Menus/Models/MenuItem.php`
- Create: `app/Http/Controllers/Admin/MenuController.php`
- Create: `app/Http/Requests/Admin/Menu/*`
- Create: `database/migrations/2026_05_27_000300_create_menus_table.php`
- Create: `database/migrations/2026_05_27_000310_create_menu_items_table.php`
- Create: `tests/Feature/Admin/Menus/MenuCrudTest.php`

- [ ] **Step 1: Write the failing menu CRUD tests**

```php
<?php

use App\Domain\Menus\Models\Menu;

it('lets an authorized admin create a header menu item', function () {
    $admin = adminWithPermission('menus.manage');
    $menu = Menu::factory()->create(['location' => 'header']);

    $response = $this->actingAs($admin, 'admin')->post("/admin/menus/{$menu->id}/items", [
        'label' => 'Noticias',
        'url' => '/noticias',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response->assertRedirect();
    expect($menu->items()->count())->toBe(1);
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/Menus/MenuCrudTest.php`
Expected: FAIL

- [ ] **Step 3: Implement minimal menu and item CRUD**

Implement ordered relationship:

```php
public function items(): HasMany
{
    return $this->hasMany(MenuItem::class)->orderBy('sort_order');
}
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/Menus/MenuCrudTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase E: Pages and Editable Sections

**Files:**
- Create: `app/Domain/Pages/Models/Page.php`
- Create: `app/Domain/Pages/Models/PageSection.php`
- Create: `app/Http/Controllers/Admin/PageController.php`
- Create: `app/Http/Controllers/Admin/PageSectionController.php`
- Create: `app/Http/Requests/Admin/Page/*`
- Create: `database/migrations/2026_05_27_000400_create_pages_table.php`
- Create: `database/migrations/2026_05_27_000410_create_page_sections_table.php`
- Create: `tests/Feature/Admin/Pages/PageCrudTest.php`

- [ ] **Step 1: Write the failing page CRUD tests**

```php
<?php

it('lets an authorized admin create a page with sections', function () {
    $admin = adminWithPermission('pages.manage');

    $response = $this->actingAs($admin, 'admin')->post('/admin/pages', [
        'title' => 'Historia',
        'slug' => 'historia',
        'status' => 'published',
        'sections' => [
            [
                'section_key' => 'hero',
                'title' => 'Nuestra Historia',
                'body' => 'Contenido inicial',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('pages', ['slug' => 'historia']);
    $this->assertDatabaseHas('page_sections', ['section_key' => 'hero']);
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/Pages/PageCrudTest.php`
Expected: FAIL

- [ ] **Step 3: Implement minimal page aggregate persistence**

Implement:

```php
$page = Page::create(Arr::except($validated, 'sections'));

foreach ($validated['sections'] ?? [] as $section) {
    $page->sections()->create($section);
}
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/Pages/PageCrudTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase F: News

**Files:**
- Create: `app/Domain/News/Models/NewsCategory.php`
- Create: `app/Domain/News/Models/NewsArticle.php`
- Create: `app/Http/Controllers/Admin/NewsArticleController.php`
- Create: `app/Http/Requests/Admin/News/*`
- Create: `database/migrations/2026_05_27_000500_create_news_categories_table.php`
- Create: `database/migrations/2026_05_27_000510_create_news_articles_table.php`
- Create: `tests/Feature/Admin/News/NewsArticleCrudTest.php`

- [ ] **Step 1: Write the failing news CRUD tests**

```php
<?php

it('lets an authorized admin create a published news article', function () {
    $admin = adminWithPermission('news.manage');

    $response = $this->actingAs($admin, 'admin')->post('/admin/news', [
        'title' => 'Victoria en casa',
        'slug' => 'victoria-en-casa',
        'summary' => 'Resumen breve',
        'body' => 'Contenido completo',
        'status' => 'published',
        'published_at' => now()->toDateTimeString(),
        'is_featured' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('news_articles', ['slug' => 'victoria-en-casa']);
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/News/NewsArticleCrudTest.php`
Expected: FAIL

- [ ] **Step 3: Implement minimal news CRUD**

Implement:

```php
NewsArticle::create($request->validated());
```

and publication scope:

```php
public function scopePublished(Builder $query): Builder
{
    return $query
        ->where('status', 'published')
        ->where(function (Builder $builder) {
            $builder->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
}
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/News/NewsArticleCrudTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase G: Media

**Files:**
- Create: `app/Domain/Media/Models/Media.php`
- Create: `database/migrations/2026_05_27_000600_create_media_table.php`
- Create: `database/factories/MediaFactory.php`
- Create: `tests/Feature/Admin/Media/MediaUploadValidationTest.php`
- Modify: admin requests that accept images

- [ ] **Step 1: Write the failing media validation tests**

```php
<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('rejects oversized settings logo uploads', function () {
    Storage::fake('public');
    $admin = adminWithPermission('settings.update');

    $response = $this->actingAs($admin, 'admin')->put('/admin/settings', [
        'site_name' => 'Veraguas United FC',
        'logo' => UploadedFile::fake()->image('logo.jpg')->size(6000),
    ]);

    $response->assertSessionHasErrors('logo');
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/Media/MediaUploadValidationTest.php`
Expected: FAIL

- [ ] **Step 3: Implement minimal media persistence**

Implement request rules similar to:

```php
'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
```

and metadata persistence:

```php
$path = $file->store('cms', 'public');

$media = Media::create([
    'disk' => 'public',
    'path' => $path,
    'original_name' => $file->getClientOriginalName(),
    'mime_type' => $file->getMimeType(),
    'size' => $file->getSize(),
    'alt_text' => $request->input('logo_alt_text'),
]);
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/Media/MediaUploadValidationTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase H: Audit Logging

**Files:**
- Create: `app/Domain/Audit/Models/AuditLog.php`
- Create: `app/Support/Audit/AuditablePayload.php`
- Create: `app/Support/Audit/RecordsAdminAudit.php`
- Create: `database/migrations/2026_05_27_000700_create_audit_logs_table.php`
- Create: `tests/Feature/Admin/Audit/AuditLogTest.php`
- Modify: settings, menu, page, news controllers

- [ ] **Step 1: Write the failing audit tests**

```php
<?php

it('records an audit log when settings are updated', function () {
    $admin = adminWithPermission('settings.update');

    $this->actingAs($admin, 'admin')->put('/admin/settings', [
        'site_name' => 'Nuevo nombre',
        'contact_email' => 'hola@veraguasunited.test',
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'admin_user_id' => $admin->id,
        'module' => 'settings',
        'action' => 'updated',
    ]);
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Admin/Audit/AuditLogTest.php`
Expected: FAIL

- [ ] **Step 3: Implement centralized audit recording**

Implement a reusable helper similar to:

```php
AuditLog::create([
    'admin_user_id' => $request->user('admin')->id,
    'module' => 'settings',
    'action' => 'updated',
    'auditable_type' => $model::class,
    'auditable_id' => $model->getKey(),
    'old_values' => $before,
    'new_values' => $after,
    'ip_address' => $request->ip(),
]);
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Admin/Audit/AuditLogTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase I: Public API and Resources

**Files:**
- Create: `routes/api.php`
- Create: `app/Http/Controllers/Api/*`
- Create: `app/Http/Resources/*`
- Create: `tests/Feature/Api/PublicContentApiTest.php`
- Create: `docs/api/public-content-phase-1.md`

- [ ] **Step 1: Write the failing public API tests**

```php
<?php

it('returns the published page by slug', function () {
    $page = Page::factory()->published()->create(['slug' => 'historia']);
    $page->sections()->create([
        'section_key' => 'hero',
        'title' => 'Nuestra Historia',
        'body' => 'Contenido',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/pages/historia');

    $response->assertOk()
        ->assertJsonPath('data.slug', 'historia')
        ->assertJsonCount(1, 'data.sections');
});
```

- [ ] **Step 2: Run the targeted tests to verify RED**

Run: `php artisan test tests/Feature/Api/PublicContentApiTest.php`
Expected: FAIL

- [ ] **Step 3: Implement read-only public API resources**

Implement controllers with published scopes only, for example:

```php
$page = Page::published()
    ->where('slug', $slug)
    ->with(['sections' => fn ($query) => $query->active()->orderBy('sort_order')])
    ->firstOrFail();

return new PageResource($page);
```

- [ ] **Step 4: Run the targeted tests to verify GREEN**

Run: `php artisan test tests/Feature/Api/PublicContentApiTest.php`
Expected: PASS

- [ ] **Step 5: Document the completed block**

Update:
- `docs/implementation-log.md`
- `docs/phase-1-progress.md`

### Phase J: Full Verification and Documentation

**Files:**
- Modify: `docs/implementation-log.md`
- Modify: `docs/phase-1-progress.md`
- Create: `docs/api/public-content-phase-1.md`
- Review: all tests and route files

- [ ] **Step 1: Run the full Phase 1 test suite**

Run: `php artisan test`
Expected: PASS with zero failures.

- [ ] **Step 2: Run a focused migration refresh check**

Run: `php artisan migrate:fresh --seed`
Expected: PASS and seed the default superadmin plus minimum CMS bootstrap data.

- [ ] **Step 3: Run a public route smoke check**

Run: `php artisan route:list`
Expected: shows separate public auth, admin auth, admin routes, and public API routes.

- [ ] **Step 4: Finalize documentation**

Write:
- endpoint request/response notes in `docs/api/public-content-phase-1.md`
- phase summary in `docs/phase-1-progress.md`
- final completed blocks and commands in `docs/implementation-log.md`

- [ ] **Step 5: Verify requirements against the approved spec**

Checklist:
- separate `users` and `admin_users`
- separate `web` and `admin`
- separate admin password broker
- `/admin/*` protected with `auth:admin`
- no admin controller uses `App\Models\User`
- Blade backoffice works
- settings, menus, pages, sections, news are editable
- media works for supported modules
- audit logging records admin actions
- public API returns dynamic content for React

---

## Commands to Run During Implementation

1. `php artisan test tests/Feature/Admin/Auth/AdminAuthenticationTest.php tests/Feature/Admin/Auth/AdminAuthorizationBoundaryTest.php tests/Feature/Admin/Dashboard/AdminDashboardTest.php`
2. `php artisan test tests/Feature/Admin/AccessControl/AdminPermissionTest.php`
3. `php artisan test tests/Feature/Admin/Settings/SiteSettingCrudTest.php`
4. `php artisan test tests/Feature/Admin/Menus/MenuCrudTest.php`
5. `php artisan test tests/Feature/Admin/Pages/PageCrudTest.php`
6. `php artisan test tests/Feature/Admin/News/NewsArticleCrudTest.php`
7. `php artisan test tests/Feature/Admin/Media/MediaUploadValidationTest.php`
8. `php artisan test tests/Feature/Admin/Audit/AuditLogTest.php`
9. `php artisan test tests/Feature/Api/PublicContentApiTest.php`
10. `php artisan test`
11. `php artisan migrate:fresh --seed`
12. `php artisan route:list`

---

## Technical Decisions Locked In

1. Public users remain on `App\Models\User`; admins use `App\Domain\AdminUsers\Models\AdminUser`.
2. Admin and public auth remain fully separate in routes, controllers, providers, guards, and password brokers.
3. Backoffice UI uses Blade only in Phase 1.
4. Media starts as a focused image-centric table with polymorphic attachment support.
5. Audit logging is centralized and reusable rather than controller-specific ad hoc inserts.
6. Future payments are documented only through the reserved `PaymentProvider` contract path; no payment implementation is allowed in this phase.

---

## Local Workspace Logging Rules

Because the workspace has no Git checkpoints, every major completed block must update:

1. `docs/implementation-log.md`
2. `docs/phase-1-progress.md`

Each update must include:

1. Timestamp or phase label
2. Files created or modified
3. Tests added or updated
4. Commands run
5. Verification results
6. Pending items or technical decisions
