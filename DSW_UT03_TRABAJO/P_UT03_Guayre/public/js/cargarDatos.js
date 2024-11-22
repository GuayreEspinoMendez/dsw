// LOGIN y LOGOUT 🔐👤

async function login() {
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        const usuario = document.getElementById("usuario").value;
        const clave = document.getElementById("clave").value;
        const params = new URLSearchParams();
        params.append("usuario", usuario);
        params.append("clave", clave);

        const response = await fetch("login_json", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: params.toString(),
        });

        const data = await response.json();  // Espera una respuesta JSON

        if (data.success) {
            // Si el login es exitoso, muestra la vista principal
            document.getElementById("principal").style.display = "block";
            document.getElementById("login").style.display = "none";
            document.getElementById("cab_usuario").innerHTML = "Usuario: " + data.username;
        } else {
            alert(data.message || "Revise usuario y contraseña");
        }
    } catch (error) {
        console.error("Error en la petición:", error);
    }
    return false;
}


async function cerrarSesion() {
    try {
        const response = await fetch("logout_json", {
            method: "GET",
        });

        const data = await response.json();  // Espera una respuesta JSON

        if (data.success) {
            alert("Sesión cerrada con éxito");
            document.getElementById("principal").style.display = "none";
            document.getElementById("login").style.display = "block";
            document.getElementById("contenido").innerHTML = "";
        } else {
            alert("Error: No se pudo cerrar la sesión");
        }
    } catch (error) {
        console.error("Error en la petición:", error);
    }
}


async function checkearSesion() {
    try {
        const response = await fetch("check_sesion", {
            method: "GET",
        });

        const data = await response.json();  // Asume que devuelves un JSON

        if (data.success) {
            document.getElementById("principal").style.display = "block";
            document.getElementById("login").style.display = "none";
            document.getElementById("cab_usuario").innerHTML = "Usuario: " + data.username;
        } else {
            document.getElementById("principal").style.display = "none";
            document.getElementById("login").style.display = "block";
        }
    } catch (error) {
        console.error("Error al verificar la sesión:", error);
    }
}
checkearSesion();

/**
 *
 * CARGAR LIBROS Y GENEROS 🔋📚
 *
 */

function cargarGeneros() {
    fetch("/generos")
        .then((response) => response.json()) 
        .then((generos) => {
            const contenedorGeneros = document.getElementById("contenido");
            contenedorGeneros.innerHTML = ""; 

            const contenedorMenu = document.createElement("ul");
            contenedorMenu.setAttribute("class", "list-unstyled");

            generos.forEach((genero) => {
                const item = document.createElement("li");
                item.setAttribute("class", "list-group-item");
                const enlace = document.createElement("a");
                enlace.setAttribute("href", "#");
                enlace.innerText = genero.nombre;
                enlace.addEventListener("click", function () {
                    cargarGeneroLibros(genero.nombre); 
                });
                item.appendChild(enlace);
                contenedorMenu.appendChild(item);
            });

            contenedorGeneros.appendChild(contenedorMenu);

            document.getElementById("generos-section").style.display = "block";
            document.getElementById("libros-section").style.display = "none";
        })
        .catch((error) => {
            console.error("Error al cargar los géneros:", error);
        });
}

