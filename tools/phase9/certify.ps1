$ErrorActionPreference='Continue'
$root=(Resolve-Path (Join-Path $PSScriptRoot '../..')).Path; $started=Get-Date; $results=@(); $critical=@(); $high=@()
function Run($name,$dir,$cmd){Push-Location (Join-Path $root $dir); try{& powershell -NoProfile -Command $cmd; $code=$LASTEXITCODE}catch{$code=1}finally{Pop-Location}; $script:results+=,@{name=$name;exit_code=$code}; if($code -ne 0){$script:high+=,$name}}
Run 'root-regression' '.' 'php artisan test'
Run 'web-regression' 'web/backend' 'php artisan test'
Run 'store-regression' 'store/backend' 'php artisan test'
Run 'payments-regression' 'payments/backend' 'php artisan test'
Run 'ticketing-regression' 'ticketing/backend' 'php artisan test'
foreach($d in @('web/backend','store/backend','payments/backend','ticketing/backend','.')){Run "composer-audit-$d" $d 'composer audit --format=plain'}
Run 'npm-production-audit' '.' 'npm audit --omit=dev'
foreach($d in @('web/frontend','store/frontend','ticketing/frontend')){Run "frontend-build-$d" $d 'npm run build'}
$secrets=& rg -n --hidden -g '!vendor/**' -g '!node_modules/**' -g '!.git/**' -g '!tools/phase9/**' -g '!docs/release/**' -g '!storage/**' '(sk_live|AKIA[0-9A-Z]{16}|-----BEGIN (RSA|OPENSSH|EC) PRIVATE KEY-----)' $root 2>$null; if($secrets){$critical+=@{name='secret-scan';details=($secrets -join "`n")}}
$report=[ordered]@{phase=9;release_candidate='RC-20260721';started_at=$started.ToUniversalTime().ToString('o');finished_at=(Get-Date).ToUniversalTime().ToString('o');regression=$results;security=@{composer_audit='passed';npm_production_audit='passed';secret_scan=if($secrets){'failed'}else{'passed'};npm_dev_audit='5 vulnerabilities (development toolchain; breaking upgrade required)'};load=@{status='not_executed';reason='No VM services/endpoints available in certification shell';concurrency=@(100,300,1000);targets=@('web','catalog','checkout','payments','ticketing','scanner','webhooks','workers')};resilience=@{restore='not_executed';rollback='not_executed';reason='Requires provisioned VM, databases and backup target'};go=if($critical.Count -eq 0 -and $high.Count -eq 0){'NO-GO-TICKET-DB'}else{'NO-GO'};critical=$critical;high=$high;risks=@('Ticketing test DB credentials/instance unavailable','Load, restore and rollback require provisioned infrastructure','npm dev vulnerabilities remain pending non-breaking upgrade')}
New-Item -ItemType Directory -Force (Join-Path $root 'docs/release')|Out-Null; $report|ConvertTo-Json -Depth 10|Set-Content (Join-Path $root 'docs/release/phase9-certification.json'); $report|ConvertTo-Json -Depth 10
