<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{

    public $id;
    public $nombre;
    public $usuario;
    public $clave;
    public $rol;
    public $estado;
    
    protected $hidden = ["created_at", "updated_at"];
    protected $dates = ["deleted_at"];

    public function crear()
    {
        $usuarioExistente = self::obtenerUno($this->usuario);

        if(isset($usuarioExistente->id))
        {
            return false;
        }
        $this->clave = password_hash($this->clave, PASSWORD_DEFAULT);
        return $this->save();
    }

    public static function obtenerTodos()
    {
        return self::all();
    }

    public static function obtenerRol($rol)
    {
        return self::all()->where('rol',$rol);
    }

    public static function obtenerUno($usuario)
    {
        return self::where('usuario',$usuario)->take(1);
    }

    public function modificar()
    {
        return $this->save();
    }

    public function borrar()
    {
        $this->estado = "eliminado";
        $this->modificar();
        return $this->delete();
    }

    public function toggleEstado()
    {
        if ($this->estado == "activo")
        {
            $this->estado = "suspendido";
        }
        else
        {
            $this->estado = "activo";
        }
        
        return $this->modificar();
    }

    public static function verificarCredenciales($usuario, $clave)
    {
        $usuarioObtenido = self::obtenerUno($usuario);

        if(isset($usuarioObtenido->id))
        {
            if(!isset($usuarioObtenido->fechaBaja))
            {
                return password_verify($clave, $usuarioObtenido->clave);
            }
        }
        return false;
    }  

    public function esValido()
    {
        return (strlen($this->nombre) > 3 && strlen($this->usuario) > 6 && strlen($this->clave) > 6 && in_array($this->rol,array("bartender","cervecero","cocinero","mozo","socio")));
    }
}

?>