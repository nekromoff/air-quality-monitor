<?php
$payload = file_get_contents('php://input');

$ch = curl_init('http://vzduch.cyklokoalicia.sk/public/receive.php');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($payload)]);
$result = curl_exec($ch);
echo $result;
