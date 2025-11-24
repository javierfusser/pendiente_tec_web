<?php
header("Access-Control-Allow-Origin: *");
include_once __DIR__ . '/database.php';

// SE OBTIENE LA INFORMACIÓN DEL PRODUCTO ENVIADA POR EL CLIENTE
$producto = file_get_contents('php://input');
if (!empty($producto)) {
    // SE TRANSFORMA EL STRING DEL JSON A OBJETO
    $jsonOBJ = json_decode($producto);
    // Prepara los datos
    $nombre = $jsonOBJ->nombre;
    $marca = $jsonOBJ->marca ?? "NA";
    $modelo = $jsonOBJ->modelo ?? "XX-000";
    $precio = $jsonOBJ->precio ?? 0.0;
    $detalles = $jsonOBJ->detalles ?? "NA";
    $unidades = $jsonOBJ->unidades ?? 1;

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
}
