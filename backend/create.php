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
    $descripcion = json_encode([
        "precio" => $jsonOBJ->precio ?? 0.0,
        "unidades" => $jsonOBJ->unidades ?? 1,
        "modelo" => $modelo,
        "marca" => $marca,
        "detalles" => $jsonOBJ->detalles ?? "NA"
    ], JSON_UNESCAPED_UNICODE);

    // Validar duplicados: (nombre y marca) o (modelo y marca), y eliminado = 0
    $query = "SELECT id FROM productos WHERE eliminado = 0 AND ((nombre = ? AND JSON_UNQUOTE(JSON_EXTRACT(descripcion, '$.marca')) = ?) OR (JSON_UNQUOTE(JSON_EXTRACT(descripcion, '$.modelo')) = ? AND JSON_UNQUOTE(JSON_EXTRACT(descripcion, '$.marca')) = ?)) LIMIT 1";
    $stmt = $conexion->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssss", $nombre, $marca, $modelo, $marca);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Producto duplicado
            echo json_encode(["success" => false, "message" => "Error: El producto ya existe (nombre y marca, o modelo y marca repetidos). No se insertó."]);
            $stmt->close();
            $conexion->close();
            exit;
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error en la validación: " . $conexion->error]);
        $conexion->close();
        exit;
    }

    // Si pasa la validación, insertar
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $nombre, $descripcion);
        if ($stmt->execute()) {
            $inserted_id = $stmt->insert_id;
            echo json_encode(["success" => true, "message" => "Producto insertado correctamente.", "id" => $inserted_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conexion->error]);
    }
    $conexion->close();
}
