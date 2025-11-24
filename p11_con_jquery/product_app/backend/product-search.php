<?php
include_once __DIR__ . '/database.php';

// SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
$data = array();
// SE VERIFICA HABER RECIBIDO EL ID
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $conexion->set_charset("utf8");
    // SE REALIZA LA QUERY DE BÚSQUEDA Y AL MISMO TIEMPO SE VALIDA SI HUBO RESULTADOS
    $sql = "SELECT * FROM productos WHERE (id = '{$search}' OR nombre LIKE '%{$search}%' OR marca LIKE '%{$search}%' OR detalles LIKE '%{$search}%') AND eliminado = 0";
    if ($result = $conexion->query($sql)) {
        // SE OBTIENEN LOS RESULTADOS
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        if (!is_null($rows)) {
            // SE CODIFICAN A UTF-8 LOS DATOS Y SE MAPEAN AL ARREGLO DE RESPUESTA
            foreach ($rows as $num => $row) {
                foreach ($row as $key => $value) {
                    $data[$num][$key] = $value;
                }
                // Decodificar el JSON de descripcion y agregar sus campos
                if (isset($row['descripcion']) && !empty($row['descripcion'])) {
                    $desc = json_decode($row['descripcion'], true);
                    if ($desc !== null) {
                        $data[$num]['precio'] = $desc['precio'] ?? 0.0;
                        $data[$num]['unidades'] = $desc['unidades'] ?? 1;
                        $data[$num]['modelo'] = $desc['modelo'] ?? 'XX-000';
                    }
                }
            }
        }
        $result->free();
    } else {
        die('Query Error: ' . mysqli_error($conexion));
    }
    $conexion->close();
}

// SE HACE LA CONVERSIÓN DE ARRAY A JSON
echo json_encode($data, JSON_PRETTY_PRINT);
