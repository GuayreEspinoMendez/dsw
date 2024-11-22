<?php

namespace App\Http\Controllers;

use App\Models\Libro; 
use InvalidArgumentException;
class LibroController extends Controller
{
    // Método para listar todos los libros
    public function index()
    {
        $libroModel = new Libro();

        try {
            // Ruta del archivo XML
            $xmlFilePath = storage_path('app/xml/libros.xml');

            // Obtener todos los libros del archivo XML
            $libros = $libroModel->obtenerLibros($xmlFilePath);

            // Convertir los libros a un array y devolverlos en formato JSON
            $librosArray = [];
            foreach ($libros->libro as $libro) {
                $librosArray[] = [
                    'isbn' => (string)$libro->isbn,
                    'titulo' => (string)$libro->titulo,
                    'escritores' => (string)$libro->escritores,
                    'genero' => (string)$libro->genero,
                    'numpaginas' => (string)$libro->numpaginas,
                    'imagen' => (string)$libro->imagen,
                ];
            }

            return response()->json($librosArray);  // Devolver los libros en formato JSON
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => 'Error al cargar los libros: ' . $e->getMessage()], 500);
        }
    }

    // Método para listar los libros filtrados por género
    public function librosPorGenero($genero)
{
    // Crear una instancia del modelo Libro
    $libroModel = new Libro();

    try {
        // Obtener todos los libros
        $libros = $libroModel->obtenerLibros(storage_path('app/xml/libros.xml'));

        // Normalizar el género solicitado
        $generoNormalizado = strtolower($genero);

        // Filtrar los libros por género
        $librosFiltrados = [];
        foreach ($libros->libro as $libro) {
            $generoLibro = strtolower($libro->genero);
            if ($generoLibro === $generoNormalizado) {
                $librosFiltrados[] = $libro;
            }
        }

        // Si no se encuentran libros del género, retornar un error
        if (count($librosFiltrados) === 0) {
            return response()->json(['error' => 'No se encontraron libros para el género: ' . $genero], 404);
        }

        // Retornar los libros filtrados como JSON
        return response()->json($librosFiltrados);

    } catch (InvalidArgumentException $e) {
        return response()->json(['error' => 'Error al cargar los libros: ' . $e->getMessage()], 500);
    }
}

}   