async function cargarGeneroLibros(genero) {
    try {
        const response = await fetch(
            `/libros/genero/${encodeURIComponent(genero)}`
        );

        if (!response.ok) {
            throw new Error(`Error al cargar los libros: ${response.status}`);
        }

        const libros = await response.json();

        if (libros.error) {
            alert(libros.error); 
            return;
        }

        const listaLibros = document.getElementById("contenido");
        listaLibros.innerHTML = ""; 

        const tabla = document.createElement("table");
        tabla.classList.add("table", "table-striped", "table-bordered");

        const encabezado = document.createElement("thead");
        encabezado.innerHTML = `
            <tr>
                <th>ISBN</th>
                <th>Título</th>
                <th>Escritores</th>
                <th>Género</th>
                <th>Páginas</th>
                <th>Imagen</th>
                <th>Operaciones</th>
            </tr>
        `;
        tabla.appendChild(encabezado);

        const cuerpo = document.createElement("tbody");

        libros.forEach((libro) => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                    <td>${libro.isbn}</td>
                    <td>${libro.titulo}</td>
                    <td>${libro.escritores}</td>
                    <td>${libro.genero}</td>
                    <td>${libro.numpaginas}</td>
                    <td><img src="${libro.imagen}" alt="${libro.titulo}" width="70"></td>
                    <td>
                        <form method="POST" action="/carrito/agregar">
                            <input type="number" name="cantidad" value="1" min="1" class="form-control" style="width: 60px;">
                            <input type="hidden" name="isbn" value="${libro.isbn}">
                            <button type="submit" class="btn btn-success mt-2">+</button>
                        </form>
                    </td>
                `;
            cuerpo.appendChild(fila);
        });

        tabla.appendChild(cuerpo);
        listaLibros.appendChild(tabla);
    } catch (error) {
        console.error("Error al cargar los libros:", error);
        alert("Error al cargar los libros");
    }
}
async function cargarLibros() {
    try {
        const response = await fetch("/libros");

        if (response.ok) {
            
            const libros = await response.json();

            const contenido = document.getElementById("contenido");
            contenido.innerHTML = "";

            const tableContainer = document.createElement("div");
            tableContainer.className = "table-responsive";

            const table = document.createElement("table");
            table.className = "table table-striped align-middle";

            const thead = `
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Escritores</th>
                        <th>Género</th>
                        <th>Páginas</th>
                        <th>Imagen</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>`;
            table.innerHTML = thead;

            const tbody = document.createElement("tbody");
            libros.forEach((libro) => {
                const fila = document.createElement("tr");

                fila.innerHTML = `
                    <td>${libro.isbn}</td>
                    <td>${libro.titulo}</td>
                    <td>${libro.escritores}</td>
                    <td>${libro.genero}</td>
                    <td>${libro.numpaginas}</td>
                    <td><img src="${libro.imagen}" alt="${libro.titulo}" style="width:50px;"></td>
                    <td>
                        <form class="d-flex align-items-center">
                            <input type="number" min="1" value="1" class="form-control w-50 me-2">
                            <button type="button" class="btn btn-success" onclick="anadirLibros('${libro.isbn}')">
                                +
                            </button>
                        </form>
                    </td>
                `;

                tbody.appendChild(fila);
            });

            table.appendChild(tbody);
            tableContainer.appendChild(table);
            contenido.appendChild(tableContainer);

            document.getElementById("generos-section").style.display = "block";
            document.getElementById("libros-section").style.display = "none";
        } else {
            console.error("Error al cargar los libros: ", response.status);
        }
    } catch (error) {
        console.error("Error en la petición de libros: ", error);
    }
}

function obtenerAccesos() {
    console.log("Cargando accesos...");
    fetch('/accesos')
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor");
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error("Error al cargar los accesos:", data.error);
                return;
            }

            console.log("Datos recibidos:", data);

            const contenido = document.getElementById('contenido');
            contenido.innerHTML = ""; 

            const table = document.createElement('table');
            table.className = 'table table-striped';

            const thead = `
            <thead>
                <tr>
                    <th>ID Sesión</th>
                    <th>Usuario</th>
                    <th>Inicio Sesión</th>
                    <th>Finalización Sesión</th>
                </tr>
            </thead>`;
            table.innerHTML = thead;

            const tbody = document.createElement('tbody');
            if (data.accesos && data.accesos.length > 0) {
                data.accesos.forEach(acceso => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                    <td>${acceso.idSesion}</td>
                    <td>${acceso.usuario}</td>
                    <td>${acceso.inicioSesion}</td>
                    <td>${acceso.finalSesion || 'En sesión'}</td>
                `;
                    tbody.appendChild(fila);
                });
            } else {
                const fila = document.createElement('tr');
                fila.innerHTML = "<td colspan='4'>No hay accesos registrados</td>";
                tbody.appendChild(fila);
            }

            table.appendChild(tbody);
            contenido.appendChild(table);


            document.getElementById("generos-section").style.display = "block";
            document.getElementById("libros-section").style.display = "none";
        })
        .catch(error => {
            console.error("Error en la solicitud de accesos:", error);
        });

}


/**
 *
 *  CRUD de CARRITO 🛒
 *
 */

