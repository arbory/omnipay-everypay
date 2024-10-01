<?php

namespace Omnipay\EveryPay\Common;

use Omnipay\Common\ParametersTrait;

class PaymentMethod
{
    use ParametersTrait;

    public const SOURCE_APPLE_PAY = 'apple_pay';
    public const SOURCE_GOOGLE_PAY = 'google_pay';
    private const SOURCE_CARD = 'card';

    /**
     * PaymentMethod constructor.
     * @param object $parameters
     */
    public function __construct(object $parameters)
    {
        $this->initialize((array) $parameters);
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->getParameter('source');
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->getParameter('country_code');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getParameter('display_name');
    }

    /**
     * @return string
     */
    public function getLogoUrl(): string
    {
        return $this->getParameter('logo_url');
    }

    /**
     * @return string|null
     */
    public function getPaymentLink(): ?string
    {
        return $this->getParameter('payment_link');
    }

    /**
     * @return bool
     */
    public function isCard(): bool
    {
        return $this->getSource() === self::SOURCE_CARD;
    }

    /**
     * @return bool
     */
    public function isApplePayAvailable(): bool
    {
        return $this->getParameter('applepay_available') === true;
    }

    /**
     * @return bool
     */
    public function isGooglePayAvailable(): bool
    {
        return $this->getParameter('googlepay_available') === true;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSource($value): self
    {
        return $this->setParameter('source', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCountryCode($value): self
    {
        return $this->setParameter('country_code', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDisplayName($value): self
    {
        return $this->setParameter('display_name', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setLogoUrl($value): self
    {
        return $this->setParameter('logo_url', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentLink($value): self
    {
        return $this->setParameter('payment_link', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApplepayAvailable($value): self
    {
        return $this->setParameter('applepay_available', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setGooglepayAvailable($value): self
    {
        return $this->setParameter('googlepay_available', $value);
    }
}
