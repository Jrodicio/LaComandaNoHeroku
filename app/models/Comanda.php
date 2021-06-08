<?php

class Comanda
{
    public $id;
    public $nombreCliente;
    public $fotoCliente;
    public $importe;
    public $idMesa;
    public $estado;
    public $tsInicio;
    public $tsFin;

    public function crear()
    {
        $this->tsInicio = date("Y-m-d H:i:s");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO comandas (nombreCliente, idMesa, estado, tsInicio)
                                                                SELECT :nombreCliente, :idMesa, 'activa', :tsInicio");
        
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':tsInicio', $this->tsInicio, PDO::PARAM_STR);

        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public function cerrar()
    {
        $tsFin = date("Y-m-d H:i:s");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE c 
                                                        SET importe = SUM(p.subtotal), 
                                                            estado = 'finalizada',
                                                            tsFin = :tsFin
                                                        FROM comadas AS c 
                                                        INNER JOIN pedidos AS p
                                                        ON c.id = p.idComanda AND c.id = :id
                                                        GROUP BY p.idComanda, c.id");
        
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':tsFin', $tsFin, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function obtenerUno($id)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM comandas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $comandaResultado = $consulta->fetchObject('Comanda');
        return $comandaResultado;
    }

    public static function obtenerPorMesa($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM comandas WHERE idMesa = :idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();
        $comandaResultado = $consulta->fetchObject('Comanda');
        return $comandaResultado;
    }

    public static function obtenerTodos()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM comandas");

        $consulta->execute();
        $comandaResultado = $consulta->fetchObject('Comanda');
        return $comandaResultado;
    }

    public function actualizar()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE comadas 
                                                        SET 
                                                        nombreCliente,
                                                        fotoCliente,
                                                        estado,
                                                        tsFin
                                                        WHERE id = :id");
        
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount();
    }

}

?>