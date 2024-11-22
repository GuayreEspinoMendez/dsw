<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class Genero extends Model implements JsonSerializable
{
    private $cod;
    private $nombre;

    public function __construct() {
    }

    public function cargarGeneros() {
        // Array con los géneros predefinidos
        $genero1 = array("cod" => '1', "nombre" => "Ciencia Ficción");
        $genero2 = array("cod" => '2', "nombre" => "Comedia");
        $genero3 = array("cod" => '3', "nombre" => "Distopía");
        $genero4 = array("cod" => '4', "nombre" => "Drama");
        $genero5 = array("cod" => '5', "nombre" => "Histórica");
        $genero6 = array("cod" => '6', "nombre" => "Terror");

        // Retornar la lista de géneros en formato JSON
        return json_encode(array($genero1, $genero2, $genero3, $genero4, $genero5, $genero6));
    }
    public function jsonSerialize(): mixed
    {
        return [
            'cod' => $this->getCod(),
            'nombre' => $this->getNombre()
        ];
    }
    public function getCod() {
        return $this->cod;
    }

    public function setCod($cod): self {
        $this->cod = $cod;
        return $this;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre): self {
        $this->nombre = $nombre;
        return $this;
    }
}
