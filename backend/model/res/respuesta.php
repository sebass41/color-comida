<?php

class Respuesta{
    public $success; // Propiedad para indicar si la operación fue exitosa
    public $msj; // Propiedad para almacenar un mensaje relacionado con la operación
    public $data; // Propiedad para almacenar datos adicionales relacionados con la operación

    // Constructor de la clase Respuesta
    function __construct($success, $msj, $data){
        $this->success = $success; 
        $this->msj = $msj; 
        $this->data = $data;
    }
}
?>
