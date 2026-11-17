<?php 
session_start();
require_once 'conexion.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Institucional TecNM Huejutla | Uniformes y Útiles Oficiales</title>
    <meta name="description" content="Tienda oficial del Instituto Tecnológico de Huejutla. Ropa, uniformes por carrera, accesorios y útiles escolares.">
    <meta name="keywords" content="TecNM Huejutla, tienda institucional, uniformes sistemas, útiles escolares Huejutla">
    <meta name="robots" content="index, follow">
    <meta name="google-site-verification" content="VERIFICACION_SEARCH_CONSOLE_TECNM" />
    
    <meta property="og:title" content="Tienda Institucional TecNM Huejutla">
    <meta property="og:description" content="Uniformes oficiales y artículos académicos del Instituto Tecnológico de Huejutla.">
    <meta property="og:type" content="website">

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TU_ID_AQUI"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-TU_ID_AQUI');
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-tecnm { background-color: #1B365D; }
        .text-tecnm-orange { color: #c95d2e; }
        .bg-tecnm-orange { background-color: #c95d2e; }
        .tab-content { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .carrito-scroll::-webkit-scrollbar { width: 6px; }
        .carrito-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        .modal-enter { animation: modalFadeIn 0.3s ease-out forwards; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body class="bg-white font-sans text-slate-800 relative">

    <!-- ================= MODAL LOGIN / REGISTRO (SIN GOOGLE) ================= -->
    <div id="modal-auth" class="fixed inset-0 bg-slate-900/60 z-[70] hidden items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl relative modal-enter">
            <button onclick="cerrarAuth()" class="absolute top-4 right-4 bg-slate-100 p-2 rounded-full hover:bg-red-100 text-slate-500 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div id="form-login-container">
                <h3 class="text-2xl font-extrabold text-[#1B365D] mb-2">Iniciar Sesión</h3>
                <p class="text-xs text-slate-500 mb-6">Accede a tu cuenta institucional TecNM.</p>

                <form action="auth.php" method="POST" class="space-y-4">
                    <input type="hidden" name="accion" value="login">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Correo Institucional</label>
                        <input type="email" name="email" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Contraseña</label>
                        <input type="password" name="password" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D]">
                    </div>
                    <button type="submit" class="w-full bg-[#1B365D] hover:bg-blue-900 text-white font-bold py-3 rounded-xl transition-colors shadow-md mt-2">
                        Iniciar Sesión
                    </button>
                </form>
                <p class="text-center text-xs text-slate-500 mt-6">¿No tienes cuenta? <button onclick="cambiarFormulario('registro')" class="text-tecnm-orange font-bold hover:underline">Regístrate aquí</button></p>
            </div>

            <div id="form-registro-container" class="hidden">
                <h3 class="text-2xl font-extrabold text-[#1B365D] mb-2">Crear Cuenta</h3>
                <p class="text-xs text-slate-500 mb-6">Regístrate para comprar uniformes y útiles.</p>
                
                <form action="auth.php" method="POST" class="space-y-4">
                    <input type="hidden" name="accion" value="registro">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nombre Completo</label>
                        <input type="text" name="nombre" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Contraseña</label>
                        <input type="password" name="password" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#1B365D]">
                    </div>
                    <button type="submit" class="w-full bg-tecnm-orange hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md mt-2">
                        Registrarse
                    </button>
                </form>
                <p class="text-center text-xs text-slate-500 mt-6">¿Ya tienes cuenta? <button onclick="cambiarFormulario('login')" class="text-[#1B365D] font-bold hover:underline">Inicia Sesión</button></p>
            </div>
        </div>
    </div>

    <!-- Modal Detalle del Producto -->
    <div id="modal-producto" class="fixed inset-0 bg-slate-900/60 z-[60] hidden items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto flex flex-col md:flex-row shadow-2xl relative modal-enter">
            <button onclick="cerrarModalProducto()" class="absolute top-4 right-4 bg-slate-100 p-2 rounded-full hover:bg-red-100 text-slate-500 hover:text-red-500 transition-colors z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="md:w-1/2 bg-slate-50 flex items-center justify-center p-8 border-r border-slate-100">
                <img id="modal-img" src="" alt="Detalle del producto TecNM" title="Vista ampliada del producto" class="max-w-full max-h-[400px] object-contain mix-blend-multiply rounded-xl">
            </div>
            <div class="md:w-1/2 p-8 flex flex-col">
                <p id="modal-categoria" class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2"></p>
                <h2 id="modal-nombre" class="text-3xl font-extrabold text-[#1B365D] mb-2 leading-tight"></h2>
                <p id="modal-precio" class="text-4xl font-extrabold text-emerald-600 mb-6"></p>
                <div class="mb-6">
                    <h4 class="font-bold text-slate-800 mb-2">Descripción del producto</h4>
                    <p id="modal-descripcion" class="text-sm text-slate-500 leading-relaxed"></p>
                </div>
                <div class="flex space-x-4 mb-6">
                    <div id="modal-tallas" class="hidden flex-1">
                        <h4 class="font-bold text-slate-800 mb-2 text-sm">Talla</h4>
                        <select id="select-talla" class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none">
                            <option value="CH">CH (Chica)</option>
                            <option value="M">M (Mediana)</option>
                            <option value="G">G (Grande)</option>
                            <option value="XG">XG (Extra Grande)</option>
                        </select>
                    </div>
                    <div id="modal-colores" class="hidden flex-1">
                        <h4 class="font-bold text-slate-800 mb-2 text-sm">Color Oficial</h4>
                        <select id="select-color" class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:outline-none">
                            <option value="Sistemas (Azul)">Sistemas (Azul)</option>
                            <option value="Agronomía (Verde)">Agronomía (Verde)</option>
                            <option value="Gestión (Naranja)">Gestión (Naranja)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-auto pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                            <button onclick="cambiarCantidadModal(-1)" class="px-4 py-2 font-bold hover:bg-slate-200">-</button>
                            <input type="number" id="modal-cantidad" value="1" min="1" class="w-12 text-center bg-transparent font-bold" readonly>
                            <button onclick="cambiarCantidadModal(1)" class="px-4 py-2 font-bold hover:bg-slate-200">+</button>
                        </div>
                        <p id="modal-stock" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg"></p>
                    </div>
                    <button onclick="agregarDesdeModal()" class="w-full bg-[#1B365D] hover:bg-blue-900 text-white font-bold py-4 rounded-xl shadow-lg flex justify-center items-center space-x-2">
                        <span>Agregar al Carrito</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Carrito Lateral -->
    <div id="overlay-carrito" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity" onclick="cerrarCarrito()"></div>
    <div id="sidebar-carrito" class="fixed top-0 right-0 w-full max-w-sm h-full bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-slate-50">
            <h2 class="font-extrabold text-xl text-[#1B365D]">Mi Carrito</h2>
            <button onclick="cerrarCarrito()" class="text-slate-400 hover:text-red-500">✕</button>
        </div>
        <div id="lista-carrito" class="flex-1 overflow-y-auto p-6 carrito-scroll space-y-4"></div>
        <div class="p-6 border-t border-gray-100 bg-slate-50">
            <div class="flex justify-between items-center mb-4">
                <span class="font-bold text-slate-500">Total:</span>
                <span id="total-carrito" class="font-extrabold text-2xl text-emerald-600">$0.00</span>
            </div>
            <button onclick="procesarPago()" class="w-full bg-tecnm-orange hover:bg-orange-700 text-white font-bold py-3 rounded-xl shadow-md">
                Proceder al Pago
            </button>
        </div>
    </div>

    <!-- Barra Anuncios -->
    <div class="bg-tecnm text-white text-xs font-bold text-center py-2 uppercase tracking-widest relative z-30">
        Envío gratis en compras mayores a $899 - Recoge sin costo en campus
    </div>

    <!-- Header Principal -->
    <header id="main-header" class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm transition-all duration-300 border-b border-transparent">
        <div id="header-content" class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3 cursor-pointer" onclick="document.querySelector('[data-tab=\'inicio\']').click()">
                <img src="src/original_Logo-TecNM-2017.png" alt="Logo Oficial TecNM Huejutla" title="Tienda Institucional TecNM Huejutla" class="h-12 w-auto object-contain">
                <div>
                    <h1 class="font-extrabold text-lg leading-tight">TecNM Huejutla</h1>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest">Tienda Institucional</p>
                </div>
            </div>
            <div class="flex-1 max-w-2xl mx-8">
                <div class="relative">
                    <input type="text" id="buscador-principal" onkeyup="filtrarProductosTienda()" placeholder="¿Qué estás buscando?" class="w-full bg-slate-100 rounded-full py-3 px-6 text-sm focus:outline-none">
                    <svg class="absolute right-4 top-3 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <div class="flex items-center space-x-6 text-slate-700">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 font-bold text-sm text-[#1B365D] focus:outline-none py-2">
                            <div class="w-8 h-8 bg-[#1B365D] text-white rounded-full flex items-center justify-center text-xs">
                                <?= strtoupper(substr($_SESSION['usuario'], 0, 1)) ?>
                            </div>
                            <span><?= htmlspecialchars($_SESSION['usuario']) ?></span>
                        </button>
                        <div class="absolute right-0 mt-1 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 hidden group-hover:block z-50">
                            <a href="admin.php" class="block px-4 py-2.5 text-sm font-bold text-blue-600 hover:bg-slate-50">⚙️ Panel Admin</a>
                            <a href="soporte.php" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">🛠️ Mesa de Ayuda</a>
                            <form action="auth.php" method="POST">
                                <input type="hidden" name="accion" value="logout">
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50">Cerrar Sesión</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <button onclick="abrirAuth('login')" class="bg-[#1B365D] hover:bg-blue-900 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                        Iniciar Sesión
                    </button>
                <?php endif; ?>

                <button onclick="abrirCarrito()" class="relative hover:text-[#1B365D] transition-colors focus:outline-none" title="Abrir Carrito">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span id="contador-carrito" class="absolute -top-1 -right-2 bg-tecnm-orange text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Navegación por pestañas -->
    <nav class="border-b border-gray-100 pb-4 mb-6 bg-white">
        <ul class="flex flex-wrap justify-center space-x-8 text-sm font-bold text-slate-500">
            <li data-tab="inicio" class="tab-btn text-tecnm-orange border-b-2 border-[#c95d2e] pb-4 -mb-[17px] cursor-pointer">Inicio</li>
            <li data-tab="ropa" class="tab-btn hover:text-[#1B365D] border-b-2 border-transparent pb-4 -mb-[17px] cursor-pointer">Ropa oficial</li>
            <li data-tab="utiles" class="tab-btn hover:text-[#1B365D] border-b-2 border-transparent pb-4 -mb-[17px] cursor-pointer">Útiles escolares</li>
            <li data-tab="accesorios" class="tab-btn hover:text-[#1B365D] border-b-2 border-transparent pb-4 -mb-[17px] cursor-pointer">Accesorios</li>
            <li data-tab="carreras" class="tab-btn hover:text-[#1B365D] border-b-2 border-transparent pb-4 -mb-[17px] cursor-pointer">Uniformes por carrera</li>
            <li data-tab="comunidad" class="tab-btn hover:text-[#1B365D] border-b-2 border-transparent pb-4 -mb-[17px] cursor-pointer">Blog y Comunidad</li>
        </ul>
    </nav>

    <main class="max-w-7xl mx-auto px-6 min-h-[500px]">
        <?php
        $todos_los_productos = [];
        $query = "SELECT * FROM jh_productos WHERE stock > 0 ORDER BY id_producto ASC";
        $result = @pg_query($conexion, $query);
        if($result) { while ($p = pg_fetch_assoc($result)) { $todos_los_productos[] = $p; } }

        function renderProducto($producto) {
            $img = !empty($producto['imagen']) ? $producto['imagen'] : 'https://via.placeholder.com/500x500.png?text=Sin+Imagen';
            $badge = ($producto['categoria'] == 'Ropa') ? '<span class="absolute top-3 left-3 bg-tecnm-orange text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shadow-sm z-10">Más vendido</span>' : '<span class="absolute top-3 left-3 bg-[#1B365D] text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shadow-sm z-10">Nuevo</span>';

            $id_safe = $producto['id_producto'];
            $nombre_safe = htmlspecialchars($producto['nombre'], ENT_QUOTES);
            $precio_safe = $producto['precio'];
            $cat_safe = htmlspecialchars($producto['categoria'], ENT_QUOTES);
            $desc_safe = isset($producto['descripcion']) ? htmlspecialchars($producto['descripcion'], ENT_QUOTES) : 'Sin descripción.';
            $stock = $producto['stock'];

            echo '
            <div onclick="abrirModalProducto('.$id_safe.', \''.$nombre_safe.'\', '.$precio_safe.', \''.$img.'\', \''.$cat_safe.'\', \''.$desc_safe.'\', '.$stock.')" class="card-producto group border border-slate-200 rounded-3xl p-4 hover:shadow-xl cursor-pointer transition-all bg-white flex flex-col" data-nombre="'.strtolower($nombre_safe).'">
                <div class="relative rounded-2xl overflow-hidden bg-slate-100 aspect-square mb-4">
                    <img src="'.$img.'" alt="'.$nombre_safe.' - Tienda TecNM Huejutla" title="'.$nombre_safe.'" loading="lazy" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700">
                    '.$badge.'
                </div>
                <div class="flex-1 flex flex-col pointer-events-none">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">'.$cat_safe.'</span>
                        <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded">Stock: '.$stock.'</span>
                    </div>
                    <h3 class="font-extrabold text-slate-800 leading-tight mb-3">'.$nombre_safe.'</h3>
                    <div class="mt-auto">
                        <p class="text-emerald-600 font-extrabold text-xl mb-4">$'.number_format($precio_safe, 2).'</p>
                        <button class="w-full bg-slate-100 group-hover:bg-tecnm group-hover:text-white text-slate-600 font-bold py-3 rounded-xl transition-colors pointer-events-auto">
                            <span>Ver detalles y comprar</span>
                        </button>
                    </div>
                </div>
            </div>';
        }
        ?>

        <!-- Pestaña Inicio -->
        <section id="inicio" class="tab-content block">
            <div class="relative bg-slate-800 rounded-3xl overflow-hidden h-[400px] flex items-center mb-16 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-r from-[#1B365D] to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop" alt="Comunidad estudiantil TecNM Huejutla" title="Colección Institucional TecNM 2026" class="absolute inset-0 w-full h-full object-cover opacity-60">
                <div class="relative z-20 p-12 max-w-xl text-white">
                    <span class="bg-tecnm-orange text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block">Nueva Colección 2026</span>
                    <h2 class="text-5xl font-extrabold mb-4 leading-tight">Nuevos uniformes<br>de <span class="text-tecnm-orange">Sistemas</span></h2>
                    <p class="text-slate-200 mb-8 text-lg">Viste con orgullo tu identidad TecNM. Prendas oficiales y de calidad.</p>
                    <button onclick="document.querySelector('[data-tab=\'ropa\']').click()" class="bg-tecnm-orange hover:bg-orange-700 px-8 py-3 rounded-full font-bold transition-colors">Ir al catálogo →</button>
                </div>
            </div>
            <div class="mb-16">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Selección del campus</h3>
                <h2 class="text-2xl font-extrabold text-slate-800 mb-8">Productos destacados</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php $c=0; foreach ($todos_los_productos as $p) { if($c>=4) break; renderProducto($p); $c++; } ?>
                </div>
            </div>
        </section>

        <!-- Pestaña Ropa -->
        <section id="ropa" class="tab-content hidden mb-16">
            <h2 class="text-3xl font-extrabold text-[#1B365D] mb-2 border-b pb-4">Ropa Oficial Institucional</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                <?php foreach ($todos_los_productos as $p) { if (strtolower($p['categoria']) == 'ropa') renderProducto($p); } ?>
            </div>
        </section>

        <!-- Pestaña Útiles -->
        <section id="utiles" class="tab-content hidden mb-16">
            <h2 class="text-3xl font-extrabold text-[#1B365D] mb-2 border-b pb-4">Útiles Escolares</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                <?php foreach ($todos_los_productos as $p) { if (strtolower($p['categoria']) == 'útiles' || strtolower($p['categoria']) == 'utiles') renderProducto($p); } ?>
            </div>
        </section>

        <!-- Pestaña Accesorios -->
        <section id="accesorios" class="tab-content hidden mb-16">
            <h2 class="text-3xl font-extrabold text-[#1B365D] mb-2 border-b pb-4">Accesorios TecNM</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                <?php foreach ($todos_los_productos as $p) { if (strtolower($p['categoria']) == 'accesorios') renderProducto($p); } ?>
            </div>
        </section>

        <!-- Pestaña Carreras -->
        <section id="carreras" class="tab-content hidden mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-[#1B365D] mb-4">Uniformes por Carrera</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Selecciona tu ingeniería para ver las prendas asignadas.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div onclick="document.querySelector('[data-tab=\'ropa\']').click()" class="border border-slate-200 p-8 rounded-3xl text-center hover:shadow-xl cursor-pointer bg-white group">
                    <div class="bg-blue-50 w-20 h-20 mx-auto rounded-full flex items-center justify-center text-4xl mb-6">💻</div>
                    <h3 class="font-extrabold text-xl text-[#1B365D] mb-2">Sistemas Computacionales</h3>
                    <p class="text-slate-500 text-sm mb-4">Color oficial: Azul Marino</p>
                </div>
                <div onclick="document.querySelector('[data-tab=\'ropa\']').click()" class="border border-slate-200 p-8 rounded-3xl text-center hover:shadow-xl cursor-pointer bg-white group">
                    <div class="bg-green-50 w-20 h-20 mx-auto rounded-full flex items-center justify-center text-4xl mb-6">🌱</div>
                    <h3 class="font-extrabold text-xl text-[#1B365D] mb-2">Agronomía</h3>
                    <p class="text-slate-500 text-sm mb-4">Color oficial: Verde Esmeralda</p>
                </div>
                <div onclick="document.querySelector('[data-tab=\'ropa\']').click()" class="border border-slate-200 p-8 rounded-3xl text-center hover:shadow-xl cursor-pointer bg-white group">
                    <div class="bg-orange-50 w-20 h-20 mx-auto rounded-full flex items-center justify-center text-4xl mb-6">📊</div>
                    <h3 class="font-extrabold text-xl text-[#1B365D] mb-2">Gestión Empresarial</h3>
                    <p class="text-slate-500 text-sm mb-4">Color oficial: Naranja</p>
                </div>
            </div>
        </section>

        <!-- Pestaña Comunidad, Blog, Share Social y Webconferences (Módulos 1.3, 2.1 y 2.3) -->
        <section id="comunidad" class="tab-content hidden mb-16">
            <h2 class="text-3xl font-extrabold text-[#1B365D] mb-6 border-b pb-4">Blog Institucional y Canales de Colaboración</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <article class="border border-slate-200 rounded-3xl p-6 bg-white shadow-sm">
                    <span class="text-xs font-bold text-tecnm-orange uppercase">Artículo Optimizado SEO</span>
                    <h3 class="text-xl font-extrabold text-slate-800 mt-1 mb-3">Lineamientos de Identidad Institucional para Congresos y Visitas Industriales</h3>
                    <p class="text-sm text-slate-600 mb-4 leading-relaxed">Conoce las prendas oficiales requeridas para representar al TecNM Campus Huejutla en eventos académicos nacionales, hackathons y prácticas profesionales.</p>
                    <div class="flex items-center space-x-4 pt-4 border-t border-slate-100 text-xs font-bold">
                        <span class="text-slate-400">Compartir entrada:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=https://huejutla.tecnm.mx" target="_blank" class="text-blue-600 hover:underline">Facebook</a>
                        <a href="https://twitter.com/intent/tweet?text=Tienda+Institucional+TecNM+Huejutla" target="_blank" class="text-sky-500 hover:underline">X (Twitter)</a>
                        <a href="https://api.whatsapp.com/send?text=Tienda+Oficial+TecNM+Huejutla" target="_blank" class="text-emerald-600 hover:underline">WhatsApp</a>
                    </div>
                </article>

                <article class="border border-slate-200 rounded-3xl p-6 bg-slate-50 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase">Webconferences y Atención en Vivo</span>
                        <h3 class="text-xl font-extrabold text-[#1B365D] mt-1 mb-3">Sesiones de Atención para Pedidos por Grupo</h3>
                        <p class="text-sm text-slate-600 mb-4">Únete a nuestras salas de videoconferencia (Google Meet / Microsoft Teams) para coordinar pedidos de generación, bordados personalizados o soporte en vivo.</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="https://meet.google.com/" target="_blank" class="bg-[#1B365D] text-white text-xs font-bold px-4 py-3 rounded-xl hover:bg-blue-900 transition-colors">🎥 Sala Google Meet</a>
                        <a href="soporte.php" class="bg-tecnm-orange text-white text-xs font-bold px-4 py-3 rounded-xl hover:bg-orange-700 transition-colors">🛠️ Mesa de Ayuda ITIL</a>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-tecnm text-white pt-16 pb-8 mt-8">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-blue-800 pb-12 mb-8">
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <img src="src/original_Logo-TecNM-2017.png" alt="Logo TecNM Huejutla" title="TecNM Campus Huejutla" class="h-10 w-auto bg-white p-1 rounded">
                    <div><h4 class="font-extrabold text-lg leading-tight">TecNM Huejutla</h4></div>
                </div>
                <p class="text-xs text-blue-200">Tienda oficial del Instituto Tecnológico de Huejutla.</p>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-6 text-white">Información</h4>
                <ul class="space-y-3 text-sm text-blue-200">
                    <li><a href="admin.php" class="hover:text-white">Acceso administrativo</a></li>
                    <li><a href="soporte.php" class="hover:text-white">Mesa de Ayuda (ITIL)</a></li>
                    <li><a href="sitemap.php" target="_blank" class="hover:text-white">Sitemap XML Dinámico</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-6 text-white">Redes Corporativas</h4>
                <ul class="space-y-3 text-sm text-blue-200">
                    <li><a href="https://www.facebook.com/" target="_blank" class="hover:text-white">Facebook Oficial</a></li>
                    <li><a href="https://huejutla.tecnm.mx" target="_blank" class="hover:text-white">Portal Institucional</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 text-center text-xs text-blue-400">
            &copy; 2026 TecNM Campus Huejutla - Proyecto GTE.
        </div>
    </footer>

    <!-- Scripts de Interacción -->
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('text-tecnm-orange', 'border-[#c95d2e]');
                    b.classList.add('border-transparent');
                });
                btn.classList.add('text-tecnm-orange', 'border-[#c95d2e]');
                document.querySelectorAll('.tab-content').forEach(c => c.classList.replace('block', 'hidden'));
                document.getElementById(btn.getAttribute('data-tab')).classList.replace('hidden', 'block');
                window.scrollTo({ top: 100, behavior: 'smooth' });
            });
        });

        function filtrarProductosTienda() {
            const q = document.getElementById('buscador-principal').value.toLowerCase();
            document.querySelectorAll('.card-producto').forEach(card => {
                card.style.display = card.getAttribute('data-nombre').includes(q) ? '' : 'none';
            });
        }

        function abrirAuth(tipo) {
            document.getElementById('modal-auth').classList.remove('hidden');
            document.getElementById('modal-auth').classList.add('flex');
            cambiarFormulario(tipo);
        }
        function cerrarAuth() {
            document.getElementById('modal-auth').classList.add('hidden');
            document.getElementById('modal-auth').classList.remove('flex');
        }
        function cambiarFormulario(tipo) {
            if(tipo === 'login') {
                document.getElementById('form-login-container').classList.remove('hidden');
                document.getElementById('form-registro-container').classList.add('hidden');
            } else {
                document.getElementById('form-login-container').classList.add('hidden');
                document.getElementById('form-registro-container').classList.remove('hidden');
            }
        }

        let productoActual = null;
        function abrirModalProducto(id, nombre, precio, img, categoria, descripcion, stock) {
            productoActual = { id, nombre, precio, img, categoria, stock };
            document.getElementById('modal-img').src = img;
            document.getElementById('modal-categoria').innerText = categoria;
            document.getElementById('modal-nombre').innerText = nombre;
            document.getElementById('modal-precio').innerText = '$' + parseFloat(precio).toFixed(2);
            document.getElementById('modal-descripcion').innerText = descripcion;
            document.getElementById('modal-stock').innerText = `Stock: ${stock}`;
            document.getElementById('modal-cantidad').value = 1;

            if (categoria.toLowerCase() === 'ropa') {
                document.getElementById('modal-tallas').classList.remove('hidden');
                document.getElementById('modal-colores').classList.remove('hidden');
            } else {
                document.getElementById('modal-tallas').classList.add('hidden');
                document.getElementById('modal-colores').classList.add('hidden');
            }
            document.getElementById('modal-producto').classList.remove('hidden');
            document.getElementById('modal-producto').classList.add('flex');
        }
        function cerrarModalProducto() { document.getElementById('modal-producto').classList.add('hidden'); }
        function cambiarCantidadModal(cambio) {
            let input = document.getElementById('modal-cantidad');
            let nueva = parseInt(input.value) + cambio;
            if (nueva >= 1 && nueva <= productoActual.stock) input.value = nueva;
        }
        function agregarDesdeModal() {
            let cantidad = parseInt(document.getElementById('modal-cantidad').value);
            let nombreVar = productoActual.nombre;
            if (productoActual.categoria.toLowerCase() === 'ropa') {
                nombreVar += ` (Talla: ${document.getElementById('select-talla').value}, ${document.getElementById('select-color').value})`;
            }
            agregarAlCarrito(productoActual.id, nombreVar, productoActual.precio, productoActual.img, cantidad);
            cerrarModalProducto();
        }

        let carrito = JSON.parse(localStorage.getItem('carritoTecNM')) || [];
        function guardarCarrito() { localStorage.setItem('carritoTecNM', JSON.stringify(carrito)); }
        function agregarAlCarrito(id, nombre, precio, img, cant) {
            const item = carrito.find(p => p.nombre === nombre && p.id === id);
            if (item) item.cantidad += cant;
            else carrito.push({ id, nombre, precio, img, cantidad: cant });
            guardarCarrito();
            actualizarUI();
            abrirCarrito();
        }
        function eliminarDelCarrito(nombre) {
            carrito = carrito.filter(p => p.nombre !== nombre);
            guardarCarrito();
            actualizarUI();
        }
        function actualizarUI() {
            const lista = document.getElementById('lista-carrito');
            let total = 0, cantTotal = 0;
            lista.innerHTML = '';
            if (carrito.length === 0) {
                lista.innerHTML = '<div class="text-center text-slate-400 mt-10">🛒 Tu carrito está vacío</div>';
            } else {
                carrito.forEach(p => {
                    total += p.precio * p.cantidad;
                    cantTotal += p.cantidad;
                    lista.innerHTML += `
                        <div class="flex items-center space-x-4 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                            <img src="${p.img}" alt="${p.nombre}" title="${p.nombre}" class="w-16 h-16 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800 text-sm">${p.nombre}</h4>
                                <p class="text-emerald-600 font-extrabold text-sm">$${p.precio.toFixed(2)} (x${p.cantidad})</p>
                            </div>
                            <button onclick="eliminarDelCarrito('${p.nombre}')" class="text-red-400 p-2">✕</button>
                        </div>`;
                });
            }
            document.getElementById('total-carrito').innerText = '$' + total.toFixed(2);
            document.getElementById('contador-carrito').innerText = cantTotal;
        }
        function abrirCarrito() { document.getElementById('sidebar-carrito').classList.remove('translate-x-full'); document.getElementById('overlay-carrito').classList.remove('hidden'); }
        function cerrarCarrito() { document.getElementById('sidebar-carrito').classList.add('translate-x-full'); document.getElementById('overlay-carrito').classList.add('hidden'); }
        function procesarPago() {
            if (carrito.length === 0) return alert('El carrito está vacío');
            let total = carrito.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);
            fetch('guardar_pedido.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ carrito: carrito, total: total })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('¡Pedido registrado correctamente en la Base de Datos!');
                    carrito = []; guardarCarrito(); window.location.reload();
                } else { alert('Error al procesar el pago.'); }
            });
        }
        actualizarUI();
    </script>
</body>
</html>