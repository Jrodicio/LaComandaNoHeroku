<?php
require_once './models/Comanda.php';
require_once './interfaces/IApiUsable.php';

class ComandaController extends Comanda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombreCliente'], $parametros['idMesa']))
        {
            $nombreCliente = $parametros['nombreCliente'];
            $idMesa = $parametros['idMesa'];

            $retorno = false;

            // Creamos la comanda
            $comanda = new Comanda();
            $comanda->nombreCliente = $nombreCliente;
            $comanda->idMesa = $idMesa;
            
            $retorno = $comanda->crear();

            if($retorno)
            {
                $mensaje = "Comanda $retorno creada con exito";
                $comanda->id = $retorno;
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
        if(isset($args['idComanda']))
        {
            $idComanda = $args['idComanda'];
            $comanda = Comanda::obtenerUno($idComanda);
            $payload = json_encode($comanda);
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
        $lista = Comanda::obtenerTodos();

        $payload = json_encode(array("listaComanda" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if($parametros['idComanda'])
        {
            $idComanda = $parametros['idComanda'];
    
            $comanda = Comanda::obtenerUno($idComanda);

            if(isset($parametros['nombreCliente']))
            {
                $comanda->nombreCliente = $parametros['nombreCliente'];
            }

            if(isset($_FILES['fotoCliente']))
            {
                $tipoArchivo = pathinfo($_FILES['fotoCliente']['name'], PATHINFO_EXTENSION);
                $nombreFoto = ''.$tipoArchivo;
                $pathFoto = './img/'.$nombreFoto;
                
                if ($_FILES["fotoCliente"]["size"] > 500000) 
                {
                    $mensaje = "El archivo es demasiado grande.";
                }
                
                else if(!in_array($tipoArchivo,array("jgp","jpeg","png","JPG","JPEG","PNG")))
                {
                    $mensaje = "El archivo debe ser JPG, JPEG o PNG.";
                }
                
                else
                {
                    move_uploaded_file($_FILES["fotoCliente"]["tmp_name"], $pathFoto);
                    $comanda->fotoCliente = $nombreFoto;
                }
            }

            if ($comanda->actualizar())
            {
                $mensaje = "Comanda actualizada";
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
        if(isset($parametros['idComanda']))
        {
            $idComanda = $parametros['idComanda'];
            $comanda = Comanda::obtenerUno($idComanda);

            if($comanda->cerrar())
            {
                $mensaje = "Comanda finalizada con exito";
            }
            else
            {
                $mensaje = "No se encontró comanda que finalizar";
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