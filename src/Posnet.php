<?php
require_once __DIR__ . '/Card.php'; // Importamos la clase Card que representa una tarjeta

class Posnet
{
    private $cards = []; // Array para almacenar las tarjetas registradas

    /**
     * Registra una tarjeta nueva.
     *
     * @param array $data Datos de la tarjeta a registrar.
     * @return array Respuesta con el mensaje de éxito.
     * @throws Exception Si faltan datos o son inválidos.
     */
    public function registerCard($data)
    {
        // Validamos que los campos requeridos estén presentes
        $requiredFields = ['type', 'bank', 'number', 'limit', 'client'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new Exception("El campo $field es obligatorio");
            }
        }

        // Validamos que el tipo de tarjeta sea Visa o AMEX
        if (!in_array($data['type'], ['Visa', 'AMEX'])) {
            throw new Exception('Solo se aceptan tarjetas Visa o AMEX');
        }

        // Validamos que el número de tarjeta tenga 8 dígitos
        if (strlen($data['number']) !== 8) {
            throw new Exception('El número de tarjeta debe tener 8 dígitos');
        }

        // Creamos una nueva instancia de Card
        $card = new Card(
            $data['type'],
            $data['bank'],
            $data['number'],
            $data['limit'],
            $data['client']
        );

        // Guardamos la tarjeta en el array de tarjetas registradas
        $this->cards[$data['number']] = $card;

        // Retornamos un mensaje de éxito
        return ['message' => 'Tarjeta registrada exitosamente'];
    }

    /**
     * Procesa un pago con una tarjeta registrada.
     *
     * @param array $data Datos del pago.
     * @return array Detalles del ticket generado.
     * @throws Exception Si hay errores en el pago.
     */
    public function doPayment($data)
    {
        // Validamos que los campos requeridos estén presentes
        $requiredFields = ['number', 'amount', 'installments'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new Exception("El campo $field es obligatorio");
            }
        }

        // Validamos que la tarjeta exista
        if (!isset($this->cards[$data['number']])) {
            throw new Exception('La tarjeta no está registrada');
        }

        // Obtenemos la tarjeta correspondiente
        $card = $this->cards[$data['number']];
        $totalAmount = $data['amount']; // Monto inicial
        $installments = $data['installments']; // Número de cuotas

        // Calculamos el recargo según las cuotas (3% por cada cuota adicional)
        if ($installments > 1) {
            $totalAmount += $totalAmount * (0.03 * ($installments - 1));
        }

        // Validamos que la tarjeta tenga límite suficiente
        if ($card->getLimit() < $totalAmount) {
            throw new Exception('Límite insuficiente');
        }

        // Reducimos el límite disponible en la tarjeta
        $card->reduceLimit($totalAmount);

        // Calculamos el monto por cada cuota
        $perInstallment = $totalAmount / $installments;

        // Retornamos los detalles del ticket
        return [
            'name' => $card->getClient()['name'],
            'surname' => $card->getClient()['surname'],
            'totalAmount' => round($totalAmount, 2), // Monto total redondeado a 2 decimales
            'perInstallment' => round($perInstallment, 2) // Monto por cuota redondeado
        ];
    }
}
