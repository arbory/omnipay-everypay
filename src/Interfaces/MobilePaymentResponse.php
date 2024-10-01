<?php

namespace Omnipay\EveryPay\Interfaces;

interface MobilePaymentResponse
{
    /**
     * @return bool
     */
    public function isApplePay(): bool;

    /**
     * @return bool
     */
    public function isGooglePay(): bool;

    /**
     * @return array
     */
    public function getApplePayData(): array;

    /**
     * @return array
     */
    public function getGooglePayData(): array;
}
