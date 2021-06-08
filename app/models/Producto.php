<?php

class Producto
{
    public $id;
    public $descripcion;
    public $tipo;
    public $rolResponsable;
    public $precio;
    public $estado;
    
    public function crear()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $productoExistente = Producto::obtenerUno($this->descripcion);

        if(isset($productoExistente->id))
        {
            return false;
        }

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, tipo, rolResponsable, precio, estado) 
                                                            SELECT :descripcion, :tipo, :rolResponsable, :precio, 'disponible'");
        
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':rolResponsable', $this->rolResponsable, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
     
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM productos WHERE estado <> 'eliminado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerRol($rolResponsable)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM productos WHERE rolResponsable = :rolResponsable AND estado <> 'eliminado'");
        $consulta->bindValue(':rolResponsable', $rolResponsable, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerTipo($tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM productos WHERE tipo = :tipo AND estado <> 'eliminado'");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerUno($clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM productos WHERE (id = :id OR descripcion = :descripcion) AND estado <> 'eliminado' LIMIT 1");
        $consulta->bindValue(':id', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $clave, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public function modificar()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET descripcion = :descripcion, tipo = :tipo, rolResponsable = :rolResponsable, precio = :precio WHERE id = :id AND estado <> 'eliminado'");
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':rolResponsable', $this->rolResponsable, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public function borrar()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET estado = 'eliminado' WHERE id = :id AND estado <> 'eliminado'");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function toggleEstado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET estado = CASE WHEN estado = 'disponible' THEN 'no disponible' ELSE 'disponible' END WHERE id = :id and estado <> 'eliminado'");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    } 

    public function esValido()
    {
        return (strlen($this->descripcion) > 3 && in_array($this->tipo,array("bebida","comida")) && in_array($this->rolResponsable,array("bartender","cervecero","cocinero","mozo","socio")) && floatval($this->precio) > 0);
    }
}

?>