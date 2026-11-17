<?php
require_once 'conexion.php';
$data = json_decode(file_get_contents('php://input'), true);

if($data && isset($data['carrito']) && count($data['carrito']) > 0) {
    $total = floatval($data['total']);
    $detalles = pg_escape_string($conexion, json_encode($data['carrito']));
    
    // Iniciar transacción SQL para asegurar que ambas consultas se ejecuten juntas
    pg_query($conexion, "BEGIN");
    
    // 1. Guardar el pedido
    $query_pedido = "INSERT INTO jh_pedidos (total, detalles) VALUES ($total, '$detalles')";
    $res_pedido = pg_query($conexion, $query_pedido);
    
    // 2. Descontar el stock de cada producto
    $stock_ok = true;
    foreach($data['carrito'] as $item) {
        $id = intval($item['id']);
        $cant = intval($item['cantidad']);
        $query_stock = "UPDATE jh_productos SET stock = stock - $cant WHERE id_producto = $id";
        $res_stock = pg_query($conexion, $query_stock);
        if(!$res_stock) $stock_ok = false;
    }
    
    // Confirmar o revertir cambios
    if($res_pedido && $stock_ok) {
        pg_query($conexion, "COMMIT");
        echo json_encode(['status' => 'success']);
    } else {
        pg_query($conexion, "ROLLBACK");
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'empty']);
}
?>