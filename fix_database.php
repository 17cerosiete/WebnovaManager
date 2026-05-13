<?php
/**
 * fix_database.php
 * Script para verificar y arreglar la estructura de la base de datos
 */

require_once 'config/db.php';

echo "<h2>Verificando estructura de la base de datos...</h2>";

// 1. Verificar si existen las columnas en paginas
$result = $conn->query("SHOW COLUMNS FROM paginas");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

echo "<h3>Columnas en tabla 'paginas':</h3>";
echo "<pre>";
print_r($columns);
echo "</pre>";

// 2. Agregar columnas si no existen
if (!in_array('meta_description', $columns)) {
    echo "<p>Agregando columna meta_description...</p>";
    if ($conn->query("ALTER TABLE paginas ADD COLUMN meta_description VARCHAR(160) DEFAULT ''")) {
        echo "<span style='color: green;'>✓ Columna meta_description agregada</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Error: " . $conn->error . "</span><br>";
    }
}

if (!in_array('keywords', $columns)) {
    echo "<p>Agregando columna keywords...</p>";
    if ($conn->query("ALTER TABLE paginas ADD COLUMN keywords VARCHAR(255) DEFAULT ''")) {
        echo "<span style='color: green;'>✓ Columna keywords agregada</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Error: " . $conn->error . "</span><br>";
    }
}

// 3. Lo mismo para articulos
$result = $conn->query("SHOW COLUMNS FROM articulos");
$columns_articulos = [];
while ($row = $result->fetch_assoc()) {
    $columns_articulos[] = $row['Field'];
}

echo "<h3>Columnas en tabla 'articulos':</h3>";
echo "<pre>";
print_r($columns_articulos);
echo "</pre>";

if (!in_array('meta_description', $columns_articulos)) {
    echo "<p>Agregando columna meta_description a articulos...</p>";
    if ($conn->query("ALTER TABLE articulos ADD COLUMN meta_description VARCHAR(160) DEFAULT ''")) {
        echo "<span style='color: green;'>✓ Columna meta_description agregada a articulos</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Error: " . $conn->error . "</span><br>";
    }
}

if (!in_array('keywords', $columns_articulos)) {
    echo "<p>Agregando columna keywords a articulos...</p>";
    if ($conn->query("ALTER TABLE articulos ADD COLUMN keywords VARCHAR(255) DEFAULT ''")) {
        echo "<span style='color: green;'>✓ Columna keywords agregada a articulos</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Error: " . $conn->error . "</span><br>";
    }
}

echo "<h2>✓ Base de datos verificada y arreglada</h2>";
echo "<p><a href='admin/dashboard.php'>Ir al dashboard</a></p>";
?>
