<?php
require_once './models/Pedido.php';
require_once './models/Comanda.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['idProducto'], $parametros['cantidad'], $parametros['minutosEstimados'], $parametros['idMozo'], $parametros['idComanda']))
        {
            $idProducto = $parametros['idProducto'];
            $cantidad = $parametros['cantidad'];
            $minutosEstimados = $parametros['minutosEstimados'];
            $idMozo = $parametros['idMozo'];
            $idComanda = $parametros['idComanda'];
            $retorno = false;

            // Creamos el pedido
            $pedido = new Pedido();
            $pedido->generarCodigo();
            $pedido->idComanda = $idComanda;
            $pedido->idProducto = $idProducto;
            $pedido->cantidad = $cantidad;
            $pedido->idMozo = $idMozo;
            
            $producto = Producto::obtenerUno($pedido->idProducto);
            $pedido->subtotal = floatval($producto->precio) * $pedido->cantidad;

            $pedido->tsIngresado = date("Y-m-d H:i:s");
            $pedido->tsEstimado = $pedido->tsIngresado = date("Y-m-d H:i:s",strtotime($pedido->tsIngresado ." + $minutosEstimados minute"));


            if($pedido->esValido())
            {
                $retorno = $pedido->crear();
            }

            if($retorno)
            {
                $mensaje = "Pedido [$pedido->codigo] creado con exito";
                $pedido->id = $retorno;
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
            $pedido = Pedido::obtenerUno($codigo);
            $payload = json_encode($pedido);
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
        $lista = Pedido::obtenerTodos();

        $payload = json_encode(array("listaPedido" => $lista));

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
    
            $pedido = Pedido::obtenerUno($codigo);
            $pedido->estado = $estado;

            if($pedido->esValido())
            {
                if ($pedido->modificarEstado()) 
                {
                    $mensaje = "Se actualizó el pedido";
                }
                else
                {
                    $mensaje = "No se pudo actualizar el pedido";
                }
            }
            else
            {
                $mensaje = "El estado es inválido";
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
            $pedido = Pedido::obtenerUno($codigo);

            $borrados = $pedido->cancelar();
            
            switch ($borrados) {
                case 0:
                    $mensaje = "No se encontró pedido para cancelar";
                    break;
                case 1:
                    $mensaje = "Pedido cancelado con exito";
                    break;
                default:
                    $mensaje = "Se canceló mas de un pedido, RUN";
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