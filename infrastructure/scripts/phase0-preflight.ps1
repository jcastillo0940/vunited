param(
    [switch]$RunTests
)

$ErrorActionPreference = 'Stop'
$workspace = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
Set-Location -LiteralPath $workspace

function Invoke-Checked {
    param(
        [string]$Label,
        [scriptblock]$Command
    )

    Write-Host "[CHECK] $Label"
    & $Command

    if ($LASTEXITCODE -ne 0) {
        throw "Falló: $Label (exit $LASTEXITCODE)"
    }
}

Invoke-Checked 'PHP disponible' { php --version }
Invoke-Checked 'Composer disponible' { composer --version }
Invoke-Checked 'composer.json válido' { composer validate --no-check-publish }
Invoke-Checked 'Sintaxis de archivos modificados' {
    php -l bootstrap/app.php
    php -l config/database.php
    php -l routes/api.php
    php -l app/Http/Middleware/AttachCorrelationId.php
    php -l app/Http/Controllers/Api/ProductController.php
    php -l app/Http/Resources/V1/Store/ProductResource.php
    php -l tests/contracts/ArchitectureContractTest.php
}
Invoke-Checked 'Estado de Laravel' { php artisan about }
Invoke-Checked 'Rutas Store v1 registradas' { php artisan route:list --path=api/v1/store }
Invoke-Checked 'Cambios sin errores de whitespace' { git diff --check }

$trackedEnvironment = git ls-files .env
if ($trackedEnvironment) {
    throw 'El archivo .env no debe estar versionado.'
}

$requiredPaths = @(
    'web/backend',
    'web/frontend',
    'store/backend',
    'store/frontend',
    'ticketing/backend',
    'ticketing/frontend',
    'payments/backend',
    'shared/api-contracts',
    'shared/ui',
    'shared/config',
    'docs/architecture',
    'docs/runbooks',
    'docs/decisions',
    'infrastructure/apache',
    'infrastructure/systemd',
    'infrastructure/mysql',
    'infrastructure/redis',
    'infrastructure/observability',
    'infrastructure/backups',
    'tests/contracts',
    'tests/e2e',
    'tests/performance',
    'tests/security'
)

$missingPaths = $requiredPaths | Where-Object { -not (Test-Path -LiteralPath $_) }
if ($missingPaths) {
    throw "Faltan rutas requeridas: $($missingPaths -join ', ')"
}

if ($RunTests) {
    Invoke-Checked 'Contratos OpenAPI' { npm run lint:contracts }
    Invoke-Checked 'Suite completa' { php artisan test }
} else {
    Write-Host '[SKIP] Suite completa; usar -RunTests para ejecutarla.'
}

try {
    $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/up' -UseBasicParsing -TimeoutSec 5
    Write-Host "[OK] Aplicación local HTTP $($response.StatusCode)"
} catch {
    Write-Host '[INFO] La aplicación local no está iniciada; no bloquea el preflight estático.'
}

Write-Host '[OK] Preflight local completado.'
