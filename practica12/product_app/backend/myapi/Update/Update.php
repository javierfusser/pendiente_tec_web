<?php

namespace TECWEB\MYAPI\Update;

use TECWEB\MYAPI\DataBase;

require_once __DIR__ . '/../DataBase.php';

class Update extends DataBase
{
    private $data;

    public function __construct($db, $user = 'root', $pass = '12345678a')
    {
        $this->data = array();
        parent::__construct($db, $user, $pass);
    }

    public function edit($jsonOBJ)
    {
        // SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
        $this->data = array(
            'status'  => 'error',
            'message' => 'La consulta falló'
        );
        // SE VERIFICA HABER RECIBIDO EL ID
        if (isset($jsonOBJ->id)) {
            $this->conexion->set_charset("utf8");

            // Crear descripcion vacía o con datos adicionales
            $descripcion = isset($jsonOBJ->descripcion) ? $this->conexion->real_escape_string($jsonOBJ->descripcion) : '';

            // SE REALIZA LA QUERY DE BÚSQUEDA Y AL MISMO TIEMPO SE VALIDA SI HUBO RESULTADOS
            $sql =  "UPDATE productos SET nombre='{$jsonOBJ->nombre}', marca='{$jsonOBJ->marca}',";
            $sql .= "modelo='{$jsonOBJ->modelo}', precio={$jsonOBJ->precio}, detalles='{$jsonOBJ->detalles}',";
            $sql .= "unidades={$jsonOBJ->unidades}, imagen='{$jsonOBJ->imagen}', descripcion='{$descripcion}' WHERE id={$jsonOBJ->id}";

            if ($this->conexion->query($sql)) {
                $this->data['status'] =  "success";
                $this->data['message'] =  "Producto actualizado";
            } else {
                $this->data['message'] = "ERROR: No se ejecuto $sql. " . mysqli_error($this->conexion);
            }
            $this->conexion->close();
        }
    }

    public function getData()
    {
        // SE HACE LA CONVERSIÓN DE ARRAY A JSON
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }
}
