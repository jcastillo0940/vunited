<?php
declare(strict_types=1);
$input=$argv[1]??null; $output=$argv[2]??null;
if(!$input||!$output){fwrite(STDERR,"Uso: php etl-idempotent.php input.json output.json\n");exit(64);}
$rows=json_decode((string)file_get_contents($input),true,512,JSON_THROW_ON_ERROR); $existing=is_file($output)?json_decode((string)file_get_contents($output),true):[]; $index=[];
foreach($existing as $row)$index[$row['dominio'].'|'.$row['tipo'].'|'.$row['legacy_id']]=$row;
foreach($rows as $row){$key=$row['dominio'].'|'.$row['tipo'].'|'.$row['legacy_id'];$row['new_public_id']=$index[$key]['new_public_id']??bin2hex(random_bytes(16));$row['fecha']=$row['fecha']??gmdate('c');$row['error']=null;$index[$key]=$row;}
file_put_contents($output,json_encode(array_values($index),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
