<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use InvalidArgumentException;
use Illuminate\Support\Facades\Session;
class Usuario extends Model
{

    function leer_config($rutaFicheroConf, $rutaEsquemaConfiguracion)
    {
        $config = new DOMDocument();
        $config->load($rutaFicheroConf);
        // se valida el esquema del fichero XML
        $res = $config->schemaValidate($rutaEsquemaConfiguracion);
        if ($res === FALSE) {
            throw new InvalidArgumentException("Revise fichero de configuración");
        }
        // se cargan los datos del fichero XML
        $datos = simplexml_load_file($rutaFicheroConf);
        $usu = $datos->xpath("//usuario");
        $clave = $datos->xpath("//clave");
        $resul = [];
        $resul[] = $usu[0];
        $resul[] = $clave[0];
        return $resul;
    }

    function comprobar_usuario($nombre, $clave)
    {
        $res = $this->leer_config(storage_path('app/xml/configuracion.xml'), storage_path('app/xml/configuracion.xsd'));
        return $nombre == $res[0] && $clave == $res[1];
    }

    public function registrarAcceso($usuario)
    {
        // Ruta al archivo de accesos
        $rutaAccesos = storage_path('app/datos/info_accesos.dat');
        
        // Leer todas las líneas del archivo de accesos
        $lineas = file($rutaAccesos, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Incrementamos el ID de sesión
        $ultimoIdSesion = 0;
        foreach ($lineas as $linea) {
            $datos = explode('#', $linea);
            if (isset($datos[0])) {
                $ultimoIdSesion = max($ultimoIdSesion, (int) $datos[0]);
            }
        }
    
        $nuevoIdSesion = $ultimoIdSesion + 1;  // Incrementamos el ID de sesión
    
        // Obtenemos la fecha y hora actual para el inicio de la sesión
        $fechaInicio = now()->format('Y-m-d H:i:s');
        
        // Almacenamos el acceso en el archivo con el nuevo ID de sesión
        $linea = "{$nuevoIdSesion}#{$usuario}#{$fechaInicio}#\n";  // Asumimos que el cierre aún no está disponible
        
        // Verificar si la carpeta existe, si no, crearla
        $carpeta = dirname($rutaAccesos);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }
        
        // Escribir la línea al archivo (crear o agregar)
        file_put_contents($rutaAccesos, $linea, FILE_APPEND | LOCK_EX);
        session()->put('id_sesion', $nuevoIdSesion);
    }
    
    public function registrarCierreSesion()
    {
        // Obtenemos el ID de sesión y el usuario
        $idSesion = session()->get('id_sesion');
        $usuario = Session::get('usuario');
        
        // Si no hay usuario en la sesión, no podemos registrar el cierre
        if (!$usuario || !$idSesion) {
            return response()->json(['error' => 'No se pudo obtener el usuario o el ID de sesión.'], 400);
        }
        
        // Obtenemos la fecha y hora actual para el cierre de la sesión
        $fechaCierre = now()->format('Y-m-d H:i:s');
        
        // Ruta al archivo de accesos
        $rutaAccesos = storage_path('app/datos/info_accesos.dat');
        
        // Leer todas las líneas del archivo
        $lineas = file($rutaAccesos, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lineasActualizadas = [];
        
        // Procesamos cada línea para actualizar la fecha de cierre
        foreach ($lineas as $linea) {
            $datos = explode('#', $linea);
            if ($datos[0] == $idSesion) {
                $datos[3] = $fechaCierre;  // Actualizamos la fecha de cierre
                $lineasActualizadas[] = implode('#', $datos);
            } else {
                $lineasActualizadas[] = $linea;
            }
        }
        
        // Guardamos las líneas actualizadas de nuevo en el archivo
        file_put_contents($rutaAccesos, implode(PHP_EOL, $lineasActualizadas) . PHP_EOL);
        
        // Eliminamos la sesión y limpiamos las cookies
        Session::forget('usuario');
        Session::flush();
        
        return response()->json(['success' => true]);
    }
// Usuario.php (Modelo)

public function obtenerAccesos()
{
    // Ruta al archivo de accesos
    $rutaAccesos = storage_path('app/datos/info_accesos.dat');
    
    // Verificar si el archivo existe
    if (!file_exists($rutaAccesos)) {
        return ['error' => 'El archivo de accesos no existe.'];  // Retornar un error si el archivo no existe
    }

    // Leer las líneas del archivo
    $lineas = file($rutaAccesos, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $accesos = [];

    // Procesar cada línea del archivo
    foreach ($lineas as $linea) {
        // Dividir la línea por el delimitador #
        $datos = explode('#', $linea);

        // Asegurarnos de que haya al menos 3 elementos: idSesion, usuario, fechaInicio
        if (count($datos) >= 3) {
            $accesos[] = [
                'idSesion' => $datos[0],  // El ID de sesión incrementado
                'usuario' => $datos[1],   // El nombre del usuario que acaba de registrarse
                'inicioSesion' => $datos[2],
                'finalSesion' => $datos[3] ?? 'En sesión', // Si no hay fecha de cierre, mostrar "En sesión"
            ];
        }
    }

    return $accesos;
}


    
}
