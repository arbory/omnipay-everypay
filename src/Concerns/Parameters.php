<?php
namespace Omnipay\EveryPay\Concerns;

trait Parameters
{
    public function getDefaultParameters()
    {
        return [
            'username' => getenv('EVERY_PAY_API_USERNAME'), // api_username
            'secret' => getenv('EVERY_PAY_API_SECRET'), // api_secret
            'accountName' => getenv('EVERY_PAY_ACCOUNT_NAME'), // processing account
            'gatewayUrl' => 'https://igw-demo.every-pay.com/api/v3', // use merchant provided url for production
        ];
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($username)
    {
        return $this->setParameter('username', $username);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($secret)
    {
        return $this->setParameter('secret', $secret);
    }

    public function getAccountName()
    {
        return $this->getParameter('accountName');
    }

    public function setAccountName($accountName)
    {
        return $this->setParameter('accountName', $accountName);
    }

    public function getGatewayUrl()
    {
        return $this->getParameter('gatewayUrl');
    }

    public function setGatewayUrl($gatewayUrl)
    {
        return $this->setParameter('gatewayUrl', $gatewayUrl);
    }
}