async function anadirLibros(isbn) {
    const cantidadInput = document.getElementById(`cantidad-${isbn}`); 
    const cantidad = cantidadInput ? cantidadInput.value : 1; 

    try {
        const response = await fetch(`/carrito/agregar/${isbn}/${cantidad}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            alert("Libro añadido correctamente");
            const data = await response.json();
            console.log('Carrito actualizado: ', data.carrito);
            const carritoNumero = document.getElementById('carrito-numero');
            if (carritoNumero) {
                carritoNumero.textContent = data.carrito.length; 
            }
        } else {
            console.error('Error al añadir producto al carrito');
        }
    } catch (error) {
        console.error('Error en la petición: ', error);
    }
}
async function eliminarLibro(isbn) {
    try {
        const response = await fetch(`/carrito/eliminar/${isbn}`, {
            method: 'DELETE', 
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            const data = await response.json();
            console.log('Carrito actualizado: ', data.carrito);

            cargarCarrito();  

            alert("Producto eliminado del carrito");
        } else {
            console.error('Error al eliminar producto del carrito');
            alert('Hubo un error al eliminar el libro del carrito');
        }
    } catch (error) {
        console.error('Error en la petición: ', error);
        alert('Error en la comunicación con el servidor');
    }
}

async function cargarCarrito() {
    try {
        const response = await fetch('/carrito/cargar', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            const data = await response.json();
            console.log("Carrito actualizado:", data); 

            const carrito = data.carrito || {};  
            const productos = carrito.productos || {};  

            const libros = Object.values(productos); 

            const contenido = document.getElementById("contenido");
            contenido.innerHTML = "";  

            if (libros.length === 0) {
                const mensaje = document.createElement("div");
                mensaje.className = "alert alert-info";
                mensaje.textContent = "No hay productos en el carrito.";
                contenido.appendChild(mensaje);
                return;  
            }

            const resumenContainer = document.createElement("div");
            resumenContainer.className = "resumen-carrito";
            
            const ulResumen = document.createElement("ul");

            const numArticulos = carrito.numarticulos || 0;
            const numUnidades = carrito.numunidades || 0;

            const liArticulos = document.createElement("li");
            liArticulos.textContent = `Número de artículos: ${numArticulos}`;
            const liUnidades = document.createElement("li");
            liUnidades.textContent = `Número de unidades: ${numUnidades}`;

            ulResumen.appendChild(liArticulos);
            ulResumen.appendChild(liUnidades);

            resumenContainer.appendChild(ulResumen);

            contenido.appendChild(resumenContainer);

            const tableContainer = document.createElement("div");
            tableContainer.className = "table-responsive";

            const table = document.createElement("table");
            table.className = "table table-striped align-middle";

            const thead = `
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Escritores</th>
                        <th>Género</th>
                        <th>Páginas</th>
                        <th>Imagen</th>
                        <th>Unidades</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>`;
            table.innerHTML = thead;

            const tbody = document.createElement("tbody");
            libros.forEach((libro) => {
                const fila = document.createElement("tr");

                const unidadesInput = `
                    <input type="number" value="${libro.unidades}" min="1" 
                           onchange="actualizarUnidades('${libro.isbn}', this.value)" 
                           class="form-control" style="width: 60px;">
                `;

                fila.innerHTML = `
                    <td>${libro.isbn}</td>
                    <td>${libro.titulo}</td>
                    <td>${libro.escritores}</td>
                    <td>${libro.genero}</td>
                    <td>${libro.numpaginas}</td>
                    <td><img src="${libro.imagen}" alt="${libro.titulo}" style="width:50px;"></td>
                    <td>${unidadesInput}</td>
                    <td>
                    <form class="d-flex align-items-center">
                            <input type="number" min="1" value="1" class="form-control w-50 me-2">
                            <td>
                            <button type="button" class="btn btn-success" onclick="anadirLibros('${libro.isbn}')">
                                +
                            </button>
                            </td>
                            <td>
                            <button type="button" class="btn btn-danger" onclick="eliminarLibro('${libro.isbn}')">
                            -
                        </button>
                        </td>
                        </form>
                    </td>
                `;

                tbody.appendChild(fila);
            });

            table.appendChild(tbody);
            tableContainer.appendChild(table);
            contenido.appendChild(tableContainer);
            
            document.getElementById("generos-section").style.display = "block";
            document.getElementById("libros-section").style.display = "none";

            // Crear un enlace "Realizar pedido"
            const realizarPedidoLink = document.createElement("a");
            realizarPedidoLink.href = "/procesar_pedido";  
            realizarPedidoLink.className = "btn btn-primary mt-3";  
            realizarPedidoLink.textContent = "Realizar pedido";
            
            contenido.appendChild(realizarPedidoLink);

        } else {
            console.error('Error al cargar el carrito');
            alert('Hubo un error al cargar el carrito');
        }
    } catch (error) {
        console.error('Error en la petición: ', error);
        alert('Error en la comunicación con el servidor');
    }
}

function actualizarUnidades(isbn, unidades) {
    console.log(`Actualizando unidades del libro con ISBN ${isbn} a ${unidades}`);

    fetch('/carrito/actualizar-unidades', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            isbn: isbn,
            unidades: unidades
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Unidades actualizadas correctamente');
         
            cargarCarrito();  
        } else {
            console.error('Error al actualizar las unidades');
            alert('No se pudo actualizar la cantidad de unidades');
        }
    })
    .catch(error => {
        console.error('Error al hacer la petición:', error);
        alert('Hubo un error al actualizar las unidades');
    });
}

