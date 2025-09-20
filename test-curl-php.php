<?php
// test_curl.php - Verificar si cURL está disponible

echo "<h2>Diagnóstico de cURL en PHP</h2>";

// Verificar si cURL está disponible
if (function_exists('curl_init')) {
    echo "✅ <strong>cURL está HABILITADO</strong><br>";
    
    // Obtener información de cURL
    $curl_info = curl_version();
    echo "📋 Versión de cURL: " . $curl_info['version'] . "<br>";
    echo "📋 Versión SSL: " . $curl_info['ssl_version'] . "<br>";
    
    // Probar una petición simple
    echo "<hr><h3>Prueba de conexión:</h3>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://httpbin.org/get");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Solo para prueba
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ Error en cURL: " . $error . "<br>";
    } else {
        echo "✅ Conexión exitosa (HTTP $httpCode)<br>";
        echo "📄 Respuesta (primeros 200 chars): " . substr($response, 0, 200) . "...<br>";
    }
    
} else {
    echo "❌ <strong>cURL NO está disponible</strong><br>";
    echo "🔧 Necesitas habilitarlo en php.ini<br>";
}

echo "<hr><h3>Extensiones PHP cargadas:</h3>";
$extensions = get_loaded_extensions();
sort($extensions);

echo "<div style='columns: 3; column-gap: 20px;'>";
foreach ($extensions as $ext) {
    $highlight = ($ext === 'curl') ? ' style="background: yellow; font-weight: bold;"' : '';
    echo "<div$highlight>$ext</div>";
}
echo "</div>";

echo "<hr><h3>Información del sistema:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "OS: " . php_uname() . "<br>";
?>