<?php
namespace Omnipay\EveryPay\Messages;

class CompleteAuthorizeRequest extends AbstractRequest
{
    public function apiSchemeName()
    {
        return 'payment_reference';
    }

    public function requestPath()
    {
        return str_replace(":payment_reference", $this->getTransactionReference(), parent::requestPath());
    }
}
