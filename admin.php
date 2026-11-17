<?php
require_once 'conexion.php';

$mensaje = '';

// ================= LÓGICA DE INVENTARIO (POST) =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Agregar Producto
    if (isset($_POST['agregar_producto'])) {
        $nombre = pg_escape_string($conexion, $_POST['nombre']);
        $categoria = pg_escape_string($conexion, $_POST['categoria']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $imagen = pg_escape_string($conexion, $_POST['imagen']);
        $descripcion = pg_escape_string($conexion, $_POST['descripcion']);

        $query = "INSERT INTO jh_productos (nombre, descripcion, precio, stock, categoria, imagen) 
                  VALUES ('$nombre', '$descripcion', $precio, $stock, '$categoria', '$imagen')";
        
        if(@pg_query($conexion, $query)) {
            $mensaje = '<div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl mb-6 font-bold">✅ Producto agregado con éxito al catálogo.</div>';
        } else {
            $mensaje = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-bold">❌ Error al guardar el producto en la base de datos.</div>';
        }
    } 
    // 2. Editar Producto
    elseif (isset($_POST['editar_producto'])) {
        $id_editar = intval($_POST['id_producto_editar']);
        $nombre = pg_escape_string($conexion, $_POST['nombre']);
        $categoria = pg_escape_string($conexion, $_POST['categoria']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $imagen = pg_escape_string($conexion, $_POST['imagen']);
        $descripcion = pg_escape_string($conexion, $_POST['descripcion']);

        $query = "UPDATE jh_productos 
                  SET nombre='$nombre', descripcion='$descripcion', precio=$precio, stock=$stock, categoria='$categoria', imagen='$imagen' 
                  WHERE id_producto=$id_editar";
        
        if(@pg_query($conexion, $query)) {
            $mensaje = '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-xl mb-6 font-bold">✏️ Producto actualizado correctamente.</div>';
        } else {
            $mensaje = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-bold">❌ Error al actualizar el producto.</div>';
        }
    }
    // 3. Eliminar Producto
    elseif (isset($_POST['eliminar_producto'])) {
        $id_eliminar = intval($_POST['id_producto']);
        if(@pg_query($conexion, "DELETE FROM jh_productos WHERE id_producto = $id_eliminar")) {
            $mensaje = '<div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl mb-6 font-bold">🗑️ Producto eliminado correctamente.</div>';
        } else {
            $mensaje = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-bold">❌ Error al eliminar el producto.</div>';
        }
    }
}

// ================= CONSULTAS PARA DASHBOARD =================
$ventas_mes = 0;
$tickets_abiertos = 0;
$lista_tickets = false;

$res_ventas = @pg_query($conexion, "SELECT SUM(total) as total_ventas FROM jh_pedidos");
if ($res_ventas) { $ventas_mes = pg_fetch_assoc($res_ventas)['total_ventas'] ?? 0; }

$res_tickets = @pg_query($conexion, "SELECT COUNT(*) as abiertos FROM jh_tickets WHERE estado = 'Abierto'");
if ($res_tickets) { $tickets_abiertos = pg_fetch_assoc($res_tickets)['abiertos'] ?? 0; }

$lista_tickets = @pg_query($conexion, "SELECT * FROM jh_tickets ORDER BY fecha_creacion DESC LIMIT 5");

// Consulta del inventario completo
$lista_productos = @pg_query($conexion, "SELECT * FROM jh_productos ORDER BY id_producto DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo | TecNM Huejutla</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .bg-tecnm-dark { background-color: #1B365D; }
        .text-tecnm-dark { color: #1B365D; }
        .vista-activa { display: block; animation: fadeIn 0.3s ease-in-out; }
        .vista-oculta { display: none; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#f4f6f9] font-sans flex h-screen overflow-hidden">

    <!-- ================= MODAL AGREGAR PRODUCTO ================= -->
    <div id="modal-agregar" class="fixed inset-0 bg-slate-900/60 z-[60] hidden items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl relative">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-3xl">
                <h3 class="text-xl font-extrabold text-[#1B365D]">Agregar Nuevo Producto</h3>
                <button onclick="cerrarModalAgregar()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="admin.php" method="POST" class="p-8 space-y-5">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nombre del Producto</label>
                        <input type="text" name="nombre" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Categoría</label>
                        <select name="categoria" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                            <option value="Ropa">Ropa Oficial</option>
                            <option value="Útiles">Útiles Escolares</option>
                            <option value="Accesorios">Accesorios</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Precio de Venta ($)</label>
                        <input type="number" step="0.01" name="precio" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Stock Inicial</label>
                        <input type="number" name="stock" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">URL de la Imagen</label>
                    <input type="text" name="imagen" placeholder="https://ejemplo.com/imagen.jpg" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]"></textarea>
                </div>
                <div class="pt-4 text-right">
                    <button type="submit" name="agregar_producto" class="bg-[#1B365D] hover:bg-blue-900 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-md">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL EDITAR PRODUCTO ================= -->
    <div id="modal-editar" class="fixed inset-0 bg-slate-900/60 z-[60] hidden items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl relative">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-3xl">
                <h3 class="text-xl font-extrabold text-blue-600">Editar Producto</h3>
                <button onclick="cerrarModalEditar()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="admin.php" method="POST" class="p-8 space-y-5">
                <input type="hidden" name="id_producto_editar" id="edit-id">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nombre del Producto</label>
                        <input type="text" name="nombre" id="edit-nombre" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Categoría</label>
                        <select name="categoria" id="edit-categoria" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="Ropa">Ropa Oficial</option>
                            <option value="Útiles">Útiles Escolares</option>
                            <option value="Accesorios">Accesorios</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Precio de Venta ($)</label>
                        <input type="number" step="0.01" name="precio" id="edit-precio" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Stock Disponible</label>
                        <input type="number" name="stock" id="edit-stock" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">URL de la Imagen</label>
                    <input type="text" name="imagen" id="edit-imagen" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Descripción</label>
                    <textarea name="descripcion" id="edit-descripcion" rows="3" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                </div>
                <div class="pt-4 text-right">
                    <button type="submit" name="editar_producto" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-md">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= BARRA LATERAL (SIDEBAR) ================= -->
    <aside class="w-72 bg-tecnm-dark text-white flex flex-col justify-between hidden md:flex h-full shadow-2xl z-20">
        <div>
            <!-- Logo TecNM -->
            <div class="px-6 py-8 flex items-center space-x-3 border-b border-blue-800/50">
                <img src="src/original_Logo-TecNM-2017.png" alt="Logo TecNM" class="h-10 w-auto bg-white p-1 rounded shadow-sm">
                <div>
                    <h2 class="font-bold text-sm tracking-wide">TecNM Huejutla</h2>
                    <p class="text-[9px] text-blue-300 uppercase tracking-widest">Panel de Administración</p>
                </div>
            </div>

            <!-- Menú de Navegación -->
            <div class="px-4 py-6">
                <p class="px-4 text-xs font-bold text-blue-300/50 uppercase tracking-widest mb-4">Administración</p>
                <nav class="space-y-1">
                    <button onclick="cambiarVista('resumen', this)" class="menu-btn w-full flex items-center px-4 py-3 bg-white text-tecnm-dark rounded-xl font-bold shadow-sm justify-between transition-all">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Resumen General
                        </div>
                        <div class="indicador w-2 h-2 bg-blue-500 rounded-full"></div>
                    </button>

                    <button onclick="cambiarVista('inventario', this)" class="menu-btn w-full flex items-center px-4 py-3 text-blue-200 hover:bg-white/5 rounded-xl font-medium justify-between transition-all mt-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Gestión de Inventario
                        </div>
                        <div class="indicador w-2 h-2 bg-blue-500 rounded-full hidden"></div>
                    </button>
                    
                    <a href="index.php" class="flex items-center px-4 py-3 text-blue-200 hover:bg-white/5 rounded-xl font-medium transition-colors mt-2">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Volver a la Tienda
                    </a>
                </nav>
            </div>
        </div>

        <div class="p-6">
            <div class="text-[10px] text-blue-400/50 mt-6 text-center">
                &copy; 2026 TecNM Campus Huejutla
            </div>
        </div>
    </aside>

    <!-- Área Principal -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Topbar -->
        <header class="bg-white px-8 py-4 border-b border-gray-200 flex items-center justify-between shadow-sm z-10">
            <div class="flex-1 max-w-xl">
                <div class="relative">
                    <input type="text" placeholder="Buscar en la administración..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-10 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                    <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-3 border-l border-slate-200 pl-6 cursor-pointer">
                    <div class="w-9 h-9 bg-tecnm-dark text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-sm">AD</div>
                    <div class="hidden md:block text-sm">
                        <p class="font-bold text-slate-700">Administrador</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Gestión general</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-8 relative">
            
            <?= $mensaje ?>

            <!-- ================= VISTA: RESUMEN DASHBOARD ================= -->
            <div id="vista-resumen" class="vista-activa">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
                        <h1 class="text-3xl font-extrabold text-slate-800">Soporte ITIL & E-commerce</h1>
                        <p class="text-slate-500 mt-1 text-sm">Bienvenido de nuevo. Este es el panorama de tu tienda hoy.</p>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-sm font-medium text-slate-500">Visitas del Sitio</p>
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-800 mb-2">24,892</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-sm font-medium text-slate-500">Ventas del Mes</p>
                            <div class="bg-emerald-50 text-emerald-600 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-800 mb-2">$<?= number_format($ventas_mes, 2) ?></h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-sm font-medium text-slate-500">Tickets Abiertos</p>
                            <div class="bg-orange-50 text-orange-500 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-800 mb-2"><?= $tickets_abiertos ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Gráfica Analítica -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
                    <h3 class="text-lg font-extrabold text-slate-800 mb-6">Tráfico y sesiones</h3>
                    <div class="w-full h-72">
                        <canvas id="graficaTrafico"></canvas>
                    </div>
                </div>

                <!-- Tabla de Tickets Recientes -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-extrabold text-slate-800">Tickets de Soporte Recientes</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-bold">ID</th>
                                    <th class="px-6 py-4 font-bold">Usuario</th>
                                    <th class="px-6 py-4 font-bold">Asunto</th>
                                    <th class="px-6 py-4 font-bold text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                                <?php
                                if($lista_tickets && pg_num_rows($lista_tickets) > 0) {
                                    while($ticket = pg_fetch_assoc($lista_tickets)) {
                                        $estado_clase = (strtolower($ticket['estado']) == 'abierto') ? "bg-green-100 text-green-700" : "bg-gray-100 text-gray-700";
                                        echo '
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-6 py-4 font-mono text-slate-500">#'.str_pad($ticket['id_ticket'], 4, "0", STR_PAD_LEFT).'</td>
                                            <td class="px-6 py-4 font-bold">'.htmlspecialchars($ticket['usuario']).'</td>
                                            <td class="px-6 py-4 truncate max-w-[200px]">'.htmlspecialchars($ticket['asunto']).'</td>
                                            <td class="px-6 py-4 text-center"><span class="px-3 py-1 rounded-full text-xs font-bold uppercase '.$estado_clase.'">'.htmlspecialchars($ticket['estado']).'</span></td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No hay tickets registrados.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- ================= VISTA: INVENTARIO (CRUD) ================= -->
            <div id="vista-inventario" class="vista-oculta">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Catálogo</p>
                        <h1 class="text-3xl font-extrabold text-slate-800">Gestión de Inventario</h1>
                        <p class="text-slate-500 mt-1 text-sm">Agrega, visualiza o edita productos de la tienda virtual.</p>
                    </div>
                    <button onclick="abrirModalAgregar()" class="bg-[#c95d2e] hover:bg-orange-700 px-6 py-3 rounded-xl text-white text-sm font-bold shadow-sm flex items-center transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Producto
                    </button>
                </div>

                <!-- Barra de Búsqueda y Filtro (NUEVO) -->
                <div class="flex flex-col md:flex-row gap-4 mb-6 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex-1 relative">
                        <input type="text" id="buscador-inventario" onkeyup="filtrarTabla()" placeholder="Buscar producto por nombre..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-10 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                        <svg class="absolute left-3 top-3.5 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <div class="md:w-64">
                        <select id="filtro-categoria" onchange="filtrarTabla()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D] font-bold text-slate-600">
                            <option value="todos">Todas las categorías</option>
                            <option value="ropa">Ropa Oficial</option>
                            <option value="útiles">Útiles Escolares</option>
                            <option value="accesorios">Accesorios</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="tabla-inventario">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-bold">Producto</th>
                                    <th class="px-6 py-4 font-bold text-center">Categoría</th>
                                    <th class="px-6 py-4 font-bold text-center">Precio</th>
                                    <th class="px-6 py-4 font-bold text-center">Stock</th>
                                    <th class="px-6 py-4 font-bold text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                                <?php
                                if($lista_productos && pg_num_rows($lista_productos) > 0) {
                                    while($prod = pg_fetch_assoc($lista_productos)) {
                                        $img = !empty($prod['imagen']) ? $prod['imagen'] : 'https://via.placeholder.com/150';
                                        
                                        // Datos seguros para el Modal Editar
                                        $id = $prod['id_producto'];
                                        $nombre_safe = htmlspecialchars($prod['nombre'], ENT_QUOTES);
                                        $cat_safe = htmlspecialchars($prod['categoria'], ENT_QUOTES);
                                        $precio = $prod['precio'];
                                        $stock = $prod['stock'];
                                        $img_safe = htmlspecialchars($img, ENT_QUOTES);
                                        $desc_safe = htmlspecialchars($prod['descripcion'] ?? '', ENT_QUOTES);

                                        // Atributos data-* para el filtro JS
                                        echo '
                                        <tr class="hover:bg-slate-50 transition-colors fila-producto" data-nombre="'.strtolower($nombre_safe).'" data-categoria="'.strtolower($cat_safe).'">
                                            <td class="px-6 py-4 flex items-center space-x-4">
                                                <img src="'.$img_safe.'" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                                                <span class="font-bold text-slate-800">'.$nombre_safe.'</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded text-xs font-bold uppercase">'.$cat_safe.'</span>
                                            </td>
                                            <td class="px-6 py-4 text-center font-extrabold text-emerald-600">$'.number_format($precio, 2).'</td>
                                            <td class="px-6 py-4 text-center font-bold text-slate-600">'.$stock.'</td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end space-x-2">
                                                    <!-- Botón Editar -->
                                                    <button type="button" onclick="abrirModalEditar('.$id.', \''.$nombre_safe.'\', \''.$cat_safe.'\', '.$precio.', '.$stock.', \''.$img_safe.'\', \''.$desc_safe.'\')" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Editar Producto">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    
                                                    <!-- Botón Eliminar -->
                                                    <form action="admin.php" method="POST" onsubmit="return confirm(\'¿Estás seguro de que deseas eliminar este producto?\');">
                                                        <input type="hidden" name="id_producto" value="'.$id.'">
                                                        <button type="submit" name="eliminar_producto" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors inline-flex items-center" title="Eliminar">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr id="fila-vacia"><td colspan="5" class="px-6 py-8 text-center text-slate-500">El catálogo está vacío.</td></tr>';
                                }
                                ?>
                                <tr id="fila-no-resultados" style="display:none;"><td colspan="5" class="px-6 py-8 text-center text-slate-500">No se encontraron productos con esa búsqueda.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script>
        // Navegación del Panel Lateral
        function cambiarVista(vistaId, btn) {
            document.querySelectorAll('.menu-btn').forEach(b => {
                b.classList.remove('bg-white', 'text-tecnm-dark', 'shadow-sm', 'font-bold');
                b.classList.add('text-blue-200', 'hover:bg-white/5', 'font-medium');
                b.querySelector('.indicador').classList.add('hidden');
            });
            btn.classList.remove('text-blue-200', 'hover:bg-white/5', 'font-medium');
            btn.classList.add('bg-white', 'text-tecnm-dark', 'shadow-sm', 'font-bold');
            btn.querySelector('.indicador').classList.remove('hidden');

            document.getElementById('vista-resumen').classList.replace(vistaId === 'resumen' ? 'vista-oculta' : 'vista-activa', vistaId === 'resumen' ? 'vista-activa' : 'vista-oculta');
            document.getElementById('vista-inventario').classList.replace(vistaId === 'inventario' ? 'vista-oculta' : 'vista-activa', vistaId === 'inventario' ? 'vista-activa' : 'vista-oculta');
        }

        // ================= FILTRO Y BUSCADOR JS (SIN RECARGA) =================
        function filtrarTabla() {
            const buscador = document.getElementById('buscador-inventario').value.toLowerCase();
            const categoria = document.getElementById('filtro-categoria').value.toLowerCase();
            const filas = document.querySelectorAll('.fila-producto');
            let visibles = 0;

            filas.forEach(fila => {
                const nombreProd = fila.getAttribute('data-nombre');
                // Quitamos acentos para facilitar la búsqueda
                const catProd = fila.getAttribute('data-categoria').replace('ú', 'u'); 
                const catFiltro = categoria.replace('ú', 'u');

                const coincideBusqueda = nombreProd.includes(buscador);
                const coincideCategoria = (catFiltro === 'todos') || (catProd === catFiltro);

                if (coincideBusqueda && coincideCategoria) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Mostrar mensaje si no hay resultados
            const sinResultados = document.getElementById('fila-no-resultados');
            if(visibles === 0 && filas.length > 0) {
                sinResultados.style.display = '';
            } else {
                sinResultados.style.display = 'none';
            }
        }

        // Control del Modal Agregar
        function abrirModalAgregar() {
            document.getElementById('modal-agregar').classList.remove('hidden');
            document.getElementById('modal-agregar').classList.add('flex');
        }
        function cerrarModalAgregar() {
            document.getElementById('modal-agregar').classList.add('hidden');
            document.getElementById('modal-agregar').classList.remove('flex');
        }

        // Control del Modal Editar
        function abrirModalEditar(id, nombre, categoria, precio, stock, imagen, descripcion) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nombre').value = nombre;
            document.getElementById('edit-categoria').value = categoria;
            document.getElementById('edit-precio').value = precio;
            document.getElementById('edit-stock').value = stock;
            document.getElementById('edit-imagen').value = imagen;
            document.getElementById('edit-descripcion').value = descripcion;

            document.getElementById('modal-editar').classList.remove('hidden');
            document.getElementById('modal-editar').classList.add('flex');
        }
        function cerrarModalEditar() {
            document.getElementById('modal-editar').classList.add('hidden');
            document.getElementById('modal-editar').classList.remove('flex');
        }

        // Gráfica Chart.js
        const ctx = document.getElementById('graficaTrafico').getContext('2d');
        let gradientBlue = ctx.createLinearGradient(0, 0, 0, 300);
        gradientBlue.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradientBlue.addColorStop(1, 'rgba(37, 99, 235, 0)');
        let gradientGreen = ctx.createLinearGradient(0, 0, 0, 300);
        gradientGreen.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradientGreen.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1 Jun', '5 Jun', '9 Jun', '13 Jun', '17 Jun', '21 Jun', '25 Jun', '30 Jun'],
                datasets: [
                    { label: 'Visitas', data: [1000, 1200, 1100, 1500, 1400, 1800, 1700, 2200], borderColor: '#2563EB', backgroundColor: gradientBlue, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#2563EB', pointRadius: 4, fill: true, tension: 0.4 },
                    { label: 'Sesiones', data: [700, 1000, 800, 1300, 1000, 1500, 1300, 1900], borderColor: '#10B981', backgroundColor: gradientGreen, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#10B981', pointRadius: 4, fill: true, tension: 0.4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', align: 'start' } },
                scales: { y: { beginAtZero: true, max: 3000, border: { display: false } }, x: { border: { display: false }, grid: { display: false } } }
            }
        });
    </script>
</body>
</html>