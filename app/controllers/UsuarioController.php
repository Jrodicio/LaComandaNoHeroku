<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;

class UsuarioController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre'],$parametros['usuario'],$parametros['clave'],$parametros['rol']))
        {
            $nombre = $parametros['nombre'];
            $usuario = $parametros['usuario'];
            $clave = $parametros['clave'];
            $rol = $parametros['rol'];
            $retorno = false;

            // Creamos el usuario
            $usr = new Usuario();
            $usr->nombre = $nombre;
            $usr->usuario = $usuario;
            $usr->clave = $clave;
            $usr->rol = $rol;

            if($usr->esValido())
            {
                if($usr->crear())
                {
                    $mensaje = "Usuario $usr->id creado con exito";
                }
                else
                {
                    $mensaje = "No se pudo crear al usuario";
                }
            }
        }   
        else
        {
            $mensaje = "Faltan datos";
        }
        
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        if(isset($args['usuario']))
        {
            $usr = $args['usuario'];
            $usuario = Usuario::obtenerUno($usr);
            $payload = json_encode($usuario);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        if(isset($args['rol']))
        {
            $lista = Usuario::obtenerRol($args['rol']);
        }
        else
        {
            $lista = Usuario::obtenerTodos();
        }

        $payload = json_encode(array("listaUsuario" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['usuario'],$parametros['nombre'],$parametros['clave'],$parametros['rol']))
        {
            $usuario = $parametros['usuario'];
            $nombre = $parametros['nombre'];
            $clave = $parametros['clave'];
            $rol = $parametros['rol'];
    
            $usr = Usuario::obtenerUno($usuario);
            $usr->clave = $clave;
            $usr->nombre = $nombre;
            $usr->rol = $rol;

            if($usr->esValido())
            {
                if ($usr->modificar()) 
                {
                    $mensaje = "Se actualizó el usuario";
                }
                else
                {
                    $mensaje = "No se pudo actualizar el usuario";
                }
            }
        }
        else
        {
            $mensaje = "Faltan datos [id-usuario-nombre-clave]";
        }

        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['usuario']))
        {
            $usuario = $parametros['usuario'];
            $usr = Usuario::obtenerUno($usuario);
            
            $borrados = $usr->borrar();
            
            switch ($borrados) {
                case 0:
                    $mensaje = "No se encontró usuario que borrar";
                    break;
                case 1:
                    $mensaje = "Usuario borrado con exito";
                    break;
                default:
                    $mensaje = "Se borro mas de un usuario, CORRE";
                    break;
            }
        }
        else
        {
          $mensaje = "Faltan datos";
        }

        $payload = json_encode(array("mensaje" => $mensaje));
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function Loguear($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        if(Usuario::verificarCredenciales($usuario, $clave))
        {
            $empleado = Usuario::obtenerUno($usuario);
            $token = AuthJWT::CrearToken($empleado);

            $objResponse = array('token'=>$token, 'usuario'=>$empleado->usuario, 'rol'=>$empleado->rol);
            $codResponse = 200;
        }
        else
        {
            $objResponse = array('respuesta'=>'Usuario o clave incorrecta');
            $codResponse = 401;
        }

        $response->getBody()->write(json_encode($objResponse));
        return $response->withStatus($codResponse);
    }
}
?>