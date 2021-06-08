<?php

use \Firebase\JWT\JWT;

class AuthJWT {

    private static $clave = "!Power.Rangers_1993@atFoxKids";
    private static $tipoEncriptacion = ['HS256'];
    
    public static function CrearToken($datos)
    {
        $ahora = time();
        
        $payload = array(
        	'iat'=>$ahora,
            'exp' => $ahora + 3600,
            'data' => $datos,
            self::$tipoEncriptacion
        );
     
        return JWT::encode($payload, self::$clave);
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$clave,
            self::$tipoEncriptacion
        )->data;
    }

    public static function VerificarToken($token)
    {
       
        if(empty($token) || $token == "")
        {
            throw new Exception("Token vacío");
        }      
        try {
            $decodificado = JWT::decode(
            $token,
            self::$clave,
            self::$tipoEncriptacion
            );
        } catch (ExpiredException $e) {
           throw new Exception("Token vencido");
        }
    }
}
?>