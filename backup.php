<?php

require_once "vendor/dropbox-sdk-php-1.1.4/lib/Dropbox/autoload.php";
use \Dropbox as dbx;

$date = date('Y-m-d_H-i-s');
$fileName = "backup_$date.dump";
$filePath = "/tmp/$fileName";

exec("pg_dump -U zahler -h localhost zahler > $filePath");

$accessToken = file_get_contents('dropbox_access_token');
$accessToken = str_replace("\n", '', $accessToken);

$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
$accountInfo = $dbxClient -> getAccountInfo();
print_r($accountInfo);

$f = fopen($filePath, "rb");
$result = $dbxClient -> uploadFile("/$fileName", dbx\WriteMode::add(), $f);
fclose($f);
print_r($result);

unlink($filePath);
