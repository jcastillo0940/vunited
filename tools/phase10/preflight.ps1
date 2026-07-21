param([switch]$Activate)
$ErrorActionPreference='Stop'; $root=(Resolve-Path (Join-Path $PSScriptRoot '../..')).Path; $started=Get-Date; $checks=@(); $blockers=@()
function Check($name,$ok,$detail){$script:checks+=,@{name=$name;status=if($ok){'PASS'}else{'BLOCKED'};detail=$detail};if(!$ok){$script:blockers+=,@{name=$name;detail=$detail}}}
Check 'release candidate tag' ((git tag --list RC-20260721) -eq 'RC-20260721') 'RC-20260721'
Check 'certification report' (Test-Path (Join-Path $root 'docs/release/phase9-certification.json')) 'Fase 9 report exists'
Check 'release checksum' (Test-Path (Join-Path $root 'docs/release/RC-20260721.sha256')) 'SHA256 manifest exists'
foreach($s in @('web','store','ticketing','payments')){$hasFrontend=$s -ne 'payments' -and (Test-Path (Join-Path $root "$s/frontend"));Check "backend $s" (Test-Path (Join-Path $root "$s/backend")) "$s/backend";Check "frontend $s" ($hasFrontend -or $s -eq 'payments') "$s/frontend"}
Check 'final backups available' (Test-Path (Join-Path $root 'infrastructure/backups')) 'Backup inventory exists; no production snapshot accessible'
Check 'ticketing regression' $false 'MySQL veraguas_ticketing_test rejects configured access (Fase 9)'
Check 'load/restore/rollback' $false 'Requires provisioned VM, services and backup target'
Check 'production activation authority' ($Activate -and $env:PHASE10_ALLOW_PRODUCTION -eq '1') 'Activation is dry-run unless explicit infrastructure authority is supplied'
$decision=if($blockers.Count -eq 0){'GO'}else{'NO-GO'}; $report=[ordered]@{phase=10;started_at=$started.ToUniversalTime().ToString('o');finished_at=(Get-Date).ToUniversalTime().ToString('o');maintenance=if($Activate){'requested'}else{'not_started'};checks=$checks;blockers=$blockers;decision=$decision;activation='not_executed';cleanup='not_executed';reason='No production VM/DNS/PHP-FPM/MySQL/Redis context is available in this workspace'}
New-Item -ItemType Directory -Force (Join-Path $root 'docs/release')|Out-Null; $report|ConvertTo-Json -Depth 8|Set-Content (Join-Path $root 'docs/release/phase10-activation-report.json');$report|ConvertTo-Json -Depth 8; if($decision -eq 'NO-GO'){exit 2}
