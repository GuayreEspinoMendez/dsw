<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Support\Facades\Session;
class UsuarioController extends Controller
{
    
    public function login()
    {
        $sesiones = new Usuario();
        $usuarioAutenticado = $sesiones->comprobar_usuario($_POST['usuario'], $_POST['clave']);

        if ($usuarioAutenticado === false) {
            return response()->json(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        } else {
            // Almacenamos la información en la sesión
            Session::put('usuario', $usuarioAutenticado);
            Session::put('carrito', []);

            $idSesion = session()->getId();
            $fechaInicio = now()->format('Y-m-d H:i:s');
            $sesiones->registrarAcceso($_POST['usuario']);
            return response()->json(['success' => true, 'username' => $usuarioAutenticado]);
        }
    }

    public function logout()
    {
        // Obtener el usuario y el ID de sesión
    $usuario = Session::get('usuario');
    $idSesion = session()->getId();
    if ($usuario) {
        // Registrar el fin de sesión
        $sesiones = new Usuario();
        $sesiones->registrarCierreSesion();
    }
        // Eliminación de la sesión
        Session::forget('usuario');
        Session::forget('carrito');

        // Eliminamos la sesión
        Session::flush();

        // Eliminación de Cookies asociadas a la sesión de Laravel y PHP
        setcookie("XSRF-TOKEN", " ", time() - 1000);
        setcookie("laravel_session", " ", time() - 1000);
        setcookie(session_name(), " ", time() - 1000);

        return response()->json(['success' => true]);
    }
     public function comprobarSesion()
     {
        $usuario = Session::get('usuario');
 
         // Verificar si hay un usuario en la sesión
         if ($usuario) {
            return response()->json(['success' => true, 'username' => $usuario]);
        } else {
            return response()->json(['success' => false]);
        }
     }
     // UsuarioController.php

public function obtenerAccesos()
{
    $usuario = new Usuario();  // Instanciamos el modelo

    // Llamamos al método obtenerAccesos que ya implementaste en el modelo Usuario
    $accesos = $usuario->obtenerAccesos();

    // Si no se obtuvieron accesos, devolvemos un mensaje de error
    if (isset($accesos['error'])) {
        return response()->json($accesos, 404);
    }

    return response()->json(['accesos' => $accesos]);
}


}
