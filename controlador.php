<?php
require_once 'conexion.php';
// valida si el parametro ruta existe
if (isset($_POST['ruta'])) {
    $ruta = $_POST['ruta'];
    // recibe el parametro "ruta" que se envia en cada peticion ajax para seleccionar el metodo que se utilizara
    switch ($ruta) {
        case 'cargarRegiones':
            cargarRegiones();
            break;
        case 'cargarComunas':
            cargarComunas($_POST['region']);
            break;
        case 'setVotacion':
            setVotacion($_POST['form']);
            break;
        default:
            // en caso de que no exista el metodo
            $respuesta = array('success' => false, 'message' => 'Ruta no valida');
            echo json_encode($respuesta);
    }
}


function cargarRegiones()
{
    // se crea una instancia de la clase conexion
    $conexion = new Conexion();
    $conexion->conectar();

    // se accede al metodo obtenerConexion y se maneja la conexion como $db
    $db = $conexion->obtenerConexion();
    // la bd de regiones y comunas que usada viene con caracteres incompatibles asi que se formatean a utf8 por precaucion
    $db->set_charset("utf8");

    // se crean 2 consultas para obtener los datos de las tablas regiones y candidatos y hacer 2 consultas simultaneas
    $queryRegiones = "SELECT * FROM regiones";
    $resultRegiones = $db->query($queryRegiones);
    $queryCandidatos = "SELECT * FROM candidatos";
    $resultCandidatos = $db->query($queryCandidatos);
    $respuesta = [];
    $regiones = [];
    $candidatos = [];
    $validaRegiones = false;
    $validaCandidatos = false;
    // valida que la consulta a la base de datos tenga una respuesta con los datos
    if ($resultRegiones->num_rows > 0) {
        // se recorren los datos obtenidos y se almacenan en un array para enviarlos al front
        while ($row = $resultRegiones->fetch_assoc()) {
            $regiones[] = $row;
        }
        $validaRegiones = true;
    }

    if ($resultCandidatos->num_rows > 0) {
        // se recorren los datos obtenidos y se almacenan en un array para enviarlos al front
        while ($row = $resultCandidatos->fetch_assoc()) {
            $candidatos[] = $row;
        }
        $validaCandidatos = true;
    }
    // se valida si el proceso de iteracion de ambos conjuntos de datos tanto regiones y candidatos se cumplieron de lo contrario envia un mensaje de error
    if ($validaRegiones && $validaCandidatos) {
        $respuesta = array('success' => true, 'regiones' => $regiones, 'candidatos' => $candidatos);
    } else {
        $respuesta = array('success' => false, 'msg' => "no se encontraron datos");
    }

    // se codifica el array a formato json
    $json = json_encode($respuesta);

    echo $json;

    // se cierra la conexión
    $conexion->cerrarConexion();
}

function cargarComunas($region)
{
    // se crea una instancia de la clase conexion
    $conexion = new Conexion();
    $conexion->conectar();

    // se accede al metodo obtenerConexion y se maneja la conexion como $db
    $db = $conexion->obtenerConexion();
    // la bd de regiones y comunas que usada viene con caracteres incompatibles asi que se formatean a utf8 por precaucion
    $db->set_charset("utf8");

    // se crea una consulta llamada "query" que se usara para obtener todas las comunas de la bd correspondientes a la region seleccionada
    $query = "SELECT * FROM comunas WHERE region_id = $region";
    $result = $db->query($query);
    $respuesta = [];

    // valida que la consulta a la base de datos tenga una respuesta con los datos
    if ($result->num_rows > 0) {
        $comunas = array();

        // se recorren los datos obtenidos y se almacenan en un array para enviarlos al front
        while ($row = $result->fetch_assoc()) {
            $comunas[] = $row;
        }

        // se genera una respuesta que enviara el estado de la consulta y en caso de tener exito enviara las comunas obtenidas
        $respuesta = array('success' => true, 'data' => $comunas);
    } else {
        $respuesta = array('success' => false);
    }

    // se codifica el array a formato json
    $json = json_encode($respuesta);

    echo $json;

    // se cierra la conexión
    $conexion->cerrarConexion();
}


