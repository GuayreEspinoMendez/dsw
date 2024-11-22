<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\CarritoController;


// Ruta a la que se accede por primera vez
Route::get('/', function () {
    return view('principal');
})->name("principal");

// Ruta para hacer el Login
Route::post('login_json', [UsuarioController::class, 'login'])->name("login_validar");

// Ruta para el cerrar sesion
Route::get('logout_json', [UsuarioController::class, 'logout'])->name("logout");

// Ruta para comprobar si el usuario esta logueado
Route::get('/comprobar-sesion', [UsuarioController::class, 'comprobarSesion']);

Route::get('/check_sesion', [UsuarioController::class, 'comprobarSesion']);

// Ruta para listar todos los libros
Route::get('/libros', [LibroController::class, 'index']);

// Ruta para obtener géneros
Route::get('/generos', [GeneroController::class, 'listarGeneros']);

// Ruta para listar libros por género
Route::get('/libros/genero/{genero}', [LibroController::class, 'librosPorGenero']);


Route::get('/accesos', [UsuarioController::class, 'obtenerAccesos']);

// Ruta para añadir un producto a al lista
Route::post('/carrito/agregar/{isbn}/{cantidad}', [CarritoController::class, 'añadirProducto']);

// Ruta para eliminar productos del carrito
Route::delete('/carrito/eliminar/{isbn}', [CarritoController::class, 'eliminarProducto']);

// Ruta para cargar el carrito
Route::get('/carrito/cargar', [CarritoController::class, 'cargarCarrito']);





