<?php
require_once __DIR__ . '/src/Posnet.php';

// Configuramos la respuesta como JSON
header('Content-Type: application/json');

// Obtenemos el método HTTP (POST, GET, etc.) y el endpoint solicitado
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? ''; // Si no existe "endpoint", queda vacío

// Instanciamos la clase Posnet para manejar la lógica principal
$posnet = new Posnet();

try {
    // Si el endpoint es "registerCard" y el método es POST
    if ($method === 'POST' && $endpoint === 'registerCard') {
        $data = json_decode(file_get_contents('php://input'), true); // Convertimos el JSON de la solicitud en un array
        $response = $posnet->registerCard($data); // Registramos la tarjeta
        echo json_encode($response); // Retornamos la respuesta en formato JSON
    }
    // Si el endpoint es "doPayment" y el método es POST
    elseif ($method === 'POST' && $endpoint === 'doPayment') {
        $data = json_decode(file_get_contents('php://input'), true); // Convertimos el JSON de la solicitud en un array
        $response = $posnet->doPayment($data); // Procesamos el pago
        echo json_encode($response); // Retornamos la respuesta en formato JSON
    }
    // Si el endpoint no existe
    else {
        http_response_code(404); // Código de error 404: No encontrado
        echo json_encode(['error' => 'Endpoint no encontrado']);
    }
} catch (Exception $e) {
    // Si ocurre una excepción, retornamos un error controlado
    http_response_code(400); // Código de error 400: Solicitud incorrecta
    echo json_encode(['error' => $e->getMessage()]);
}
