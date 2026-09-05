<?php
$root=dirname(__DIR__);$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));$fail=0;
foreach($it as $file){if($file->getExtension()!=='php'||str_contains($file->getPathname(),DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR))continue;$out=[];$code=0;exec('php -l '.escapeshellarg($file->getPathname()),$out,$code);if($code){$fail++;echo implode(PHP_EOL,$out),PHP_EOL;}}
echo $fail?"FAIL\n":"PASS: PHP syntax OK\n";exit($fail?1:0);
