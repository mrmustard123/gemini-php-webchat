<?php
// debug_response.php - Para ver exactamente qué responde chat.php

// Simular la misma petición que hace el frontend
$data = json_encode(['message' => 'Hola']);

$ch = curl_init('http://localhost/gemini-php/chat.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h1>Debug de respuesta del servidor</h1>";
echo "<h2>HTTP Code: $httpCode</h2>";
echo "<h2>Response length: " . strlen($response) . "</h2>";
echo "<h2>Raw response:</h2>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<h2>Análisis carácter por carácter (primeros 100):</h2>";
echo "<pre>";
for ($i = 0; $i < min(100, strlen($response)); $i++) {
    $char = $response[$i];
    $ord = ord($char);
    if ($ord < 32 || $ord > 126) {
        echo "[$ord]";
    } else {
        echo $char;
    }
}
echo "</pre>";

echo "<h2>Intento de parse JSON:</h2>";
$json = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "<pre>" . print_r($json, true) . "</pre>";
} else {
    echo "<strong>ERROR JSON:</strong> " . json_last_error_msg();
}
?>