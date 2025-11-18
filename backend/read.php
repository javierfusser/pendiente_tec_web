<?php
include_once __DIR__ . '/database.php';


// SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
$data = array();

if (isset($_POST['id'])) {
    $id = $conexion->real_escape_string($_POST['id']);
    $sql = "SELECT * FROM productos WHERE id = '$id'";
    $result = $conexion->query($sql);
    if ($result) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $row['descripcion'] = json_decode($row['descripcion'], true);
            $data[] = $row;
        }
        $result->free();
    }
    $conexion->close();
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

if (isset($_POST['nombre']) || isset($_POST['marca']) || isset($_POST['detalles']) || isset($_POST['q'])) {
    // Búsqueda flexible por parte del nombre, marca o detalles
    $q = '';
    if (isset($_POST['q'])) {
        $q = $conexion->real_escape_string($_POST['q']);
    } else if (isset($_POST['nombre'])) {
        $q = $conexion->real_escape_string($_POST['nombre']);
    } else if (isset($_POST['marca'])) {
        $q = $conexion->real_escape_string($_POST['marca']);
    } else if (isset($_POST['detalles'])) {
        $q = $conexion->real_escape_string($_POST['detalles']);
    }
    $sql = "SELECT * FROM productos WHERE nombre LIKE '%$q%' OR JSON_UNQUOTE(JSON_EXTRACT(descripcion, '$.marca')) LIKE '%$q%' OR JSON_UNQUOTE(JSON_EXTRACT(descripcion, '$.detalles')) LIKE '%$q%'";
    $result = $conexion->query($sql);
    if ($result) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $row['descripcion'] = json_decode($row['descripcion'], true);
            $data[] = $row;
        }
        $result->free();
    }
    $conexion->close();
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

// Si no hay parámetros, devolver array vacío
echo json_encode($data, JSON_PRETTY_PRINT);
