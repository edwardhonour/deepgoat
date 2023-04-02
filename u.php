<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

$cURLConnection = curl_init();

curl_setopt($cURLConnection, CURLOPT_URL, 'https://hcpcs.codes/ajax/ZipToCarrier/?zip=60173&format=json&_=1638852544627');
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$phoneList = curl_exec($cURLConnection);
echo $phoneList;
die();
curl_close($cURLConnection);

$jsonArrayResponse = json_decode($phoneList);

print_r($jsonArrayResponse);

?>
