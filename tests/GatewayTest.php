<?php
namespace Omnipay\EveryPay\Tests;

use Omnipay\EveryPay\Gateway;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\Http\ClientInterface;
use Http\Adapter\Guzzle6\Client;
use \VCR\VCR;

class GatewayTest extends GatewayTestCase
{
    private static $loadedEnvVars = false;

    public function loadEnvVars()
    {
        if (self::$loadedEnvVars) {
            return;
        }

        $env = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES);

        foreach ($env as $var) {
            putenv($var);
        }

        self::$loadedEnvVars = true;
    }

    public function setUp()
    {
        VCR::turnOn();
        VCR::configure()
            ->enableRequestMatchers(array('method', 'url', 'host'));

        parent::setUp();
        $this->loadEnvVars();
        $this->gateway = new Gateway();
        $this->gateway->setTestMode(true);
    }

    public function testMissingRequiredParam()
    {
        $request = $this->gateway->completeAuthorize([
            'transactionReference' => '68c18cd169333079f0cb231837ec41f93068f74fcb1cbb1672e2a215cb190eb4'
        ]);

        $this->expectException(\Omnipay\Common\Exception\RuntimeException::class);
        $this->expectExceptionMessage('Missing required param: customer_url');

        $request = $this->gateway->authorize([
                              'amount' => '1.28',
                          ]);
        $response = $request->send();
    }

    public function testFailedResponseWithEmptyBody()
    {
        VCR::insertCassette('failed_response_with_empty_body.yml');

        $request = $this->gateway->completeAuthorize([
            'transactionReference' => '68c18cd169333079f0cb231837ec41f93068f74fcb1cbb1672e2a215cb190eb4'
        ]);

        $this->expectException(\Omnipay\Common\Exception\InvalidResponseException::class);
        $this->expectExceptionMessage('Non parsable response received from the EveryPay API, status code: 401, body:');

        $response = $request->send();
    }

    public function testFailedResponseWithServerError()
    {
        VCR::insertCassette('failed_response_with_server_error.yml');

        $request = $this->gateway->completeAuthorize([
            'transactionReference' => '68c18cd169333079f0cb231837ec41f93068f74fcb1cbb1672e2a215cb190eb4'
        ]);

        $this->expectException(\Omnipay\Common\Exception\InvalidResponseException::class);
        $this->expectExceptionMessage(
            'EveryPay API gateway error, status code: 500, ' .
            'body: {"error":{"code":4032,"message":"Can not be captured"}}'
        );

        $response = $request->send();
    }

    public function testFailedResponseWithNotFoundError()
    {
        VCR::insertCassette('failed_response_with_not_found_error.yml');

        $request = $this->gateway->completeAuthorize([
            'transactionReference' => '68c18cd169333079f0cb231837ec41f93068f74fcb1cbb1672e2a215cb190eb4'
        ]);

        $this->expectException(\Omnipay\Common\Exception\InvalidResponseException::class);
        $this->expectExceptionMessage(
            'EveryPay API gateway error, status code: 404, ' .
            'body: {"status":404,"error":"Not Found"}'
        );

        $response = $request->send();
    }

    public function testSuccesfulAuthorization()
    {
        VCR::insertCassette('succesful_authorization.yml');
        $callbackUrl = 'https://shop.example.com/cart';
        $transactionId = 'order-12122';

        $authorize = $this->gateway->authorize([
            'amount' => '1.28',
            'transactionId' => $transactionId,
            'email' => 'user@example.com',
            'customerIp' => '1.2.3.4',
            'callbackUrl' => $callbackUrl,
            'customerUrl' => $callbackUrl,
        ]);

        $response = $authorize->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());

        $this->assertEquals($response->getData()->email, 'user@example.com');
        $this->assertEquals($response->getData()->customer_ip, '1.2.3.4');
        $this->assertEquals($response->getData()->initial_amount, 1.28);
        $this->assertEquals($response->getData()->standing_amount, 1.28);
        $this->assertEquals($response->getData()->customer_url, 'https://shop.example.com/cart');
        $this->assertEquals($response->getTransactionId(), $transactionId);
        $this->assertEquals(
            $response->getTransactionReference(),
            '4c9c81b1fc9f20274815626e9f1a3b7bee01eefd0796191d842e346e8a69859e'
        );
        $this->assertEquals($response->getRedirectMethod(), 'GET');
        $this->assertEquals($response->getRedirectUrl(), 'https://igw-seb-demo.every-pay.com/lp/qh4z5y/qh92uo');


        $completeAuthorize = $this->gateway->completeAuthorize([
            'transactionReference' => $response->getTransactionReference()
        ]);

        $response = $completeAuthorize->send();
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals($response->getData()->email, 'user@example.com');
        $this->assertEquals($response->getData()->customer_ip, '80.89.72.101');
        $this->assertEquals($response->getData()->initial_amount, 1.28);
        $this->assertEquals($response->getData()->standing_amount, 1.28);
        $this->assertEquals($response->getData()->customer_url, 'https://shop.example.com/cart');
        $this->assertEquals($response->getTransactionId(), 'order-12122');
        $this->assertEquals($response->getCard()->getBrand(), 'master_card');
        $this->assertEquals($response->getCard()->getName(), 'Every Pay');
        $this->assertEquals($response->getCard()->getNumber(), '1002');
        $this->assertEquals($response->getCard()->getExpiryYear(), 2025);
        $this->assertEquals($response->getCard()->getExpiryMonth(), 12);
        $this->assertEquals($response->getCard()->getToken(), null);
    }

    public function testSuccesfulAuthorizationWithCardTokenRequest()
    {
        VCR::insertCassette('succesful_authorization_with_card_token_request.yml');
        $callbackUrl = 'https://shop.example.com/cart';
        $transactionId = 'order-12122';

        $authorize = $this->gateway->authorize([
            'amount' => '1.28',
            'transactionId' => $transactionId,
            'email' => 'user@example.com',
            'customerIp' => '1.2.3.4',
            'requestToken' => true,
            'tokenAgreement' => 'unscheduled',
            'callbackUrl' => $callbackUrl,
            'customerUrl' => $callbackUrl,
        ]);

        $response = $authorize->send();

        $this->assertTrue($response->isSuccessful());

        $completeAuthorize = $this->gateway->completeAuthorize([
            'transactionReference' => $response->getTransactionReference()
        ]);

        $response = $completeAuthorize->send();
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals($response->getTransactionId(), 'order-12122');
        $this->assertEquals($response->getCard()->getBrand(), 'master_card');
        $this->assertEquals($response->getCard()->getName(), 'Every Pay');
        $this->assertEquals($response->getCard()->getNumber(), '1002');
        $this->assertEquals($response->getCard()->getExpiryYear(), 2025);
        $this->assertEquals($response->getCard()->getExpiryMonth(), 12);
        $this->assertEquals($response->getCard()->getToken(), 'd7061ee671dfb823fa57aac6');
    }

    public function testFailedAuthorization()
    {
        VCR::insertCassette('failed_authorization.yml');

        $transactionId = 'order-12122';
        $callbackUrl = 'https://shop.example.com/cart';

        $authorize = $this->gateway->authorize([
            'amount' => '1.28',
            'transactionId' => $transactionId,
            'callbackUrl' => $callbackUrl,
            'customerUrl' => $callbackUrl,
        ]);

        $response = $authorize->send();
        $this->assertTrue($response->isSuccessful());

        $completeAuthorize = $this->gateway->completeAuthorize([
            'transactionReference' => $response->getTransactionReference()
        ]);

        $response = $completeAuthorize->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals($response->getData()->payment_state, 'failed');
    }

    public function testIncompleteAuthorization()
    {
        VCR::insertCassette('incomplete_authorization.yml');

        $transactionId = 'order-12122';
        $callbackUrl = 'https://shop.example.com/cart';

        $authorize = $this->gateway->authorize([
            'amount' => '1.28',
            'transactionId' => $transactionId,
            'callbackUrl' => $callbackUrl,
            'customerUrl' => $callbackUrl,
        ]);

        $response = $authorize->send();
        $this->assertTrue($response->isSuccessful());

        $completeAuthorize = $this->gateway->completeAuthorize([
            'transactionReference' => $response->getTransactionReference()
        ]);

        $response = $completeAuthorize->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals($response->getData()->payment_state, 'initial');
    }
}
