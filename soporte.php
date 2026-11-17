<?php 
require_once 'conexion.php'; 
$mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = pg_escape_string($conexion, $_POST['usuario']);
    $asunto = pg_escape_string($conexion, $_POST['asunto']);
    $descripcion = pg_escape_string($conexion, $_POST['descripcion']);
    
    $query = "INSERT INTO jh_tickets (usuario, asunto, descripcion) VALUES ('$usuario', '$asunto', '$descripcion')";
    $result = pg_query($conexion, $query);
    
    if ($result) {
        $mensaje = '<div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl mb-6 font-bold">✅ Ticket generado exitosamente. Un agente de soporte ITIL lo atenderá pronto.</div>';
    } else {
        $mensaje = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-bold">❌ Error al generar el ticket de soporte.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SEO On-Page (Módulo 1) -->
    <title>Mesa de Ayuda ITIL | Tienda TecNM Huejutla</title>
    <meta name="description" content="Portal de soporte técnico y atención a incidentes para usuarios de la Tienda Oficial del TecNM Huejutla.">
    
    <!-- Google Analytics (Módulo 2) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TU_ID_AQUI"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-TU_ID_AQUI');
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-tecnm-dark { background-color: #1B365D; }
        .bg-tecnm-mid { background-color: #24487b; }
    </style>
</head>
<body class="bg-[#f4f6f9] min-h-screen font-sans flex flex-col">

    <!-- Encabezado Original -->
    <header class="bg-tecnm-dark text-white px-10 py-5 flex justify-between items-center shadow-lg z-20">
        <div class="flex items-center space-x-6">
            <div class="bg-white px-4 py-2 rounded-xl shadow-md flex items-center justify-center h-16 w-48">
                <img src="src/original_Logo-TecNM-2017.webp" alt="Logotipo Oficial TecNM" class="h-12 w-auto object-contain">
            </div>
            <div>
                <h1 class="text-3xl font-bold tracking-wide">Instituto Tecnológico de Huejutla</h1>
                <p class="text-xl text-blue-200 font-light mt-0.5">Gestión de Servicios de TI - Mesa de Ayuda</p>
            </div>
        </div>
        <div class="text-right">
            <a href="index.php" class="bg-[#c95d2e] px-6 py-2 rounded-lg text-white font-bold uppercase tracking-wider hover:bg-orange-700 transition-colors shadow-sm">
                ⬅ Volver a la Tienda
            </a>
        </div>
    </header>

    <!-- Contenido Principal del Formulario ITIL -->
    <main class="flex-1 p-8 flex justify-center items-start mt-8">
        <section class="w-full max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden p-8">
            <h2 class="text-2xl font-extrabold text-[#1B365D] mb-2">Levantar Ticket de Soporte</h2>
            <p class="text-slate-500 mb-6">Complete el formulario para reportar una incidencia o solicitar ayuda con un pedido.</p>
            
            <?= $mensaje ?>

            <form action="soporte.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Nombre de Usuario o Alumno</label>
                    <input type="text" name="usuario" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                </div>
                
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Asunto del Problema</label>
                    <select name="asunto" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]">
                        <option value="Problema con un pedido">Problema con un pedido</option>
                        <option value="Error en la página web">Error en la página web</option>
                        <option value="Duda sobre stock o tallas">Duda sobre stock o tallas</option>
                        <option value="Soporte Técnico General">Soporte Técnico General</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-2">Descripción Detallada</label>
                    <textarea name="descripcion" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#1B365D] focus:ring-1 focus:ring-[#1B365D]"></textarea>
                </div>

                <div class="pt-4 text-right">
                    <button type="submit" class="bg-[#1B365D] text-white px-8 py-3 rounded-xl font-bold uppercase tracking-wider hover:bg-[#24487b] transition-colors shadow-sm">
                        Enviar Ticket
                    </button>
                </div>
            </form>
        </section>
    </main>

</body>
</html>