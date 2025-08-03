<?php
// cSpell:disable (Ignora errores de ortografía en este archivo)

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    $config = include 'config/database.php';
    $conn = new mysqli(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );

    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }

    // Ejecutar el procedimiento almacenado
    $sql = "CALL PrecargarDatosMantenimiento()";
    
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                while ($row = $result->fetch_assoc()) {
                    $response['data'][] = $row;
                }
                $result->free();
            }
        } while ($conn->next_result());

        $response['success'] = true;
        $response['message'] = 'Datos precargados correctamente';
    } else {
        throw new Exception("Error al ejecutar el procedimiento: " . $conn->error);
    }

    $conn->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>