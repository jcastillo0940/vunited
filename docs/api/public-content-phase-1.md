# Public Content API - Phase 1

Base path: `/api`

These endpoints are public and do not require authentication. They expose only publishable CMS content for the React frontend.

## GET `/api/site-settings`

Returns the current singleton site settings payload.

Response fields:

- `site_name`
- `site_tagline`
- `primary_logo_url`
- `secondary_logo_url`
- `primary_color`
- `accent_color`
- `contact_email`
- `contact_phone`
- `social_links`
- `global_seo_title`
- `global_seo_description`
- `maintenance_mode`

Example response:

```json
{
  "data": {
    "site_name": "Veraguas United FC",
    "site_tagline": "Orgullo de Veraguas",
    "primary_logo_url": "/storage/logos/primary.png",
    "secondary_logo_url": "/storage/logos/secondary.png",
    "primary_color": "#123ABC",
    "accent_color": "#abcdef",
    "contact_email": "hola@veraguasunited.test",
    "contact_phone": "+507 6000-0000",
    "social_links": {
      "instagram": "https://instagram.com/veraguasunited"
    },
    "global_seo_title": "Veraguas United FC",
    "global_seo_description": "Sitio oficial del club.",
    "maintenance_mode": false
  }
}
```

## GET `/api/menu/header`
## GET `/api/menu/footer`

Returns the active menu for the requested location.

Response fields:

- `location`
- `items[]`
  - `label`
  - `url`
  - `target`
  - `sort_order`
  - `children[]`

Behavior:

- Only active menu items are returned.
- Items are ordered by `sort_order`.
- Nested children are included when active.

Example response:

```json
{
  "data": {
    "location": "header",
    "items": [
      {
        "label": "Noticias",
        "url": "/noticias",
        "target": "_self",
        "sort_order": 1,
        "children": []
      },
      {
        "label": "Club",
        "url": "/club",
        "target": "_self",
        "sort_order": 2,
        "children": [
          {
            "label": "Historia",
            "url": "/club/historia",
            "target": "_self",
            "sort_order": 1,
            "children": []
          }
        ]
      }
    ]
  }
}
```

## GET `/api/pages/{slug}`

Returns one public page by slug.

Response fields:

- `title`
- `slug`
- `excerpt`
- `status`
- `seo_title`
- `seo_description`
- `published_at`
- `sections[]`
  - `section_key`
  - `type`
  - `title`
  - `body`
  - `payload`
  - `sort_order`
  - `image_url`

Visibility rules:

- Included: `published`
- Included: `scheduled` only when `published_at <= now`
- Excluded: `draft`
- Excluded: `archived`
- Only active sections are returned
- Sections are ordered by `sort_order`

Example response:

```json
{
  "data": {
    "title": "Historia",
    "slug": "historia",
    "excerpt": "Resumen institucional",
    "status": "published",
    "seo_title": "Historia del club",
    "seo_description": "Conoce la historia del club.",
    "published_at": "2026-05-27T12:00:00.000000Z",
    "sections": [
      {
        "section_key": "hero",
        "type": "banner",
        "title": "Hero",
        "body": "Contenido principal",
        "payload": {
          "cta_label": "Ver mas"
        },
        "sort_order": 1,
        "image_url": "/storage/pages/hero.png"
      }
    ]
  }
}
```

## GET `/api/news`

Returns the public news listing.

Response fields per article:

- `title`
- `slug`
- `summary`
- `body`
- `published_at`
- `is_featured`
- `seo_title`
- `seo_description`
- `featured_image_url`
- `category`

Visibility rules:

- Included: `published`
- Included: `scheduled` only when `published_at <= now`
- Excluded: `draft`
- Excluded: `archived`

Example response:

```json
{
  "data": [
    {
      "title": "Victoria en casa",
      "slug": "victoria-en-casa",
      "summary": "Resumen breve",
      "body": "Contenido completo de la noticia.",
      "published_at": "2026-05-27T12:00:00.000000Z",
      "is_featured": true,
      "seo_title": "Victoria en casa",
      "seo_description": "Detalle de la victoria",
      "featured_image_url": "/storage/news/victoria.png",
      "category": {
        "name": "Primer Equipo",
        "slug": "primer-equipo"
      }
    }
  ]
}
```

## GET `/api/news/{slug}`

Returns one public news article by slug with the same payload shape as the listing resource.

Example response:

```json
{
  "data": {
    "title": "Victoria en casa",
    "slug": "victoria-en-casa",
    "summary": "Resumen breve",
    "body": "Contenido completo de la noticia.",
    "published_at": "2026-05-27T12:00:00.000000Z",
    "is_featured": true,
    "seo_title": "Victoria en casa",
    "seo_description": "Detalle de la victoria",
    "featured_image_url": "/storage/news/victoria.png",
    "category": {
      "name": "Primer Equipo",
      "slug": "primer-equipo"
    }
  }
}
```

## Notes

- Media URLs are derived from the `public` disk.
- No write endpoints are exposed in Phase 1.
- React can rely on these shapes as the stable public read model for settings, menus, pages, and news.
