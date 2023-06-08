<?php
//se crea la clase Conexion para utilizarla de forma global usando un constructor
class Conexion {
    //se establecen las credenciales para conectarse a la bd
    private $host = "localhost";
    private $usuario = "root";
    private $contrasena = "";
    private $basedatos = "bd_votacion";
    private $conexion;

    public function conectar() {
        //este metodo crea una instancia de conexion en $this->conexion comenzando el constructor
        $this->conexion = new mysqli($this->host, $this->usuario, $this->contrasena, $this->basedatos);

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function obtenerConexion() {
        //manipula la instancia creada por el constructor en $this->conexion
        return $this->conexion;
    }

    public function cerrarConexion() {
        //metodo que cierra la instancia de conexion
        $this->conexion->close();
    }
}
?>