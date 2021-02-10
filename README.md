EveryPay gateway for Omnipay
=============================================

[![Build Status](https://travis-ci.org/arbory/omnipay-everypay.svg?branch=master)](https://travis-ci.org/arbory/omnipay-everypay)
[![Coverage Status](https://coveralls.io/repos/github/arbory/omnipay-everypay/badge.svg?branch=master)](https://coveralls.io/github/arbory/omnipay-everypay?branch=master)

## Usage

Require the package using composer:

> composer require arbory/omnipay-everypay

### Initialize the gateway

```php
$gateway = Omnipay::create('EveryPay')->initialize([
  'username' => '', // EveryPay api username
  'secret' => '', // EveryPay api secret
  'accountName' => '', // merchant account name
  'gatewayUrl' => 'https://igw-demo.every-pay.com/api/v3', // use merchant provided url for production
]);
```

### Process a authorize (Gateway)

```php
$authorize = $gateway
    ->authorize([
      'amount' => '1.28',
      'transactionId' => uniqid(),
      'email' => 'user@example.com',
      'customerIp' => '1.2.3.4',
      'callbackUrl' => 'https://shop.example.com/cart',
      'customerUrl' => 'https://shop.example.com/cart'
    ]);

$response = $authorize->send();

// Gateway transaction reference
$response->getTransactionReference();

return $response->redirect(); // this will call redirect to payment portal
```

### Complete Payment (handle Gateway redirect from EveryPay)

EveryPay will return to your callback url with a `GET` request once the payment is finalized.
You need to validate this response and check if the payment succeeded.

```php
// Here, pass the payment array that we previously stored when creating the payment
$response = $gateway->completeAuthorize(['transactionReference' => 'foo')->send();

if ($response->isSuccessful()) {
  // Payment succeeded!
}

// Payment succeeded!
// Here's your payment reference number: $response->getTransactionReference()
```
