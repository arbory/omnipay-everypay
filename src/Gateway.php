<?php
namespace Omnipay\EveryPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\EveryPay\Messages\AuthorizeRequest;
use Omnipay\EveryPay\Messages\CompleteAuthorizeRequest;

class Gateway extends AbstractGateway
{
    use Concerns\Parameters;

    public function getName()
    {
        return 'EveryPay';
    }

    public function authorize(array $parameters = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $parameters);
    }

    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest(CompleteAuthorizeRequest::class, $parameters);
    }
}
