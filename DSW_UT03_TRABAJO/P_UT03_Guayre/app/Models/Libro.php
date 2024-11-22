<?php

namespace App\Models;

use App\Models\SimpleXMLElement;

class Libro
{
    // Método para obtener todos los libros desde el archivo XML
    public function obtenerLibros($xmlFilePath)
    {
        // Verificamos si el archivo XML existe
        if (!file_exists($xmlFilePath)) {
            throw new \InvalidArgumentException("El archivo XML no existe.");
        }

        // Cargar el archivo XML
        $xml = simplexml_load_file($xmlFilePath);

        // Si no es un archivo XML válido, lanzamos un error
        if ($xml === false) {
            throw new \InvalidArgumentException("No se pudo cargar el archivo XML.");
        }

        // Devolver el objeto SimpleXMLElement que contiene los libros
        return $xml;
    }
}
