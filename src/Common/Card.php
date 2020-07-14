<?php
namespace Omnipay\EveryPay\Common;

use Omnipay\Common\CreditCard;

class Card extends CreditCard
{
    protected $token;

    public static function make($payload)
    {
        $card = new Card([
            'brand' => $payload->type,
            'name' => $payload->holder_name,
            'number' => $payload->last_four_digits,
            'expiryYear' => $payload->year,
            'expiryMonth' => $payload->month,
        ]);

        if (isset($payload->token)) {
            $card->setToken($payload->token);
        }

        return $card;
    }

    public function setBrand($brand)
    {
        return $this->setParameter('brand', $brand);
    }

    public function getBrand()
    {
        return $this->getParameter('brand');
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
