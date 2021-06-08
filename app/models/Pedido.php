<?php

require_once './models/Producto.php';

class Pedido
{
    public $id;
    public $codigo;
    public $idComanda;
    public $estado;
    public $idProducto;
    public $cantidad;
    public $subtotal;
    public $tsIngresado;
    public $tsEstimado;
    public $tsEntregado;
    public $idMozo;
    public $idUsuario;
    
    public function generarCodigo()
    {
        $this->codigo = '';
        while (strlen($this->codigo) < 5)
        {
            $this->codigo .= chr(rand(0, 255));
        }
    }

    public function crear()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (codigo, idComanda, estado, idProducto, cantidad, subtotal, tsIngresado, tsEstimado, idMozo)
                                                                SELECT :codigo, :idComanda, 'pendiente', :idProducto, :cantidad, :subtotal, :tsIngresado, :tsEstimado, :idMozo");
        
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':idComanda', $this->idComarca, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR);
        $consulta->bindValue(':subtotal', $this->subtotal, PDO::PARAM_STR);
        $consulta->bindValue(':tsIngresado', $this->tsIngresado, PDO::PARAM_STR);
        $consulta->bindValue(':tsEstimado', $this->tsEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_STR);

        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, idComanda, estado, idProducto, cantidad, subtotal, tsIngresado, tsEstimado, tsEntregado, idMozo, idUsuario FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerUno($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, idComanda, estado, idProducto, cantidad, subtotal, tsIngresado, tsEstimado, tsEntregado, idMozo, idUsuario FROM pedidos WHERE codigo = :codigo LIMIT 1");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public function cancelar()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = 'cancelado' WHERE codigo = :codigo and estado <> 'cancelado'");

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function modificarEstado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE codigo = :codigo and estado <> 'cancelado'");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function esValido()
    {
        return (strlen($this->codigo) == 5 && in_array($this->estado,array("pendiente","en preparación","listo para servir","cancelado")));
    }
}

?>