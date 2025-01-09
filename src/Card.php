<?php

class Card
{
    private $type; // Tipo de tarjeta (Visa o AMEX)
    private $bank; // Banco emisor
    private $number; // Número de tarjeta
    private $limit; // Límite disponible
    private $client; // Datos del titular (DNI, nombre, apellido)

    /**
     * Constructor de la clase Card.
     */
    public function __construct($type, $bank, $number, $limit, $client)
    {
        $this->type = $type;
        $this->bank = $bank;
        $this->number = $number;
        $this->limit = $limit;
        $this->client = $client;
    }

    /**
     * Obtiene el límite disponible de la tarjeta.
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Reduce el límite disponible de la tarjeta.
     *
     * @param float $amount Monto a descontar.
     */
    public function reduceLimit($amount)
    {
        $this->limit -= $amount;
    }

    /**
     * Obtiene los datos del titular de la tarjeta.
     */
    public function getClient()
    {
        return $this->client;
    }
}
