# Veraguas United FC Phase 1 Design Spec

**Date:** 2026-05-27
**Project:** Veraguas United FC
**Phase:** Phase 1 - Secure Foundation + Demonstrable Vertical
**Architecture Style:** Modular monolith by domain on Laravel 13
**Status:** Approved design documented for implementation planning

---

## 1. Objective

Build Phase 1 of the Veraguas United FC platform as a secure, editable CMS-backed website foundation with a demonstrable end-to-end vertical:

`separate admin login -> admin edits CMS content in Blade -> public React app consumes REST API -> content renders dynamically from database`

This phase must prove the architectural direction, enforce authentication separation between public and administrative users, and leave the codebase ready for future modules without implementing monetization, ticketing, or logistics yet.

---

## 2. Scope

### In Scope

Phase 1 includes:

1. Separate authentication for public users and backoffice administrators
2. Administrative Blade backoffice base
3. Base role/permission system for backoffice
4. General site settings CMS
5. Header and footer menu management
6. CMS pages with editable sections
7. News management
8. Basic media upload and attachment
9. Base audit logging for admin actions
10. Public REST API for React consumption
11. Automated tests for security, CRUD, and public API behavior
12. Minimal API documentation

### Explicitly Out of Scope

The following are intentionally excluded from Phase 1 implementation:

1. Store / e-commerce
2. Ticketing
3. Memberships / FanClub
4. Buses / Expeditions
5. FanFest
6. Real payment processing
7. Payment gateway integrations

### Future-Ready but Not Implemented

Phase 1 must still define conventions for:

1. Modular namespaces for future domains
2. Folder conventions for domain growth
3. Module naming conventions
4. A future `PaymentProvider` abstraction interface, documented only

---

## 3. Non-Negotiable Security Requirements

### Identity Separation

Public and administrative authentication must remain completely isolated.

1. `users` remains the table for public users only
2. `admin_users` is the table for backoffice accounts only
3. `web` is the guard for public users
4. `admin` is the guard for administrative users
5. Admin routes are mounted under `/admin/*`
6. All `/admin/*` routes are protected with `auth:admin`
7. Public users must never access the backoffice
8. Admin controllers must not use `App\Models\User`
9. Admin password reset broker must be separate from the public broker
10. Administrative actions that modify content must be auditable

### Session and Access Rules

1. Public login lives at `/login`
2. Public account area lives at `/mi-cuenta`
3. Admin login lives at `/admin/login`
4. Admin dashboard lives at `/admin`
5. Public middleware remains `auth:web`
6. Admin middleware is `auth:admin`
7. Guest middleware must also be separated where needed

### Media Security

1. Uploaded files must be validated by type and size
2. Sensitive/private file handling must be designed to avoid direct uncontrolled exposure
3. Phase 1 only requires image upload for CMS usage, but media storage must not assume all future uploads are public

---

## 4. Architectural Direction

### Recommended Architecture

The application will be built as a modular monolith on Laravel 13.

This means:

1. One Laravel application
2. Strong separation by domain inside the codebase
3. Distinct admin and public surfaces
4. Shared infrastructure only where appropriate
5. Explicit conventions to support future domain expansion without premature package extraction

### Domain Boundaries for Phase 1

Phase 1 establishes these initial domains:

1. `AdminAuth`
2. `AdminUsers`
3. `AccessControl`
4. `Settings`
5. `Menus`
6. `Pages`
7. `News`
8. `Media`
9. `Audit`
10. `PublicApi`

### Future Domains Reserved

Reserved future domains include:

1. `Commerce`
2. `Ticketing`
3. `Memberships`
4. `Events`
5. `Transport`
6. `Payments`

These names should guide folder naming, route prefixes, and service boundaries, but no production implementation for those domains is part of Phase 1.

---

## 5. Surface Areas

### Administrative Surface

The backoffice will use Blade and server-rendered Laravel routes for:

1. Admin login/logout
2. Dashboard
3. Admin user management
4. Role/permission management
5. Site settings management
6. Menu management
7. Page and page section management
8. News management
9. Media attachment workflows

### Public Surface

The public website frontend will be React-driven and consume data through REST endpoints. Phase 1 does not require pixel-perfect implementation of the public UI, only stable data delivery for the frontend agent to consume later.

### API Surface

Public API endpoints are read-only in this phase and are intended for the React frontend only.

---

## 6. Functional Requirements

### 6.1 Separate Admin Authentication

Implement:

