<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__ . '/database.php';

// SE OBTIENE LA INFORMACIÓN DEL PRODUCTO ENVIADA POR EL CLIENTE
// jQuery $.post() envía datos como form data en $_POST, no como JSON
if (!empty($_POST)) {
    // Validar que el campo nombre existe y no está vacío
    if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
        echo json_encode(["status" => "error", "message" => "El campo 'nombre' es obligatorio"]);
        exit;
    }
    
    // Prepara los datos desde $_POST
    $nombre = trim($_POST['nombre']);
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : "NA";
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : "XX-000";
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.0;
    $detalles = isset($_POST['detalles']) ? trim($_POST['detalles']) : "NA";
    $unidades = isset($_POST['unidades']) ? intval($_POST['unidades']) : 1;

    // Crear el JSON de descripción con los campos que no tienen columna
    $descripcion = json_encode([
        "precio" => $precio,
        "unidades" => $unidades,
        "modelo" => $modelo
    ], JSON_UNESCAPED_UNICODE);

    // SE ESTABLECE LA CODIFICACIÓN UTF-8
    $conexion->set_charset("utf8");

    // Validar duplicados: (nombre y marca), y eliminado = 0
    $query = "SELECT id FROM productos WHERE eliminado = 0 AND nombre = ? AND marca = ? LIMIT 1";
    $stmt = $conexion->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $nombre, $marca);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Producto duplicado
            echo json_encode(["status" => "error", "message" => "Error: El producto ya existe (nombre y marca repetidos). No se insertó."]);
            $stmt->close();
            $conexion->close();
            exit;
        }
        $stmt->free_result();
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error en la validación: " . $conexion->error]);
        $conexion->close();
        exit;
    }

    // Si pasa la validación, insertar
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, marca, detalles, descripcion, eliminado) VALUES (?, ?, ?, ?, 0)");
    if ($stmt) {
        $stmt->bind_param("ssss", $nombre, $marca, $detalles, $descripcion);
        if ($stmt->execute()) {
            $inserted_id = $stmt->insert_id;
            echo json_encode(["status" => "success", "message" => "Producto insertado correctamente.", "id" => $inserted_id]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al insertar: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error en la preparación: " . $conexion->error]);
    }
    $conexion->close();
} else {
    // Si no llegó ningún payload, devolver un mensaje JSON
    echo json_encode(["status" => "error", "message" => "No se recibió payload"]);
}