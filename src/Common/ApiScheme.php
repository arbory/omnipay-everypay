<?php
namespace Omnipay\EveryPay\Common;

class ApiScheme
{
    const POST_REQUEST = "POST";
    const GET_REQUEST = "GET";

    const REQUIRED_PARAM = "required";
    const OPTIONAL_PARAM = "optional";

    const PAYMENT_STATE_INITIAL = "initial";
    const PAYMENT_STATE_SETTLED = "settled";

    const SCHEME = [
        "make_oneoff_payment" => [
            "request" => [
                "method" => self::POST_REQUEST,
                "path" => "/payments/oneoff",
                "params" => [
                    "api_username" => self::REQUIRED_PARAM,
                    "account_name" => self::REQUIRED_PARAM,
                    "amount" => self::REQUIRED_PARAM,
                    "customer_url" => self::REQUIRED_PARAM,
                    "token_agreement" => self::OPTIONAL_PARAM,
                    "mobile_payment" => self::OPTIONAL_PARAM,
                    "order_reference" => self::OPTIONAL_PARAM,
                    "nonce" => self::REQUIRED_PARAM,
                    "email" => self::OPTIONAL_PARAM,
                    "customer_ip" => self::OPTIONAL_PARAM,
                    "preferred_country" => self::OPTIONAL_PARAM,
                    "billing_city" => self::OPTIONAL_PARAM,
                    "billing_country" => self::OPTIONAL_PARAM,
                    "billing_line1" => self::OPTIONAL_PARAM,
                    "billing_line2" => self::OPTIONAL_PARAM,
                    "billing_line3" => self::OPTIONAL_PARAM,
                    "billing_postcode" => self::OPTIONAL_PARAM,
                    "billing_state" => self::OPTIONAL_PARAM,
                    "shipping_city" => self::OPTIONAL_PARAM,
                    "shipping_country" => self::OPTIONAL_PARAM,
                    "shipping_line1" => self::OPTIONAL_PARAM,
                    "shipping_line2" => self::OPTIONAL_PARAM,
                    "shipping_line3" => self::OPTIONAL_PARAM,
                    "shipping_code" => self::OPTIONAL_PARAM,
                    "shipping_state" => self::OPTIONAL_PARAM,
                    "locale" => self::OPTIONAL_PARAM,
                    "request_token" => self::OPTIONAL_PARAM,
                    "token_consent_agreed" => self::OPTIONAL_PARAM,
                    "timestamp" => self::REQUIRED_PARAM,
                    "skin_name" => self::OPTIONAL_PARAM,
                    "integration_details" => self::OPTIONAL_PARAM
                ]
            ],
            "response" => [
                "successful_states" => [self::PAYMENT_STATE_INITIAL]
            ]
        ],
        "payment_reference" => [
          "request" => [
            "method" => self::GET_REQUEST,
            "path" => "/payments/:payment_reference",
            "params" => [
                "api_username" => self::REQUIRED_PARAM
            ]
          ],
          "response" => [
            "successful_states" => [self::PAYMENT_STATE_SETTLED]
          ]
        ],
    ];

    public static function get($name)
    {
        return self::SCHEME[$name];
    }
}