1. `admin_users` table and model
2. `admin` guard
3. `admin_users` provider
4. Separate admin password broker
5. `/admin/login`
6. `/admin/logout`
7. `/admin` dashboard
8. `auth:admin` middleware usage across admin routes

Acceptance intent:

1. Public users cannot access `/admin`
2. Admins cannot authenticate through public user login by accident
3. Backoffice authentication is isolated from public user identity storage

### 6.2 Backoffice Blade Base

Implement:

1. Admin layout
2. Sidebar navigation
3. Header with current admin context
4. Dashboard landing page
5. Admin logout flow
6. Basic flash/status messaging

### 6.3 Roles and Permissions Base

Implement:

1. Role model
2. Permission model
3. Assignment of roles to `admin_users`
4. Permission checks for admin modules
5. Seeders for minimum permissions and superadmin

Initial backoffice permissions should cover at least:

1. `admin_users.view`
2. `admin_users.manage`
3. `settings.view`
4. `settings.update`
5. `menus.view`
6. `menus.manage`
7. `pages.view`
8. `pages.manage`
9. `news.view`
10. `news.manage`
11. `media.upload`
12. `audit.view`

### 6.4 Site Settings CMS

Implement editable general settings including:

1. Site name
2. Site tagline
3. Primary logo
4. Secondary logo if needed
5. Brand color metadata
6. Contact email
7. Contact phone
8. Social network URLs
9. Global SEO title
10. Global SEO description
11. Maintenance mode flag

The settings module should behave as a managed singleton record or equivalent controlled configuration resource, not a free-form list of multiple records.

### 6.5 Menus

Implement menu management for:

1. Header menu
2. Footer menu

Each menu item should support:

1. Label
2. URL or internal path
3. Sort order
4. Target behavior if needed
5. Parent-child nesting support if practical in Phase 1, otherwise document flat structure explicitly
6. Active/inactive state

### 6.6 CMS Pages

Implement:

1. Pages
2. Slug
3. Title
4. Summary/excerpt if needed
5. Publication status
6. SEO fields
7. Scheduled publication capability if included in model design
8. Editable sections

Page statuses must support:

1. Draft
2. Published
3. Scheduled
4. Archived

Each page section should support:

1. Section key or type
2. Title
3. Body/content payload
4. Sort order
5. Optional attached image
6. Active/inactive state

### 6.7 News

Implement:

1. News posts
2. Slug
3. Title
4. Summary
5. Body
6. Featured image
7. Publication status
8. Publish date
9. Featured on home flag

News categories may be included now if useful for data integrity and future growth, but tags are not required in this phase.

### 6.8 Basic Media

Implement a minimal media layer to support:

1. Image upload
2. Storage path tracking
3. Original filename
4. MIME type
5. File size
6. Alt text
7. Association to supported content records

Phase 1 image attachment targets:

1. Site settings
2. Page sections or pages
3. News

Validation requirements:

1. Restrict formats to approved image types
2. Restrict size to configured maximum
3. Reject invalid uploads with proper validation responses

### 6.9 Audit Logging

Audit logging must record administrative content changes for:

1. Settings
2. Menus
3. Pages
4. Page sections
5. News

Each audit entry should include:

1. Acting admin user
2. Affected module/domain
3. Action type
4. Target model type
5. Target model id
6. IP address if available
7. Timestamp
8. Previous values when practical
9. New values when practical

### 6.10 Public API

Phase 1 must expose these endpoints:

1. `GET /api/site-settings`
2. `GET /api/menu/header`
3. `GET /api/menu/footer`
4. `GET /api/pages/{slug}`
5. `GET /api/news`
6. `GET /api/news/{slug}`

Public API behavior:

1. Only published/public-safe content is returned
2. Scheduled or archived content must not leak
3. JSON shape should be stable and frontend-friendly
4. Media URLs must resolve consistently for public content

---

## 7. Data Design Principles

### Modeling Rules

1. Use normalized relational tables
2. Use explicit foreign keys where ownership is direct
3. Use polymorphic relations only where they clearly reduce duplication, such as media attachments and audit targets
4. Keep publication state explicit in content tables
5. Store SEO metadata on the content entity it belongs to

### Suggested Core Relationships

1. `AdminUser` belongs to many `Role`
2. `Role` belongs to many `Permission`
3. `Page` has many `PageSection`
4. `Menu` has many `MenuItem`
5. `News` optionally belongs to `NewsCategory`
6. `Media` may belong to an attachable model polymorphically or be linked through explicit foreign keys depending on final implementation choice
7. `AuditLog` belongs to `AdminUser` and references an auditable target polymorphically

