<?php
$key = 'mZ5jKCSb3ssoiafzGHxpMQaRg4RMGGnM';
$secret = 'raWK4eBk7ArI1CEl';
$combined = $key . ':' . $secret;
$credentials = base64_encode($combined);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Status: " . $status . "\n";
echo "Response: " . $response . "\n";
