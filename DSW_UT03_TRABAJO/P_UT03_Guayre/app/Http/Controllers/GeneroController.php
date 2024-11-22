<?php

namespace App\Http\Controllers;

use App\Models\Genero;

class GeneroController extends Controller
{
    
    public function listarGeneros()
    {
        // Crear una instancia del modelo Genero
        $generoModel = new Genero();
        
        // Cargar los géneros desde el modelo
        $generos = $generoModel->cargarGeneros();
        
        // Retornar los géneros en formato JSON
        return response()->json(json_decode($generos));
    }
}
