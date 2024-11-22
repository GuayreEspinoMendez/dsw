<?php
namespace App\Http\Controllers;

use App\Models\Carrito;

class CarritoController extends Controller
{
    protected $carrito;

    public function __construct()
    {
        // Crear una instancia del modelo Carrito
        $this->carrito = new Carrito();
    }

    
    // Cargar el carrito con los detalles de los libros
    public function cargarCarrito()
    {
        // Obtener el carrito con los detalles de los libros
        $carrito = $this->carrito->cargarCarrito();

        // Devolver el carrito con los detalles completos
        return response()->json([
            'carrito' => $carrito
        ]);
    }

    // Añadir producto al carrito
    public function añadirProducto($isbn,)
    {
        // Agregar el libro al carrito
        $this->carrito->añadirAlCarrito($isbn, $isbn);

        // Devolver el carrito actualizado
        return response()->json([
            'mensaje' => 'Producto añadido al carrito correctamente',
            'carrito' => $this->carrito->cargarCarrito()
        ]);
    }

    // Eliminar producto del carrito
    public function eliminarProducto($isbn)
    {
        $eliminado = $this->carrito->eliminarDelCarrito($isbn);

        if ($eliminado) {
            return response()->json([
                'mensaje' => 'Producto eliminado del carrito correctamente',
                'carrito' => $this->carrito->cargarCarrito()
            ]);
        } else {
            return response()->json(['error' => 'Producto no encontrado en el carrito'], 404);
        }
    }
}
