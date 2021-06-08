<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['descripcion'],$parametros['tipo'],$parametros['rolResponsable'],$parametros['precio']))
        {
            $descripcion = $parametros['descripcion'];
            $tipo = $parametros['tipo'];
            $rolResponsable = $parametros['rolResponsable'];
            $precio = $parametros['precio'];
            $retorno = false;

            // Creamos el producto
            $producto = new Producto();
            $producto->descripcion = $descripcion;
            $producto->tipo = $tipo;
            $producto->rolResponsable = $rolResponsable;
            $producto->precio = $precio;

            if($producto->esValido())
            {
                $retorno = $producto->crear();
            }

            if($retorno)
            {
                $mensaje = "Producto $retorno creado con exito";
                $producto->id = $retorno;
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
        if(isset($args['id']))
        {
            $id = $args['id'];
            $producto = Producto::obtenerUno($id);
            $payload = json_encode($producto);
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

        if(isset($args['rolResponsable']))
        {
            $lista = Producto::obtenerRol($args['rolResponsable']);
        }
        elseif(isset($args['tipo']))
        {
            $lista = Producto::obtenerTipo($args['tipo']);
        }
        else
        {
            $lista = Producto::obtenerTodos();
        }
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['id'],$parametros['descripcion'],$parametros['tipo'],$parametros['rolResponsable'],$parametros['precio']))
        {
            $id = $parametros['id'];
            $descripcion = $parametros['descripcion'];
            $tipo = $parametros['tipo'];
            $rolResponsable = $parametros['rolResponsable'];
            $precio = $parametros['precio'];
    
            $producto = new Producto();
            $producto->id = $id;
            $producto->descripcion = $descripcion;
            $producto->tipo = $tipo;
            $producto->rolResponsable = $rolResponsable;
            $producto->precio = $precio;

            if ($producto->modificar()) 
            {
                $mensaje = "Se actualizó el producto";
            }
            else
            {
                $mensaje = "No se pudo actualizar el producto";
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
        if(isset($parametros['id']))
        {
            $id = $parametros['id'];
            $producto = Producto::obtenerUno($id);
            $borrados = $producto->borrar();
            
            switch ($borrados) {
                case 0:
                    $mensaje = "No se encontró producto que borrar";
                    break;
                case 1:
                    $mensaje = "Producto borrado con exito";
                    break;
                default:
                    $mensaje = "Se borro mas de un producto, CORRE";
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