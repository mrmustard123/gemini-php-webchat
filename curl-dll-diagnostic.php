<?php
// curl_dll_test.php - Diagnóstico específico de cURL DLLs

echo "<h1>🔍 Diagnóstico específico cURL PHP 8.2.27</h1>";

echo "<h2>📋 Información básica:</h2>";
echo "<strong>Versión PHP actual:</strong> " . phpversion() . "<br>";
echo "<strong>PHP.ini cargado:</strong> " . php_ini_loaded_file() . "<br>";
echo "<strong>Extension dir:</strong> " . ini_get('extension_dir') . "<br>";

echo "<h2>🔧 Estado de cURL:</h2>";
if (function_exists('curl_init')) {
    echo "✅ <strong>cURL disponible</strong><br>";
    exit; // Si funciona, no necesitamos más diagnóstico
} else {
    echo "❌ <strong>cURL NO disponible</strong><br>";
}

echo "<h2>📂 Verificación de archivos DLL:</h2>";

$ext_dir = ini_get('extension_dir');
$php_dir = dirname(PHP_BINARY);
$system_dir = getenv('WINDIR') . '\\System32';

// Lista de DLLs que necesita cURL
$required_dlls = [
    'php_curl.dll' => $ext_dir,
    'libcurl.dll' => [$php_dir, $system_dir, $ext_dir],
    'libeay32.dll' => [$php_dir, $system_dir], 
    'ssleay32.dll' => [$php_dir, $system_dir],
    'libssl-1_1-x64.dll' => [$php_dir, $system_dir],
    'libcrypto-1_1-x64.dll' => [$php_dir, $system_dir],
    'libssh2.dll' => [$php_dir, $system_dir, $ext_dir],
    'nghttp2.dll' => [$php_dir, $system_dir, $ext_dir]
];

foreach ($required_dlls as $dll => $paths) {
    echo "<h3>🔍 Buscando: <code>$dll</code></h3>";
    
    if (!is_array($paths)) {
        $paths = [$paths];
    }
    
    $found = false;
    foreach ($paths as $path) {
        $full_path = $path . DIRECTORY_SEPARATOR . $dll;
        if (file_exists($full_path)) {
            echo "✅ Encontrado en: <code>$full_path</code><br>";
            echo "&nbsp;&nbsp;&nbsp;Tamaño: " . number_format(filesize($full_path)) . " bytes<br>";
            echo "&nbsp;&nbsp;&nbsp;Fecha: " . date('Y-m-d H:i:s', filemtime($full_path)) . "<br>";
            $found = true;
        }
    }
    
    if (!$found) {
        echo "❌ <strong>NO encontrado</strong> en ninguna ubicación<br>";
        echo "&nbsp;&nbsp;&nbsp;Ubicaciones buscadas:<br>";
        foreach ($paths as $path) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;- <code>$path</code><br>";
        }
    }
    echo "<br>";
}

echo "<h2>🔄 Comparación con PHP 8.1.0 (funcional):</h2>";

$php81_dir = 'C:\\wamp64\\bin\\php\\php8.1.0';
if (is_dir($php81_dir)) {
    echo "📂 Comparando DLLs con PHP 8.1.0:<br><br>";
    
    foreach (['php_curl.dll', 'libcurl.dll', 'libssl-1_1-x64.dll', 'libcrypto-1_1-x64.dll'] as $dll) {
        $php81_file = $php81_dir . '\\ext\\' . $dll;
        $php82_file = $ext_dir . '\\' . $dll;
        
        echo "<strong>$dll:</strong><br>";
        
        if (file_exists($php81_file)) {
            echo "&nbsp;&nbsp;PHP 8.1.0: ✅ " . number_format(filesize($php81_file)) . " bytes, " . 
                 date('Y-m-d', filemtime($php81_file)) . "<br>";
        } else {
            echo "&nbsp;&nbsp;PHP 8.1.0: ❌ No encontrado<br>";
        }
        
        if (file_exists($php82_file)) {
            echo "&nbsp;&nbsp;PHP 8.2.27: ✅ " . number_format(filesize($php82_file)) . " bytes, " . 
                 date('Y-m-d', filemtime($php82_file)) . "<br>";
        } else {
            echo "&nbsp;&nbsp;PHP 8.2.27: ❌ No encontrado<br>";
        }
        
        // Verificar si son el mismo archivo
        if (file_exists($php81_file) && file_exists($php82_file)) {
            $same_size = filesize($php81_file) === filesize($php82_file);
            echo "&nbsp;&nbsp;Mismo tamaño: " . ($same_size ? "✅ Sí" : "❌ No") . "<br>";
        }
        echo "<br>";
    }
} else {
    echo "❌ Directorio PHP 8.1.0 no encontrado en la ubicación esperada<br>";
}

echo "<h2>🛠️ Soluciones recomendadas:</h2>";
echo "<ol>";
echo "<li><strong>Copiar DLLs desde PHP 8.1.0:</strong> Copia los DLLs faltantes desde la versión que funciona</li>";
echo "<li><strong>Descargar DLLs oficiales:</strong> Descarga desde php.net para PHP 8.2.27</li>";
echo "<li><strong>Reinstalar PHP 8.2.27:</strong> Usa el instalador oficial de WAMP addon</li>";
echo "<li><strong>Verificar arquitectura:</strong> Asegúrate de que sea x64 si tu WAMP es x64</li>";
echo "</ol>";

echo "<h2>🔍 Variables de entorno PATH:</h2>";
$path = getenv('PATH');
$paths = explode(';', $path);
echo "<strong>Directorios en PATH que podrían contener DLLs:</strong><br>";
foreach ($paths as $p) {
    if (stripos($p, 'php') !== false || stripos($p, 'wamp') !== false || stripos($p, 'openssl') !== false) {
        echo "- <code>$p</code><br>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
code { background: #f0f0f0; padding: 2px 5px; border-radius: 3px; font-size: 11px; }
h1, h2, h3 { color: #333; }
h3 { margin-top: 15px; margin-bottom: 5px; }
</style>