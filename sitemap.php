<?php
header("Content-Type: application/xml; charset=utf-8");
require_once 'conexion.php';

// Cambia esta URL cuando tengas tu enlace público definitivo
$base_url = "https://tu-dominio-en-render.onrender.com";

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Páginas estáticas principales
$paginas = ['index.php', 'soporte.php'];
foreach ($paginas as $pag) {
    echo "<url>";
    echo "<loc>{$base_url}/{$pag}</loc>";
    echo "<changefreq>daily</changefreq>";
    echo "<priority>1.0</priority>";
    echo "</url>";
}

// Productos dinámicos desde PostgreSQL
$res = @pg_query($conexion, "SELECT id_producto FROM jh_productos WHERE stock > 0");
if ($res) {
    while ($row = pg_fetch_assoc($res)) {
        echo "<url>";
        echo "<loc>{$base_url}/index.php?producto=" . $row['id_producto'] . "</loc>";
        echo "<changefreq>weekly</changefreq>";
        echo "<priority>0.8</priority>";
        echo "</url>";
    }
}

echo '</urlset>';
?>