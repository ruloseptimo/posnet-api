<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/Posnet.php';
require_once __DIR__ . '/../src/Card.php';

class PosnetTest extends TestCase
{
    private $posnet;

    protected function setUp(): void
    {
        // Inicializamos una instancia de Posnet antes de cada prueba
        $this->posnet = new Posnet();

        // Registramos una tarjeta para las pruebas
        $this->posnet->registerCard([
            'type' => 'Visa',
            'bank' => 'Banco Nación',
            'number' => '12345678',
            'limit' => 5000,
            'client' => [
                'dni' => '12345678',
                'name' => 'Cristian Eduardo',
                'surname' => 'Rojas'
            ]
        ]);
    }

    public function testDoPaymentSuccessful()
    {
        // Simulamos un pago exitoso
        $paymentData = [
            'number' => '12345678',
            'amount' => 1000,
            'installments' => 1
        ];

        $result = $this->posnet->doPayment($paymentData);

        // Verificamos que el resultado contiene los datos esperados
        $this->assertEquals('Cristian Eduardo', $result['name']);
        $this->assertEquals('Rojas', $result['surname']);
        $this->assertEquals(1000, $result['totalAmount']);
        $this->assertEquals(1000, $result['perInstallment']);
    }

    public function testDoPaymentWithInsufficientLimit()
    {
        // Simulamos un pago con un monto mayor al límite disponible
        $paymentData = [
            'number' => '12345678',
            'amount' => 6000,
            'installments' => 1
        ];

        // Esperamos una excepción debido al límite insuficiente
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Límite insuficiente');

        $this->posnet->doPayment($paymentData);
    }

    public function testDoPaymentWithInstallments()
    {
        // Simulamos un pago en cuotas
        $paymentData = [
            'number' => '12345678',
            'amount' => 1000,
            'installments' => 3
        ];

        $result = $this->posnet->doPayment($paymentData);

        // Verificamos el cálculo correcto del total y las cuotas
        $this->assertEquals(1090, round($result['totalAmount'], 2)); // 9% extra por 3 cuotas
        $this->assertEquals(363.33, round($result['perInstallment'], 2));
    }
}
