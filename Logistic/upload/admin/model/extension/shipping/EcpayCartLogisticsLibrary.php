<?php

class EcpayCartLogisticsLibrary
{
    // Return messages
    private $returnMessages = [
        '2067' => '門市店EC代收(C2C成功取件)',
        '300' => '訂單處理中(已收到訂單資料)',
        '3022' => '買家已到店取貨',
    ];

    // Simulation data file path
    private $filePath = '/tmp/SimulatorData.txt';

    /**
     * Save the simulation data
     * @param  array $aio        AIO
     * @param  array $response   Response data
     * @param  array $returnCode Return code
     * @param  array $service    Service name
     * @return integer
     */
    public function easySaveSimulationData($aio, $response, $returnCode, $service)
    {
        $data = $this->getSimulationData($aio, $response, $returnCode, $service);
        return $this->saveSimulationData($data);
    }

    /**
     * Get the LogisticsID name
     * @param  string $service Logistics service name
     * @return string
     */
    public function getLogisticsIdName($service)
    {
        if ($service === 'opay') {
            return 'AllPayLogisticsID';
        }

        if ($service === 'ecpay') {
            return 'AllPayLogisticsID';
        }
        return '';
    }

    /**
     * Get the logistics return message
     * @param  string $code Return code
     * @return string
     */
    public function getReturnMessage($code)
    {
        return $this->returnMessages[$code];
    }

    /**
     * Parse the simulation data
     * @return array
     */
    public function parseSimulationData()
    {
        $content = file_get_contents($this->filePath);
        $data = explode(',', $content);
        $list = [
            'MerchantID',
            'HashKey',
            'HashIV',
            'LogisticsID',
            'MerchantTradeNo',
            'RtnCode',
            'RtnMsg',
            'LogisticsType',
            'LogisticsSubType',
            'GoodsAmount',
            'UpdateStatusDate',
            'CVSPaymentNo',
            'CVSValidationNo',
            'ReceiverName',
            'ReceiverPhone',
            'ReceiverCellPhone',
            'ReceiverEmail',
            'ReceiverAddress',
            'BookingNote',
            'ServiceURL',
        ];
        foreach ($list as $index => $name) {
            $parsed[$name] = $data[$index];
        }
        return $parsed;
    }

    /**
     * Get the simulation data
     * @param  array $aio        AIO
     * @param  array $response   Response data
     * @param  array $returnCode Return code
     * @param  array $service    Service name
     * @return string
     */
    public function getSimulationData($aio, $response, $returnCode, $service)
    {
        $send = $aio->Send;
        $data = [
            'MerchantID' => $send['MerchantID'],
            'HashKey' => $aio->HashKey,
            'HashIV' => $aio->HashIV,
            'LogisticsID' => $response[$this->getLogisticsIdName($service)],
            'MerchantTradeNo' => substr($send['MerchantTradeNo'], 19),
            'RtnCode' => $returnCode,
            'RtnMsg' => $this->getReturnMessage($returnCode),
            'LogisticsType' => $send['LogisticsType'],
            'LogisticsSubType' => $send['LogisticsSubType'],
            'GoodsAmount' => $send['GoodsAmount'],
            'UpdateStatusDate' => date('Y/m/d H:i:s'),
            'CVSPaymentNo' => '',
            'CVSValidationNo' => '',
            'ReceiverName' => $send['ReceiverName'],
            'ReceiverPhone' => '',
            'ReceiverCellPhone' => $send['ReceiverCellPhone'],
            'ReceiverEmail' => $send['ReceiverEmail'],
            'ReceiverAddress' => '',
            'BookingNote' => '',
            'ServiceURL' => $send['ServerReplyURL'],
        ];
        return $data;
    }

    /**
     * Save the simulation data
     * @param  array   $data Simulation data
     * @return integer
     */
    public function saveSimulationData($data)
    {
        return file_put_contents($this->filePath, implode(',', $data));
    }
}