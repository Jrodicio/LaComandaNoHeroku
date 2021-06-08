<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class VerificacionMW
{
	
	public function VerificarToken($request, $handler)
    {  
		$arrayConToken = $request->getHeader('token');
		$token = $arrayConToken[0];
		
		try 
        {
			AuthJWT::verificarToken($token);
			$esValido = true;
		} 
        catch (Exception $e) 
        {
			$esValido = false;
		}
		
		if($esValido)
        {
			$payload = AuthJWT::ObtenerData($token);
			$request = $request->withAttribute('usuario', $payload);
			$response = $handler->handle($request);
		} 
        else 
        {
			$response = new Response();
			$response->getBody()->write('Sesión inválida, vuelva a loguearse');
			$response->withStatus(401);
		}
        		
        return $response;
	}

	public function VerificarAdmin($request, $handler) 
    {
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$rol = $request->getAttribute('usuario')->rol;

		if($rol == "socio")
        {
			$response = $handler->handle($request);
		}
		else
		{
			$response = new Response();
			$response->getBody()->write("Ud no tiene permisos para realizar esta acción");
			$response->withStatus(401);
		}

        return $response;
	}

	public function VerificarEmpleado($request, $handler)
    {
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$rol = $request->getAttribute('usuario')->rol;
		if(in_array($rol,array("bartender","cervecero","cocinero","mozo","socio"))) 
        {
			$response = $handler->handle($request);
		}
		else
		{
			$response = new Response();
			$response->getBody()->write("Ud no tiene permisos para realizar esta acción");
			$response->withStatus(401);
		}
        
        return $response;
	}

	public function VerificarMozo($request, $handler)
    {
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$rol = $request->getAttribute('usuario')->rol;
		if($rol == "mozo" || $rol == "socio")
        {
			$response = $handler->handle($request);
		}
		else
		{
			$response = new Response();
			$response->getBody()->write("Ud no tiene permisos para realizar esta acción");
			$response->withStatus(401);
		}

        return $response;
	}
}

?>