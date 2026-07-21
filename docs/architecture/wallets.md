# Google Wallet y Apple Wallet — Fase 7

## Estado

Código funcional y probado para ambos, credenciales reales pendientes:

| | Google Wallet | Apple Wallet |
| --- | --- | --- |
| Código | `GoogleWalletService` — genera un enlace `https://pay.google.com/gp/v/save/{JWT}` firmado RS256 | `AppleWalletService` — genera el `pass.json` de un event ticket |
| Prueba real | JWT firmado con un par de llaves RSA de prueba, verificado con `openssl_verify` contra la llave pública — prueba matemáticamente que la firma es correcta | Estructura de `pass.json` verificada sin PII en el código de barras |
| Falta | `GOOGLE_WALLET_ISSUER_ID` + cuenta de servicio de Google Wallet API (JSON) | Certificado Pass Type ID (`.p12`) de una cuenta Apple Developer + `APPLE_WALLET_TEAM_ID` |
| Comportamiento sin credenciales | `GET /api/tickets/{id}/wallet/google` responde `501` con mensaje claro | `GET /api/tickets/{id}/wallet/apple` responde `501` con mensaje claro |

Ninguno de los dos se "simula" con datos falsos — cuando falta la
credencial, la API devuelve explícitamente que la función no está
configurada, en vez de fingir éxito.

## Qué se necesita para activarlos

### Google Wallet

1. Cuenta de Google Cloud con la **Google Wallet API** habilitada.
2. Registrarse como emisor (Issuer) en la
   [consola de Google Wallet](https://pay.google.com/business/console) →
   obtiene el `GOOGLE_WALLET_ISSUER_ID`.
3. Crear una cuenta de servicio con el rol de editor sobre ese emisor,
   descargar el JSON de credenciales → va completo (como string JSON) en
   `GOOGLE_WALLET_SERVICE_ACCOUNT_JSON` (en `shared/.env` de Fase 2, nunca
   en el repositorio).
4. Crear la **EventTicketClass** una sola vez (vía la API, con el
   `issuer_id`) — el código actual asume el id
   `{issuer_id}.veraguas-ticketing-event`; falta el script de
   aprovisionamiento de esa clase (no bloqueante: se hace una vez, a mano
   o con un comando `artisan wallet:google:provision-class` a futuro).

### Apple Wallet

1. Cuenta de **Apple Developer Program** (de pago, USD 99/año).
2. Generar un **Pass Type ID** en el portal de Apple Developer.
3. Generar el certificado asociado (`.cer` → convertir a `.p12` con la
   llave privada).
4. Subir el `.p12` fuera del repositorio (mismo patrón que
   `docs/security/secrets-management.md`), apuntar
   `APPLE_WALLET_CERT_PATH` a esa ruta, y completar
   `APPLE_WALLET_TEAM_ID` / `APPLE_WALLET_PASS_TYPE_ID`.
5. Implementar el empaquetado real del `.pkpass` (zip con `pass.json` +
   `manifest.json` con SHA-1 de cada archivo + `signature` PKCS#7 vía
   `openssl_pkcs7_sign`) — el método
   `AppleWalletService::buildSignedPkpass()` tiene el punto de extensión
   marcado explícitamente donde va ese código, pendiente de tener el
   certificado real para poder probarlo de verdad (firmar contra un
   certificado inventado no prueba nada).

## Principio de seguridad que ambos respetan

El valor codificado en el código de barras/QR de **ambos** wallets es
siempre `ticket.qr_token` — el mismo token firmado (HMAC) que ya se usa en
el QR impreso/mostrado en la app. Nunca se genera un identificador
alternativo para el wallet, y ningún campo del código de barras contiene
nombre, correo, teléfono o precio (verificado en
`GoogleWalletServiceTest`/`AppleWalletServiceTest`). Los campos visibles
de la tarjeta (nombre del evento, zona, asiento) sí pueden mostrar
información no sensible para que el usuario identifique su boleto — eso es
distinto del valor escaneable, que es el único que le importa a
`TicketValidationService`.
