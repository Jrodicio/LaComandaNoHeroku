<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['codigo'], $parametros['estado']))
        {
            $codigo = $parametros['codigo'];
            $estado = $parametros['estado'];

            $retorno = false;

            // Creamos la mesa
            $mesa = new Mesa();
            $mesa->codigo = $codigo;
            $mesa->estado = $estado;

            if($mesa->esValido())
            {
                $retorno = $mesa->crear();
            }

            if($retorno)
            {
                $mensaje = "Mesa $retorno creada con exito";
                $mesa->id = $retorno;
            }
            else
            {
                $mensaje = "Error";
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
        if(isset($args['codigo']))
        {
            $codigo = $args['codigo'];
            $mesa = Mesa::obtenerUno($codigo);
            $payload = json_encode($mesa);
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
        $lista = Mesa::obtenerTodos();

        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['codigo'],$parametros['estado']))
        {
            $codigo = $parametros['codigo'];
            $estado = $parametros['estado'];
    
            $mesa = Mesa::obtenerUno($codigo);
            $mesa->estado = $estado;

            if ($mesa->modificarEstado()) 
            {
                $mensaje = "Se actualizó la mesa";
            }
            else
            {
                $mensaje = "No se pudo actualizar la mesa";
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

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['codigo']))
        {
            $codigo = $parametros['codigo'];
            $mesa = Mesa::obtenerUno($codigo);

            $borrados = $mesa->borrar();
            
            switch ($borrados) {
                case 0:
                    $mensaje = "No se encontró mesa que borrar";
                    break;
                case 1:
                    $mensaje = "Mesa borrado con exito";
                    break;
                default:
                    $mensaje = "Se borro mas de una mesa, CORRE";
                    break;
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
}
?>