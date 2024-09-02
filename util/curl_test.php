<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

$curlopt_url = "https://www.google.com";
 
$curlData    = curl_version();
 
if ($curlData['features'] & CURL_VERSION_SSL) {
    echo "SSL is not supported with this cURL installation.";
    exit;
}
 
try {
    $ch = curl_init();
 
    curl_setopt($ch, CURLOPT_URL, $curlopt_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
 
    if(!$result = curl_exec($ch)) {
        var_dump(curl_error($ch));
    } else {
        echo $result;
    }
 
    curl_close($ch);
} catch(Exception $e) {
    print_r($e);
}