<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Carrito extends Model
{
    public function cargarCarrito()
{
    // Obtener el carrito actual de la sesión
    $carrito = session('carrito', []);

    // Asegurarse de que 'productos' sea un arreglo vacío si no existe
    $carrito['productos'] = $carrito['productos'] ?? [];  // Si no existe, inicializa como un arreglo vacío

    // Cargar los detalles del libro desde el XML
    $librosDetalles = $this->cargarDetallesLibros();

    // Agregar los detalles del libro a los productos del carrito
    foreach ($carrito['productos'] as &$producto) {
        $isbn = $producto['isbn'];

        if (isset($librosDetalles[$isbn])) {
            $producto['titulo'] = $librosDetalles[$isbn]['titulo'];
            $producto['escritores'] = $librosDetalles[$isbn]['escritores'];
            $producto['genero'] = $librosDetalles[$isbn]['genero'];
            $producto['numpaginas'] = $librosDetalles[$isbn]['numpaginas'];
            $producto['imagen'] = $librosDetalles[$isbn]['imagen'];
        }
    }

    return $carrito;
}


    // Cargar los detalles del libro desde el XML
    private function cargarDetallesLibros()
    {
        $librosDetalles = [];

        // Cargar el archivo XML de los libros (asegúrate de que la ruta sea correcta)
        $xml = simplexml_load_file(storage_path('app/xml/libros.xml'));

        // Recorrer el XML y almacenar los detalles del libro en un array
        foreach ($xml->libro as $libro) {
            $isbn = (string) $libro->isbn;

            $librosDetalles[$isbn] = [
                'titulo' => (string) $libro->titulo,
                'escritores' => (string) $libro->escritores,
                'genero' => (string) $libro->genero,
                'numpaginas' => (string) $libro->numpaginas, // Este valor ya está como texto con 'pág.'
                'imagen' => (string) $libro->imagen
            ];
        }

        return $librosDetalles;
    }

    public function añadirAlCarrito($idProducto, $nombreProducto, $cantidad = 1)
    {
        // Obtener el carrito actual
        $carrito = $this->cargarCarrito();

        // Verificar si el producto ya está en el carrito
        if (isset($carrito['productos'][$idProducto])) {
            // Si ya está, incrementar la cantidad
            $carrito['productos'][$idProducto]['unidades'] += $cantidad;
        } else {
            // Si no está, añadirlo al carrito
            $carrito['productos'][$idProducto] = [
                'isbn' => $idProducto,  // Usamos ISBN como ID
                'titulo' => $nombreProducto,  // El nombre del libro
                'unidades' => $cantidad  // La cantidad solicitada
            ];
        }

        // Actualizar el número total de unidades y artículos
        $carrito['numunidades'] = array_reduce($carrito['productos'], function ($carry, $producto) {
            return $carry + $producto['unidades'];  // Sumar unidades de cada producto
        }, 0);

        $carrito['numarticulos'] = count($carrito['productos']);  // Contar productos diferentes

        // Guardar el carrito actualizado en la sesión
        session()->put('carrito', $carrito);
    }

   // En tu modelo Carrito (por ejemplo, Carrito.php)
public function eliminarDelCarrito($isbn)
{
    // Obtener el carrito actual
    $carrito = $this->cargarCarrito();

    // Verificar si el producto existe en el carrito
    if (isset($carrito['productos'][$isbn])) {
        // Eliminar el producto del carrito
        unset($carrito['productos'][$isbn]);

        // Actualizar el número total de unidades y artículos
        $carrito['numunidades'] = array_reduce($carrito['productos'], function ($carry, $producto) {
            return $carry + $producto['unidades'];
        }, 0);

        $carrito['numarticulos'] = count($carrito['productos']); // Contar productos diferentes

        // Guardar el carrito actualizado en la sesión
        session()->put('carrito', $carrito);

        return true;
    }

    return false; // Producto no encontrado en el carrito
}


    public function calcularTotal()
    {
        $carrito = $this->cargarCarrito();

        // Total de unidades y artículos
        $total = [
            'numunidades' => $carrito['numunidades'],
            'numarticulos' => $carrito['numarticulos'],
        ];

        return $total;
    }
}
