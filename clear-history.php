<?php
// clear_history.php - Limpiar historial corrupto

$sessionFile = __DIR__ . '/chat_session.json';

echo "<h1>Limpieza de historial</h1>";

if (file_exists($sessionFile)) {
    echo "📄 Archivo de historial encontrado<br>";
    
    $content = file_get_contents($sessionFile);
    echo "📊 Contenido actual:<br>";
    echo "<pre>" . htmlspecialchars($content) . "</pre>";
    
    // Crear nuevo archivo limpio
    $cleanHistory = ['history' => []];
    file_put_contents($sessionFile, json_encode($cleanHistory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "✅ Historial limpiado exitosamente<br>";
    echo "📄 Nuevo contenido:<br>";
    echo "<pre>" . htmlspecialchars(file_get_contents($sessionFile)) . "</pre>";
} else {
    echo "❌ Archivo de historial no encontrado<br>";
    
    // Crear archivo nuevo
    $cleanHistory = ['history' => []];
    file_put_contents($sessionFile, json_encode($cleanHistory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "✅ Nuevo archivo de historial creado<br>";
}

echo "<br><a href='simple_test.html'>🔄 Volver al test</a>";
?>