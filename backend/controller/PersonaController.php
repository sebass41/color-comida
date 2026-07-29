<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once "../model/Persona.php";

$funcion = $_GET['fun'];

switch ($funcion) {

    case 'obtenerTodos':
        obtenerTodos();
        break;

    case 'obtenerColorPersona':
        obtenerColorPersona();
        break;

    case 'asignarColor':
        asignarColor();
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Función inexistente."
        ]);
        break;

}

//======================================================
// OBTENER TODAS LAS PERSONAS
//======================================================

function obtenerTodos(){

    $resultado = (new Persona())->obtenerTodos();

    echo json_encode($resultado);

}

//======================================================
// OBTENER COLOR
//======================================================

function obtenerColorPersona(){

    $idPersona = $_POST["idPersona"];

    $resultado = (new Persona())->obtenerColorPersona($idPersona);

    echo json_encode($resultado);

}

//======================================================
// ASIGNAR COLOR
//======================================================

function asignarColor(){

    $idPersona = $_POST["idPersona"];

    $resultado = (new Persona())->asignarColor($idPersona);

    echo json_encode($resultado);

}