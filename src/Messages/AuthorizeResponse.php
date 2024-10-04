<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\EveryPay\Common\PaymentMethod;
use Omnipay\EveryPay\Interfaces\MobilePaymentResponse;

/**
 * Response
 */
class AuthorizeResponse extends AbstractResponse implements RedirectResponseInterface, MobilePaymentResponse
{
    public function isRedirect()
    {
        return true;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectUrl()
    {
        $paymentMethod = $this->request->getPaymentMethod();
        $paymentMethodUrl = $paymentMethod ? $this->getPaymentMethodPaymentUrl($paymentMethod) : null;

        return $paymentMethodUrl ?? parent::getRedirectUrl();
    }

    /**
     * @param string $source
     * @return string|null
     */
    public function getPaymentMethodPaymentUrl(string $source): ?string
    {
        foreach ($this->getPaymentMethods() as $paymentMethod) {
            if ($paymentMethod->getSource() === $source) {
                return $paymentMethod->getPaymentLink();
            }
        }

        return null;
    }

    /**
     * @return array|PaymentMethod[]
     */
    public function getPaymentMethods(): array
    {
        return array_map(function (object $paymentMethod) {
            return new PaymentMethod($paymentMethod);
        }, $this->data->payment_methods);
    }

    /**
     * @return array
     */
    public function getApplePayData(): array
    {
        return [
            'mobile_access_token' => $this->getMobileAccessToken(),
            'merchant_id' => $this->data->applepay_merchant_identifier,
            'api_username' => $this->data->api_username,
            'account_name' => $this->data->account_name,
            'payment_reference' => $this->data->payment_reference,
            'country_code' => $this->data->descriptor_country,
            'currency_code' => $this->data->currency,
            'payment_link' => $this->data->payment_link,
            'total_amount' => $this->data->initial_amount,
            'total_label' => (string)$this->data->initial_amount,
        ];
    }

    /**
     * @return array
     */
    public function getGooglePayData(): array
    {
        return [
            'mobile_access_token' => $this->getMobileAccessToken(),
            'api_username' => $this->data->api_username,
            'account_name' => $this->data->account_name,
            'payment_reference' => $this->data->payment_reference,
            'country_code' => $this->data->descriptor_country,
            'currency_code' => $this->data->currency,
            'payment_link' => $this->data->payment_link,
            'total_amount' => (string)$this->data->initial_amount,
        ];
    }

    /**
     * @return bool
     */
    public function isApplePay(): bool
    {
        return $this->getMobileAccessToken() && $this->request->getPaymentMethod() === PaymentMethod::SOURCE_APPLE_PAY;
    }

    /**
     * @return bool
     */
    public function isGooglePay(): bool
    {
        return $this->getMobileAccessToken() && $this->request->getPaymentMethod() === PaymentMethod::SOURCE_GOOGLE_PAY;
    }

    /**
     * @return string|null
     */
    public function getMobileAccessToken(): ?string
    {
        return $this->data->mobile_access_token ?? null;
    }
}
