<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__.'/database.php';

// SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
$data = array(
    'status'  => 'error',
    'message' => 'La consulta falló'
);

// SE VERIFICA HABER RECIBIDO EL ID
if(isset($_POST['id']) && isset($_POST['nombre'])) {
    $id = intval($_POST['id']);
    
    // Validar que el campo nombre no está vacío
    if (empty(trim($_POST['nombre']))) {
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
    
    // Preparar y ejecutar el UPDATE
    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, marca=?, detalles=?, descripcion=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("ssssi", $nombre, $marca, $detalles, $descripcion, $id);
        if ($stmt->execute()) {
            $data['status'] = "success";
            $data['message'] = "Producto actualizado";
        } else {
            $data['message'] = "ERROR: No se pudo actualizar. " . $stmt->error;
        }
        $stmt->close();
    } else {
        $data['message'] = "ERROR: No se pudo preparar la consulta. " . $conexion->error;
    }
    $conexion->close();
} else {
    $data['message'] = "ERROR: Faltan datos necesarios (id o nombre)";
}

// SE HACE LA CONVERSIÓN DE ARRAY A JSON
echo json_encode($data, JSON_PRETTY_PRINT);
?>