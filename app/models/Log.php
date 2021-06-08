<?

class Log
{
    public $id;
    public $ts;
    public $idUsuario;
    public $operacion;
    public $datos;
    public $tipoObjeto;
    public $idObjeto;
    
    public function InsertarLog() 
    {
        $this->ts = date("Y-m-d H:i:s");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (ts, idUsuario, operacion, datos, tipoObjeto, idObjeto)
                                                                SELECT :ts, :idUsuario, :operacion, :datos, :tipoObjeto, :idObjeto");
        
        $consulta->bindValue(':ts', $this->ts, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':operacion', $this->operacion, PDO::PARAM_STR);
        $consulta->bindValue(':datos', $this->datos, PDO::PARAM_STR);
        $consulta->bindValue(':tipoObjeto', $this->tipoObjeto, PDO::PARAM_STR);
        $consulta->bindValue(':idObjeto', $this->idObjeto, PDO::PARAM_STR);

        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
}
?>