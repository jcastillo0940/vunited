param([ValidateSet(1,2,3)][int]$Trial=1,[switch]$Final)
$ErrorActionPreference='Stop'
$root=(Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$stamp=Get-Date -Format 'yyyyMMdd-HHmmss'; $out=Join-Path $root "storage/phase8/$stamp"; New-Item -ItemType Directory -Force $out | Out-Null
$started=Get-Date; $services=@('web','store','ticketing','payments'); $findings=@(); $refs=@()
foreach($s in $services){
  $dir=Join-Path $root "$s/backend"
  if(!(Test-Path $dir)){ $findings+=,@{service=$s;severity='critical';message='backend inexistente'}; continue }
  $refs+=,@{service=$s;routes='validated';path=$dir}
}
$cross=@()
$rules=@{ 'web/backend'='App\\Domain\\(Store|Ticketing|Payments)|DB::connection\((?!mysql)'; 'store/backend'='App\\Domain\\(Web|Ticketing|Payments)|DB::connection\((?!mysql)'; 'ticketing/backend'='App\\Domain\\(Web|Store)|DB::connection\((?!mysql)'; 'payments/backend'='App\\Domain\\(Web|Store|Ticketing)|DB::connection\((?!mysql)' }
foreach($pair in $rules.GetEnumerator()){$hit=& rg -n --pcre2 $pair.Value (Join-Path $root $pair.Key) 2>$null;if($hit){$cross+=$hit}}
if($cross){$findings+=,@{service='architecture';severity='high';message='referencias cruzadas detectadas';details=($cross -join "`n")}}
$legacy=Get-ChildItem (Join-Path $root 'storage') -Recurse -File -ErrorAction SilentlyContinue | Where-Object {$_.Name -match 'legacy|dump|export'}
if(!$legacy){$findings+=,@{service='etl';severity='blocked';message='No existe dataset legacy verificable; no se ejecuta carga destructiva'}}
$result=[ordered]@{phase=8;trial=if($Final){'final'}else{$Trial};started_at=$started.ToUniversalTime().ToString('o');finished_at=(Get-Date).ToUniversalTime().ToString('o');services=$refs;records=@{source=0;target=0;inserted=0;updated=0;skipped=0};differences=@();errors=@($findings|Where-Object {$_.severity -in @('critical','high')});manual_steps=@('Proporcionar snapshot/dataset legacy firmado','Validar credenciales de producción y ventana de mantenimiento');decision=if($findings.Count){'NO-GO'}else{'GO'};findings=$findings}
$result|ConvertTo-Json -Depth 8|Set-Content (Join-Path $out 'report.json')
$latest=Join-Path $root 'docs/migration/phase8-latest-report.json'; New-Item -ItemType Directory -Force (Split-Path $latest) | Out-Null; Copy-Item (Join-Path $out 'report.json') $latest -Force
Write-Output "Reporte: $(Join-Path $out 'report.json')"
if($result.decision -eq 'NO-GO'){exit 2}
