<?php
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'proyecto_gte'; 
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: 'admin';

$conexion = @pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conexion) {
    die("Error de conexión a PostgreSQL.");
}
?>