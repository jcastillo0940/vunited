param(
    [string]$Workspace = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path,
    [string]$BackupRoot = (Join-Path (Split-Path (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path -Parent) 'VeragasUnited-backups'),
    [switch]$SkipDatabaseRestore
)

$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $Workspace

function Get-DotEnvValue([string]$Name) {
    $line = Get-Content -LiteralPath (Join-Path $Workspace '.env') | Where-Object { $_ -match "^$Name=" } | Select-Object -First 1
    if ($null -eq $line) { return '' }
    return ($line -split '=', 2)[1]
}

function Write-Inventory([string]$Path, [scriptblock]$Body) {
    & $Body | Out-File -LiteralPath $Path -Encoding utf8
}

function Invoke-ExternalChecked([string]$File, [string[]]$Arguments, [string]$StdOutPath) {
    $stderrPath = "$StdOutPath.stderr"
    & $File @Arguments 1> $StdOutPath 2> $stderrPath
    $exitCode = $LASTEXITCODE
    if ($exitCode -ne 0) {
        $errorText = if (Test-Path -LiteralPath $stderrPath) { Get-Content -Raw -LiteralPath $stderrPath } else { '' }
        throw "Comando falló ($exitCode): $File $($Arguments -join ' ') $errorText"
    }
    if (Test-Path -LiteralPath $stderrPath) { Remove-Item -LiteralPath $stderrPath -Force }
}

if (-not (Test-Path -LiteralPath '.env')) { throw 'Falta .env; no se puede respaldar la base sin imprimir credenciales.' }

$stamp = (Get-Date).ToUniversalTime().ToString('yyyyMMddTHHmmssZ')
New-Item -ItemType Directory -Path $BackupRoot -Force | Out-Null
$destination = Join-Path (Resolve-Path $BackupRoot).Path "phase1-$stamp"
New-Item -ItemType Directory -Path $destination, "$destination\databases", "$destination\code", "$destination\media", "$destination\config", "$destination\inventories", "$destination\restore-test" -Force | Out-Null

$mysqlHost = Get-DotEnvValue 'DB_HOST'; if (-not $mysqlHost) { $mysqlHost = '127.0.0.1' }
$mysqlPort = Get-DotEnvValue 'DB_PORT'; if (-not $mysqlPort) { $mysqlPort = '3306' }
$mysqlUser = Get-DotEnvValue 'DB_USERNAME'
$mysqlPassword = Get-DotEnvValue 'DB_PASSWORD'
$mysql = (Get-Command mysql -ErrorAction SilentlyContinue).Source
$mysqldump = (Get-Command mysqldump -ErrorAction SilentlyContinue).Source
if (-not $mysql) { $mysql = 'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe' }
if (-not $mysqldump) { $mysqldump = 'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe' }
if (-not (Test-Path -LiteralPath $mysql)) { throw "No se encontró mysql: $mysql" }
if (-not (Test-Path -LiteralPath $mysqldump)) { throw "No se encontró mysqldump: $mysqldump" }

$env:MYSQL_PWD = $mysqlPassword
try {
    $databaseList = & $mysql --protocol=TCP --host=$mysqlHost --port=$mysqlPort --user=$mysqlUser --batch --skip-column-names -e 'SHOW DATABASES' 2> "$destination\inventories\mysql-connect.stderr"
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo conectar a MySQL.' }
    $databaseList = @($databaseList | Where-Object { $_ -and $_ -notmatch '^(information_schema|performance_schema|sys)$' })
    $databaseList | Out-File -LiteralPath "$destination\inventories\databases.txt" -Encoding utf8

    Invoke-ExternalChecked $mysqldump @('--protocol=TCP', "--host=$mysqlHost", "--port=$mysqlPort", "--user=$mysqlUser", '--all-databases', '--routines', '--events', '--triggers', '--single-transaction', '--hex-blob') "$destination\databases\all-databases.sql"
    foreach ($database in $databaseList) {
        $safeName = $database -replace '[^A-Za-z0-9_.-]', '_'
        Invoke-ExternalChecked $mysqldump @('--protocol=TCP', "--host=$mysqlHost", "--port=$mysqlPort", "--user=$mysqlUser", '--databases', $database, '--routines', '--events', '--triggers', '--single-transaction', '--hex-blob') "$destination\databases\$safeName.sql"
    }
    $restoreSource = if ($databaseList -contains 'weveraguas') { 'weveraguas' } else { $databaseList | Select-Object -First 1 }
    if ($restoreSource) {
        Invoke-ExternalChecked $mysqldump @('--protocol=TCP', "--host=$mysqlHost", "--port=$mysqlPort", "--user=$mysqlUser", $restoreSource, '--routines', '--events', '--triggers', '--single-transaction', '--hex-blob') "$destination\restore-test\source.sql"
    }
} finally {
    Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
}

$codeDestination = "$destination\code"
$robocopyArgs = @($Workspace, $codeDestination, '/E', '/COPY:DAT', '/R:1', '/W:1', '/XJ', '/XD', '.git', 'vendor', 'node_modules', 'storage\logs', 'public\build', "$BackupRoot", '/XF', '.env', '.env.*', '*.log')
& robocopy @robocopyArgs | Out-Null
if ($LASTEXITCODE -gt 7) { throw "Copia de código falló con robocopy $LASTEXITCODE" }

if (Test-Path -LiteralPath 'storage\app\public') {
    $mediaDestination = "$destination\media\storage-app-public"
    & robocopy (Join-Path $Workspace 'storage\app\public') $mediaDestination '/E' '/COPY:DAT' '/R:1' '/W:1' '/XJ' | Out-Null
    if ($LASTEXITCODE -gt 7) { throw "Copia de medios falló con robocopy $LASTEXITCODE" }
}

$apacheConf = Get-ChildItem 'C:\wamp64\bin\apache' -Recurse -File -ErrorAction SilentlyContinue | Where-Object { $_.Name -in @('httpd.conf', 'httpd-vhosts.conf') }
foreach ($file in $apacheConf) {
    $relative = $file.FullName.Substring('C:\wamp64\bin\apache\'.Length)
    $target = Join-Path "$destination\config\apache" $relative
    New-Item -ItemType Directory -Path (Split-Path $target -Parent) -Force | Out-Null
    Copy-Item -LiteralPath $file.FullName -Destination $target
}
$phpIni = Get-ChildItem 'C:\wamp64\bin\php\php8.3.14\php.ini' -ErrorAction SilentlyContinue
if ($phpIni) { New-Item -ItemType Directory -Path "$destination\config\php" -Force | Out-Null; Copy-Item $phpIni.FullName "$destination\config\php\php.ini" }

Write-Inventory "$destination\inventories\environment-keys.txt" {
    Get-Content '.env' | Where-Object { $_ -match '^[A-Za-z_][A-Za-z0-9_]*=' } | ForEach-Object { ($_ -split '=', 2)[0] }
}
Write-Inventory "$destination\inventories\services-processes.txt" {
    'Windows services:'; Get-Service | Where-Object Status -eq Running | Select-Object Name,DisplayName,Status
    'Relevant processes:'; Get-Process | Where-Object ProcessName -match 'php|apache|httpd|mysql|maria|redis|supervis' | Select-Object ProcessName,Id,Path
}
Write-Inventory "$destination\inventories\ports-firewall.txt" {
    'Listening ports:'; Get-NetTCPConnection -State Listen -ErrorAction SilentlyContinue | Sort-Object LocalPort | Select-Object LocalAddress,LocalPort,OwningProcess
    'Firewall profiles:'; Get-NetFirewallProfile | Select-Object Name,Enabled,DefaultInboundAction,DefaultOutboundAction
}
Write-Inventory "$destination\inventories\certificates.txt" {
    Get-ChildItem Cert:\LocalMachine\My -ErrorAction SilentlyContinue | Select-Object Subject,Issuer,NotAfter,Thumbprint
}
Write-Inventory "$destination\inventories\git.txt" {
    git rev-parse --show-toplevel; git branch --show-current; git log -5 --oneline; git status --short
}
Write-Inventory "$destination\inventories\unsupported-linux-gcp.txt" {
    'Apache CLI, PHP-FPM, systemd, Supervisor, cron, gcloud, gsutil y Ops Agent no están disponibles en este entorno Windows/WAMP.'
}
Write-Inventory "$destination\inventories\recovery-procedure.txt" {
    "Backup: $destination"
    'Verificar checksums.sha256 antes de usar cualquier archivo.'
    'Restaurar una base a una instancia temporal y validar conteos antes de tocar producción.'
    'Restaurar configuración en una ruta temporal y ejecutar el validador de la plataforma.'
    'Activar cambios solo mediante una release versionada; conservar este backup para rollback.'
}

if (-not $SkipDatabaseRestore -and (Test-Path -LiteralPath "$destination\restore-test\source.sql")) {
    $restoreDb = "veraguas_phase1_restore_$stamp" -replace '[^A-Za-z0-9_]', '_'
    $env:MYSQL_PWD = $mysqlPassword
    try {
        & $mysql --protocol=TCP --host=$mysqlHost --port=$mysqlPort --user=$mysqlUser -e "CREATE DATABASE $restoreDb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" 2> "$destination\restore-test\create.stderr"
        if ($LASTEXITCODE -ne 0) { throw 'No se pudo crear la base temporal de restauración.' }
        Get-Content -Raw -LiteralPath "$destination\restore-test\source.sql" | & $mysql --protocol=TCP --host=$mysqlHost --port=$mysqlPort --user=$mysqlUser $restoreDb 2> "$destination\restore-test\restore.stderr"
        if ($LASTEXITCODE -ne 0) { throw 'Falló la restauración temporal.' }
        $tableCount = & $mysql --protocol=TCP --host=$mysqlHost --port=$mysqlPort --user=$mysqlUser --batch --skip-column-names -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$restoreDb'" 2> "$destination\restore-test\verify.stderr"
        if ([int]$tableCount -lt 1) { throw 'La restauración temporal no contiene tablas.' }
        $tableCount | Out-File -LiteralPath "$destination\restore-test\table-count.txt" -Encoding utf8
    } finally {
        & $mysql --protocol=TCP --host=$mysqlHost --port=$mysqlPort --user=$mysqlUser -e "DROP DATABASE IF EXISTS $restoreDb" 2> "$destination\restore-test\drop.stderr" | Out-Null
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }
}

$apacheExe = 'C:\wamp64\bin\apache\apache2.4.62.1\bin\httpd.exe'
$restoredApache = Get-ChildItem "$destination\config\apache" -Recurse -File -Filter httpd.conf | Where-Object FullName -notmatch '\\original\\' | Select-Object -First 1
if ((Test-Path -LiteralPath $apacheExe) -and $restoredApache) {
    New-Item -ItemType Directory -Path "$destination\restore-test\apache\conf" -Force | Out-Null
    $restoredApachePath = "$destination\restore-test\apache\conf\httpd.conf"
    Copy-Item -LiteralPath $restoredApache.FullName -Destination $restoredApachePath -Force
    & $apacheExe -t -f $restoredApachePath 2>&1 | Out-File "$destination\restore-test\apache-validation.txt" -Encoding utf8
    if ($LASTEXITCODE -ne 0) { throw 'La configuración Apache restaurada no valida.' }
}
$phpExe = (Get-Command php -ErrorAction SilentlyContinue).Source
$restoredPhp = "$destination\config\php\php.ini"
if ($phpExe -and (Test-Path -LiteralPath $restoredPhp)) {
    New-Item -ItemType Directory -Path "$destination\restore-test\php" -Force | Out-Null
    $restoredPhpPath = "$destination\restore-test\php\php.ini"
    Copy-Item -LiteralPath $restoredPhp -Destination $restoredPhpPath -Force
    & $phpExe -c $restoredPhpPath -i 2>&1 | Select-Object -Last 4 | Out-File "$destination\restore-test\php-validation.txt" -Encoding utf8
    if ($LASTEXITCODE -ne 0) { throw 'La configuración PHP restaurada no valida.' }
}

Write-Inventory "$destination\inventories\services-processes-after.txt" {
    'AFTER_BACKUP'; Get-Date -Format o
    Get-Process | Where-Object ProcessName -match 'php|apache|httpd|mysql|maria|redis|supervis' | Select-Object ProcessName,Id,Path
}
Write-Inventory "$destination\inventories\ports-firewall-after.txt" {
    'AFTER_BACKUP'; Get-Date -Format o
    Get-NetTCPConnection -State Listen -ErrorAction SilentlyContinue | Sort-Object LocalPort | Select-Object LocalAddress,LocalPort,OwningProcess
}

Get-ChildItem -LiteralPath $destination -Recurse -File | Where-Object Name -ne 'checksums.sha256' | Get-FileHash -Algorithm SHA256 | ForEach-Object {
    $relative = $_.Path.Substring($destination.Length + 1)
    '{0} *{1}' -f $_.Hash.ToLowerInvariant(), $relative.Replace('\', '/')
} | Out-File -LiteralPath "$destination\checksums.sha256" -Encoding utf8

Write-Output "BACKUP_ROOT=$destination"
Write-Output "DATABASE_COUNT=$($databaseList.Count)"
Write-Output "CHECKSUM_FILE=$destination\checksums.sha256"
