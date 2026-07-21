# Inventario y clasificación de datos

| Entidades/tablas principales | Propietario | Clasificación | Sensibilidad |
| --- | --- | --- | --- |
| users, admin_users, roles, permissions, role pivots, password resets | Web | identidad/autorización | alta |
| site_settings, menus, menu_items, pages, sections, news, media, audit_logs | Web | CMS/contenido/auditoría | media/alta |
| players, staff_members, sponsors, board_members, stadiums, clubs, standings, goals, fan_fest, bus_trips | Web | contenido deportivo | media |
| product_categories, products, store_orders, store_order_items | Store | catálogo/comercio | órdenes contienen PII |
| match_events, ticket_zones, ticket_orders, ticket_order_items, issued_tickets | Ticketing | capacidad/entradas | PII y acceso |
| payment_settings, payments, payment_events | Payments | financiero/webhooks | crítica; secretos cifrados |
| membership_plans, membership_orders | Web (legacy) | decisión pendiente: membresías | PII/financiera |
| cache, jobs, failed_jobs, migrations | Infraestructura del runtime | fuera del dominio | operacional |

Dinero se almacena actualmente con columnas decimales heredadas; el contrato nuevo
usa unidades mínimas enteras y código ISO. Las claves públicas futuras serán UUID,
ULID u opacas. No se observan relaciones físicas entre bases porque el monolito usa
una base principal; la separación requiere migraciones y conciliación posteriores.
