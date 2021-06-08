<?php

class Mesa
{
    public $id;
    public $codigo;
    public $estado;
    
    public function crear()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $mesaExistente = Mesa::obtenerUno($this->mesa);

        if(isset($mesaExistente->id))
        {
            return false;
        }

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo, estado) SELECT :codigo, 'cerrada'");
        
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);

        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerUno($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado FROM mesas WHERE codigo = :codigo LIMIT 1");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public function borrar()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = 'eliminado' WHERE id = :id and estado <> 'eliminado'");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function modificarEstado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id and estado <> 'eliminado'");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function esValido()
    {
        return (strlen($this->codigo) == 5 && in_array($this->estado,array("con cliente esperando pedido","con cliente comiendo","con cliente pagando","cerrado")));
    }
}

?>