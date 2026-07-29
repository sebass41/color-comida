<?php

require_once "../conexion.php";
require_once "res/respuesta.php";

// Configuración para la gestión de errores
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', 1);
ini_set('error_log', '../log/php_errors.log');

class Persona
{

    private $colores = [
        "Rojo",
        "Azul",
        "Verde",
        "Amarillo",
        "Naranja",
        "Violeta",
        "Rosa",
        "Blanco",
        "Negro",
        "Celeste"
    ];

    //=========================================================
    // PERSONAS
    //=========================================================

    public function obtenerTodos()
    {
        try {

            $connection = conection();

            $sql = "SELECT id, nombre
                    FROM persona
                    ORDER BY nombre";

            $respuesta = $connection->query($sql);

            $personas = $respuesta->fetch_all(MYSQLI_ASSOC);

            return new Respuesta(true, "Personas obtenidas correctamente", $personas);

        } catch (Exception $e) {

            return new Respuesta(false, $e->getMessage(), []);

        }
    }

    //=========================================================
    // COLOR DE UNA PERSONA
    //=========================================================

    public function obtenerColorPersona($idPersona)
    {

        try {

            $connection = conection();

            $sql = "SELECT g.color
                    FROM persona p
                    INNER JOIN grupo g
                        ON p.grupo = g.id
                    WHERE p.id = ?";

            $stmt = $connection->prepare($sql);

            $stmt->bind_param("i", $idPersona);

            $stmt->execute();

            $resultado = $stmt->get_result()->fetch_assoc();

            if($resultado == null){
                return new Respuesta(true, "No tienes color", ["color" => null]);
            }else{
                return new Respuesta(true, "Tienes color", $resultado);
            }

        } catch (Exception $e) {

            return new Respuesta(false, $e->getMessage(), []);

        }

    }

    //=========================================================
    // ASIGNAR COLOR
    //=========================================================

    public function asignarColor($idPersona)
    {

        $connection = conection();

        try {

            $connection->begin_transaction();

            // Obtengo el grupo

            $idGrupo = $this->obtenerGrupoPersona($connection, $idPersona);

            if($idGrupo == null){
                return new Respuesta(false, "Todavía no estás en un grupo", $idGrupo);
            }
            // Por si otro integrante ya obtuvo color

            $sql = "SELECT color
                    FROM grupo
                    WHERE id=?";

            $stmt = $connection->prepare($sql);

            $stmt->bind_param("i", $idGrupo);

            $stmt->execute();

            $colorActual = $stmt->get_result()->fetch_assoc();

            if ($colorActual && $colorActual["color"] != null) {

                $connection->commit();

                return new Respuesta(true, $colorActual, [
                    "color" => $colorActual["color"]
                ]);

            }

            // Colores usados

            $coloresUsados = $this->obtenerColoresUsados($connection);

            // Disponibles

            $disponibles = array_values(array_diff($this->colores, $coloresUsados));

            if (count($disponibles) == 0) {

                throw new Exception("No quedan colores disponibles.");

            }

            // Elijo uno

            $color = $disponibles[array_rand($disponibles)];

            // Lo guardo

            $this->guardarColorGrupo($connection, $idGrupo, $color);

            $connection->commit();

            return new Respuesta(true, "Color asignado", [
                "color" => $color
            ]);

        } catch (Exception $e) {

            $connection->rollback();

            return new Respuesta(false, $e->getMessage(), []);

        }

    }

    //=========================================================
    // PRIVADOS
    //=========================================================

    private function obtenerGrupoPersona($connection, $idPersona)
    {

        $sql = "SELECT grupo
                FROM persona
                WHERE id=?";

        $stmt = $connection->prepare($sql);

        $stmt->bind_param("i", $idPersona);

        $stmt->execute();

        $grupo = $stmt->get_result()->fetch_assoc();

        return $grupo["grupo"];

    }

    private function obtenerColoresUsados($connection)
    {

        $sql = "SELECT color
                FROM grupo
                WHERE color IS NOT NULL";

        $respuesta = $connection->query($sql);

        $datos = [];

        while ($fila = $respuesta->fetch_assoc()) {

            $datos[] = $fila["color"];

        }

        return $datos;

    }

    private function guardarColorGrupo($connection, $idGrupo, $color)
    {

        $sql = "UPDATE grupo
                SET color=?
                WHERE id=?";

        $stmt = $connection->prepare($sql);

        $stmt->bind_param("si", $color, $idGrupo);

        $stmt->execute();

    }

}