---

## 8. Folder and Namespace Conventions

Phase 1 should establish conventions for a modular monolith without requiring package extraction.

### Proposed Namespace Structure

Use domain-oriented application folders under `app/Domain` plus shared HTTP infrastructure.

Example top-level domains:

1. `app/Domain/AdminAuth`
2. `app/Domain/AdminUsers`
3. `app/Domain/AccessControl`
4. `app/Domain/Settings`
5. `app/Domain/Menus`
6. `app/Domain/Pages`
7. `app/Domain/News`
8. `app/Domain/Media`
9. `app/Domain/Audit`
10. `app/Domain/Shared`

### HTTP Layer Conventions

1. Admin controllers live under `app/Http/Controllers/Admin/...`
2. Public API controllers live under `app/Http/Controllers/Api/...`
3. Public-auth controllers remain separate from admin-auth controllers
4. Admin Form Requests live under `app/Http/Requests/Admin/...`
5. API resources live under `app/Http/Resources/...`

### Route File Conventions

1. `routes/web.php` remains the public web entrypoint
2. `routes/admin.php` handles backoffice routes
3. `routes/auth.php` handles public auth routes
4. `routes/admin_auth.php` handles admin auth routes
5. `routes/api.php` handles public content API routes

### View Conventions

1. Admin Blade views under `resources/views/admin/...`
2. Shared admin layout under `resources/views/layouts/admin/...`

### Future Payment Abstraction Convention

Reserve a future namespace such as `app/Domain/Payments/Contracts/PaymentProvider.php` for a payment provider contract. Phase 1 only documents this convention and does not implement provider logic.

---

## 9. Testing Strategy

Phase 1 must follow TDD during implementation.

### Required Test Coverage

1. Admin login is separate from public login
2. Public users cannot access `/admin`
3. Admin users can access `/admin`
4. Admin logout works
5. Permissions restrict module access
6. Settings can be updated by authorized admins
7. Pages CRUD works
8. News CRUD works
9. Menu management works
10. Media upload validates format and size
11. Audit records are created for admin content mutations
12. Public API returns expected content
13. Unpublished content does not appear in public API

### Test Layers

1. Feature tests for authentication, routing, and CRUD flows
2. Policy or permission tests for authorization
3. API tests for public JSON contracts
4. Model tests only where behavior is non-trivial

---

## 10. Delivery Order

The implementation order for Phase 1 is fixed:

1. Separate admin auth
2. Blade dashboard base
3. Base RBAC
4. Settings
5. Menus
6. Pages and sections
7. News
8. Media
9. Audit
10. Public API
11. Tests hardening and documentation

No monetization, ticketing, or logistics implementation work may start before this phase is closed with passing tests and minimum API documentation.

---

## 11. Acceptance Criteria

Phase 1 is considered complete only when all of the following are true:

1. Admin authentication is fully separated from public authentication
2. `/admin/*` routes use `auth:admin`
3. Public users cannot enter the backoffice
4. Admin dashboard and core Blade backoffice screens work
5. Authorized admins can manage settings, menus, pages, page sections, and news
6. Images can be uploaded and attached within supported modules
7. Administrative changes are audited
8. React can consume public REST endpoints for dynamic content
9. Automated tests for critical security and CRUD behavior pass
10. API documentation exists for the public endpoints in scope

---

## 12. Risks and Guardrails

### Risks

1. Laravel starter auth may leak assumptions from public auth into admin auth if not cleanly separated
2. Mixing domain logic into generic controllers may weaken long-term modularity
3. Media handling can become inconsistent if public/private exposure rules are not explicit early
4. Audit logging can become noisy or incomplete if not implemented through a consistent pattern

### Guardrails

1. No admin controller may depend on `App\Models\User`
2. No public auth middleware may protect admin routes
3. No content API may expose unpublished records
4. No future-domain tables should be added in this phase unless strictly needed for shared abstractions
5. All new features must follow red-green-refactor TDD

---

## 13. Implementation Handoff

This spec defines the target for the Phase 1 implementation plan.

The next artifact must be a detailed technical implementation plan covering:

1. Implementation phases
2. Migration list
3. Model list
4. API route list
5. Admin Blade screen list
6. Test list
7. Proposed folder structure

That plan must remain strictly within the approved Phase 1 scope.
