<?php

namespace Evans\Directpay;

use Evans\Directpay\OrderInterface;
use Evans\Directpay\XMLHelper;
use Evans\Directpay\Client;

/**
 * Order Interface
 *
 * @author Evans Mwendwa
 */
class Directpay
{
    protected $companyToken;
    protected $acceptableCurrencies;

    /**
     * Class Constructor
     *
     * @param string $apikey
     * @param string $endpoint
     *
     * @return void
     */
    public function __construct($companyToken, $endpoint)
    {
        $this->companyToken = $companyToken;
        $this->endpoint = $endpoint;
        $this->acceptableCurrencies = ['USD','ZMW','TZS','KES','RWF','EUR','GBP','UGX'];
    }

    /**
     * create an Order
     *
     * @param Evans\Directpay\OrderInterface $Order
     *
     * @return void
     */
    public function createOrder(OrderInterface $order)
    {
        $post_xml = XMLHelper::createTransactionXML($order, $this->companyToken);

        //$dpoResponse = Client::sendXMLRequest($this->endpoint, $post_xml);

        $dpoResponse = Client::mockRequest($this->endpoint, $post_xml);

        if(false === $dpoResponse) {
            return $this->errorResponse($order);
        }

        if(isset($dpoResponse->TransToken)) {
            $order->setTransactionToken($dpoResponse->TransToken);
        }

        if(isset($dpoResponse->TransRef)) {
            $order->setTransactionReference($dpoResponse->TransRef);
        }

        return $this->preparedResponse($dpoResponse, $order);
    }

    /**
     * pay with credit card
     *
     * @param Evans\Directpay\OrderInterface $Order
     *
     * @return void
     */
    public function payWithCreditCard(OrderInterface $order)
    {

    }

    /**
     * pay with mobile money
     *
     * @param Evans\Directpay\OrderInterface $Order
     *
     * @return void
     */
    public function payWithMobileMoney(OrderInterface $order)
    {

    }

    /**
     * verifyPayment
     *
     * @param Evans\Directpay\OrderInterface $Order
     *
     * @return void
     */
    public function verifyPayment(OrderInterface $order)
    {

    }

    private function errorResponse($payload = []) {
        return [
            'status' => 'error',
            'code' => 400,
            'description' => 'Invalid merchant response',
            'payload' => $payload
        ];
    }

    private function preparedResponse($dpoResponse, $payload = []) {
        $status = 'error';

        if(isset($dpoResponse->Result) && $dpoResponse->Result === '000') {
            $status = 'success';
        }

        $response = [
            'status' => $status,
            'code' => '',
            'description' => '',
            'payload' => $payload
        ];

        if(isset($dpoResponse->Result)) {
            $response['code'] = $dpoResponse->Result;
        }

        if(isset($dpoResponse->ResultExplanation)) {
            $response['description'] = $dpoResponse->ResultExplanation;
        }

        return $response;
    }
}