function setVotacion($form)
{
    // se crea un array vacio para formatear la string recibida por por el formulario
    $formData = [];
    parse_str($form, $formData);

    $checkboxes = array('web', 'tv', 'redes', 'amigo');
    $checkeados = 0;

    foreach ($checkboxes as $checkbox) {
        if (isset($formData[$checkbox]) && $formData[$checkbox] === 'on') {
            $checkeados++;
            $formData[$checkbox] = '1';
        } else {
            $formData[$checkbox] = '0';
        }
    }
    // se crea un array que se llenara con los campos que estan sin completar
    $camposVacios = [];
    foreach ($formData as $k => $data) {
        //se iteran todos los campos y si esta vacio se agrega al array $camposVacios para luego mostrar el error 
        if ($data == "") {
            array_push($camposVacios, "el campo " . $k . " no puede estar vacio");
        }
    }
    //se inicializa la variable $aliasV en false luego se aplican validaciones alfanumericas y de longitud si pasa ambas validaciones el valor cambia a true y permitira completar el flujo
    $aliasV = false;
    if (strlen($formData["alias"]) > 5 && preg_match('/^(?=.*[a-zA-Z])(?=.*\d).+$/', $formData["alias"])) {
        $aliasV = true;
    }

    // se crea una instancia de la clase conexion
    $conexion = new Conexion();
    $conexion->conectar();

    // se accede al metodo obtenerConexion y se maneja la conexion como $db
    $db = $conexion->obtenerConexion();
    $query = "SELECT * FROM votos WHERE rut = '" . $formData["rut"] . "'";
    $result = $db->query($query);

    // cada if verifica una de las validaciones previamente procesadas para mostrar el mensaje correspondiente en la respuesta 
    if (count($camposVacios) > 0) {
        $respuesta = array('status' => 1, "msg" => $camposVacios);
    } else if (!$aliasV) {
        $respuesta = array('status' => 6, "msg" => "El alias debe tener una longitud mayor a 5 caracteres y contener letras y numeros.");
    } else if (!validaRut($formData["rut"])) {
        // esta validacion utiliza el "helper" para validar el rut que nos retorna un booleano para saber si el rut es valido o es incorrecto
        $respuesta = array('status' => 2, "msg" => "Debes ingresar un rut valido.");
    } else if ($result->num_rows > 0) {
        $respuesta = array('status' => 3, "msg" => "El rut ingresado ya registro un voto.");
    } else if ($checkeados < 2) {
        $respuesta = array('status' => 4, "msg" => "Debes seleccionar mas de 2 cajas.");
    } else {
        // se inserta el nuevo registro y se valida el mensaje resultante
        $insert = "INSERT INTO votos (nombre, alias, rut, email, region, comuna, candidato, web, tv, redes_sociales, amigo) VALUES 
        ('" . $formData["nombre"] . "', '" . $formData["alias"] . "', '" . $formData["rut"] . "', '" . $formData["email"] . "', '" . $formData["region"] . "',
        '" . $formData["comuna"] . "', '" . $formData["candidato"] . "', '" . $formData["web"] . "', '" . $formData["tv"] . "', '" . $formData["redes"] . "', '" . $formData["amigo"] . "')";
        $result = $db->query($insert);
        if ($result === true) {
            $respuesta = array('status' => 0, 'msg' => 'Votación exitosa.');
        } else {
            $respuesta = array('status' => 5, 'msg' => 'Error al realizar la votación.');
        }
    }

    $json = json_encode($respuesta);
    header('Content-Type: application/json');
    echo $json;
}


// este script publico de internet que verifica si un rut es valido, no es de mi autoria ya que no conosco el algoritmo de los rut asi que los importe como helper
// fuente : https://gist.github.com/donpandix/16162fb082f8c7305fe8#file-validarut-php
function validaRut($rutCompleto)
{
    if (!preg_match("/^[0-9]+-[0-9kK]{1}/", $rutCompleto)) return false;
    $rut = explode('-', $rutCompleto);
    return strtolower($rut[1]) == dv($rut[0]);
}
function dv($T)
{
    $M = 0;
    $S = 1;
    for (; $T; $T = floor($T / 10))
        $S = ($S + $T % 10 * (9 - $M++ % 6)) % 11;
    return $S ? $S - 1 : 'k';
}
