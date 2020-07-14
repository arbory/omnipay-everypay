<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class AuthorizeResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isRedirect()
    {
        return true;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }
}
