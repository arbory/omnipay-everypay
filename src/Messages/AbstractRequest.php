<?php
namespace Omnipay\EveryPay\Messages;

use DateTime;
use Omnipay\EveryPay\Concerns\Parameters;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\EveryPay\Common\ApiScheme;
use Omnipay\Common\Exception\RuntimeException;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    use Parameters;

    public $scheme = null;

    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);

        if (!$this->scheme) {
            $this->scheme = ApiScheme::get($this->apiSchemeName());
        }

        // workaround to skip generating setters and getters for each API param
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $method = 'set'.ucfirst(\Omnipay\Common\Helper::camelCase($key));
                if (!method_exists($this, $method)) {
                    $this->setParameter($key, $value);
                }
            }
        }

        return $this;
    }

    public function requestMethod()
    {
        return $this->scheme["request"]["method"];
    }

    public function requestPath()
    {
        return $this->scheme["request"]["path"];
    }

    public function getData()
    {
        $data = [
            'api_username' => $this->getUsername(),
        ];

        foreach ($this->scheme["request"]["params"] as $key => $type) {
            $camelCaseKey = \Omnipay\Common\Helper::camelCase($key);
            $value = $this->getParameter($camelCaseKey);
            if (!is_null($value)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function nonce()
    {
        return uniqid(true);
    }

    public function timestamp()
    {
        $objDateTime = new DateTime('NOW');
        return $objDateTime->format(DateTime::ISO8601);
    }

    public function requestUrl($data)
    {
        $url = $this->getGatewayUrl() . $this->requestPath();

        if ($this->requestMethod() == ApiScheme::GET_REQUEST) {
            $url .= '?' . http_build_query($data);
        }

        return $url;
    }

    public function requestBody($data)
    {
        if ($this->requestMethod() == ApiScheme::POST_REQUEST) {
            return json_encode($data);
        }
    }

    public function requestHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->getUsername() . ':' . $this->getSecret())
        ];
    }

    protected function createResponse($data)
    {
        $responseClass = $this->responseClass();
        return $this->response = new $responseClass($this, $data);
    }

    public function responseClass()
    {
        return preg_replace('/(.*)Request$/', '${1}Response', get_class($this));
    }

    public function sendData($data)
    {
        $this->validateData($data);

        $response = $this->httpClient->request(
            $this->requestMethod(),
            $this->requestUrl($data),
            $this->requestHeaders(),
            $this->requestBody($data)
        );

        return $this->createResponse($response);
    }

    public function validateData($data)
    {
        // validate all required params
        foreach ($this->scheme["request"]["params"] as $key => $type) {
            if ($type == ApiScheme::REQUIRED_PARAM && empty($data[$key])) {
                throw new RuntimeException('Missing required param: ' . $key);
            }
        }
    }
}
