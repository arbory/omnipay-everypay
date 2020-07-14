<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\AbstractResponse as CommonAbstractResponse;
use Omnipay\Common\Exception\InvalidResponseException;

abstract class AbstractResponse extends CommonAbstractResponse
{
    public function __construct(RequestInterface $request, $response)
    {
        $this->request = $request;
        $this->data = json_decode($response->getBody());

        if ($this->data == null) {
            throw new InvalidResponseException('Non parsable response received from the EveryPay API, status code: '
                 . $response->getStatusCode() . ', body: ' . $response->getBody());
        } elseif ($response->getStatusCode() == 500) {
            throw new InvalidResponseException('EveryPay API gateway error, status code: '
                . $response->getStatusCode() . ', body: ' . $response->getBody());
        }
    }

    public function isSuccessful()
    {
        return isset($this->data->payment_state) && in_array(
            $this->data->payment_state,
            $this->request->scheme["response"]["successful_states"]
        );
    }

    public function getTransactionId()
    {
        return $this->data->order_reference;
    }

    public function getTransactionReference()
    {
        return $this->data->payment_reference;
    }

    public function getRedirectUrl()
    {
        return $this->data->payment_link;
    }
}
