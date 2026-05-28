# Store Catalog Admin

Fecha: 2026-05-28

## Objetivo

Phase 4B mueve la tienda desde un mock visual a un catalogo real administrable desde backoffice.

En esta fase:

- el frontend de `/tienda` consume productos reales
- las categorias se administran desde backoffice
- el producto destacado sale de datos reales
- el carrito sigue siendo local en navegador
- el checkout real ahora existe solo para tienda
- `store_orders` ya existen
- PayPal ya se usa para el pago de tienda

## Backoffice

Rutas admin creadas:

- `GET /admin/product-categories`
- `GET /admin/product-categories/create`
- `POST /admin/product-categories`
- `GET /admin/product-categories/{productCategory}/edit`
- `PUT /admin/product-categories/{productCategory}`
- `DELETE /admin/product-categories/{productCategory}`
- `GET /admin/products`
- `GET /admin/products/create`
- `POST /admin/products`
- `GET /admin/products/{product}/edit`
- `PUT /admin/products/{product}`
- `DELETE /admin/products/{product}`

Permisos:

- `product_categories.view`
- `product_categories.manage`
- `products.view`
- `products.manage`

## Que puede gestionar el administrador

Categorias:

- nombre
- slug
- descripcion
- orden
- activo/inactivo

Productos:

- categoria
- sku
- nombre
- slug
- descripcion
- descripcion corta
- precio
- compare at price
- moneda
- stock
- si controla stock
- destacado
- activo/inactivo
- badge
- image path
- gallery JSON
- metadata
- orden

## Reglas operativas del catalogo

- solo productos activos salen en API publica
- solo categorias activas salen en API publica
- el producto destacado sale por `is_featured = true`
- si `track_stock = true` y `stock_quantity <= 0`, la API lo marca `out_of_stock`
- el carrito sigue sin persistirse en backend
- la orden y el pago de tienda ya se crean en una fase posterior sobre este catalogo

Protecciones:

- no se permite borrar una categoria con productos asociados
- para productos, el flujo recomendado sigue siendo desactivar antes que borrar

## API publica consumida por /tienda

- `GET /api/store/products`
- `GET /api/store/products/{slug}`
- `GET /api/store/categories`
- `GET /api/store/featured-product`

Filtros soportados en `GET /api/store/products`:

- `category`
- `featured`
- `search`

## Como impacta en el frontend

`/tienda` ahora intenta cargar:

- categorias reales
- listado real de productos
- producto destacado real

Si la API falla, el frontend usa fallback temporal basado en `productsMock.js` para no romper la experiencia local.

## Precio y visualizacion

La UI muestra:

- `price` como precio actual
- `compare_at_price` como precio tachado si existe
- badge comercial si existe
- estado `Agotado` si el producto llega como `out_of_stock`

## Que sigue mock todavia

- carrito
- cupones
- envios
- fulfillment avanzado
- experiencia de carrito como sesion persistida
- descuentos complejos

## Checkout de tienda sobre este catalogo

Sobre este catalogo ya existe:

- `Store Orders + PayPal`

Eso significa:

- se crea orden real de tienda
- se calcula total en backend
- se crea `Payment` asociado
- se redirige a PayPal
- el webhook sincroniza el estado final

Ver tambien:

- `docs/store/store-orders-payment-flow.md`
