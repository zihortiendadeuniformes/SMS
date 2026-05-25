<?php
// Quick test - run: php test-register.php PAIRING_CODE
$code = $argv[1] ?? 'TEST';
$url  = 'https://sendbridge-backend.vercel.app/gateway/device/register';

$data = json_encode([
    'pairing_code'    => $code,
    'name'            => 'Test Device',
    'device_uuid'     => 'test-uuid-123',
    'android_version' => '12',
    'app_version'     => '1.0',
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$code_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $code_http\n";
echo json_encode(json_decode($response), JSON_PRETTY_PRINT) . "\n";
