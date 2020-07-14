<?php
namespace Omnipay\EveryPay\Messages;

use Exception;
use Omnipay\EveryPay\Common\Card;

/**
 * Response
 */
class CompleteAuthorizeResponse extends AbstractResponse
{
    protected $message;

    public function getCard()
    {
        return Card::make($this->data->cc_details);
    }
}
