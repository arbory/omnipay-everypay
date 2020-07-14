<?php
namespace Omnipay\EveryPay\Messages;

class AuthorizeRequest extends AbstractRequest
{
    public function apiSchemeName()
    {
        return 'make_oneoff_payment';
    }

    public function getData()
    {
        $data = parent::getData();

        $data['amount'] = $this->getAmount();
        $data['order_reference'] = $this->getTransactionId();
        $data['account_name'] = $this->getAccountName();
        $data['nonce'] = $this->nonce();
        $data['timestamp'] = $this->timestamp();

        return $data;
    }
}
