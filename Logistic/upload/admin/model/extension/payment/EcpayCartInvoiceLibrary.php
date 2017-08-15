<?php

class EcpayCartInvoiceLibrary
{
    /**
     * Get the invoice service
     * @param  string $opayStatus  O'Pay status
     * @param  string $ecpayStatus ECPay status
     * @return string
     */
    public function getInvoiceService($opayStatus, $ecpayStatus)
    {
        $service = '';
        if ($opayStatus === '1' and $ecpayStatus !== '1') {
            $service = 'opay';
        }
        if ($ecpayStatus === '1' and $opayStatus !== '1') {
            $service = 'ecpay';
        }
        return $service;
    }
}