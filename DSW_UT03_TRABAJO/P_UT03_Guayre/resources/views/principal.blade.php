<!DOCTYPE html>
<html lang="es">

<head>
    <title>Librería</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="{{ asset('js/cargarDatos.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <section id="login" class="card mx-auto shadow-sm" style="max-width: 400px;">
            <div class="card-body">
                <h1 class="h4 text-center mb-3">Librería</h1>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input id="usuario" type="text" class="form-control" placeholder="usuario (root@email.com)">
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label">Clave</label>
                        <input id="clave" type="password" class="form-control" placeholder="Contraseña (1234)">
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" onclick="login();">Iniciar Sesión</button>
                    </div>
                </form>
            </div>
        </section>

        <section id="principal" style="display:none;">
            <header id="cabecera" class="mb-4">
                <h1 class="h5">Librería</h1>
                <p><span id="cab_usuario" class="fw-bold">Bienvenido</span></p>
                <nav>
                    <span>Menú: </span>
                    <a href="#" class="text-primary" onclick="cargarGeneros();">Listado de Géneros</a> /
                    <a href="#" class="text-primary" onclick="cargarLibros();">Listado de Libros</a> /
                    <a href="#" class="text-primary" onclick="cargarCarrito();">Ver carrito</a> /
                    <a href="#" class="text-primary" onclick=";">Pedidos</a> /
                    <a href="#" class="text-primary" onclick="obtenerAccesos();">Accesos</a> /
                    <a href="/" class="text-danger" onclick="cerrarSesion();">Cerrar sesión</a>
                </nav>
                <hr>
            </header>

            <section id="generos-section" style="display:none;">
                <h2>Géneros de Libros</h2>
                <div id="contenido"></div>
            </section>

            <!-- Contenido de Libros -->
            <section id="libros-section" style="display:none;">
                <h2>Libros por Género</h2>
                <div id="contenido"></div>
            </section>

            <!-- Contenido del Carrito -->
            <section id="carrito-section" style="display:none;">
                <h2>Tu Carrito</h2>
                <div id="carrito-container" class="mb-3"></div>
                <p id="total-unidades" class="fw-bold"></p>
                <p id="total-articulos" class="fw-bold"></p>
            </section>



        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>