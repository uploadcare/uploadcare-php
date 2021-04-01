<?php

namespace Uploadcare;

use GuzzleHttp\ClientInterface;
use Uploadcare\Client\ClientFactory;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;
use Uploadcare\Interfaces\ClientFactoryInterface;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\Serializer\SerializerFactoryInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Interfaces\SignatureInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

/**
 * Uploadcare Api Configuration.
 */
class Configuration implements ConfigurationInterface
{
    const LIBRARY_VERSION = 'v3.0.4';
    const API_VERSION = '0.6';
    const API_BASE_URL = 'api.uploadcare.com';
    const USER_AGENT_TEMPLATE = 'PHPUploadcare/{lib-version}/{publicKey} (PHP/{lang-version})';

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var SignatureInterface
     */
    private $secureSignature;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var AuthUrlConfigInterface|null
     */
    private $authUrlConfig;

    /**
     * @var string
     */
    private $frameworkVersion = null;

    /**
     * @param string                          $publicKey         Uploadcare API public key
     * @param string                          $secretKey        Uploadcare API private key
     * @param array                           $clientOptions     Parameters for Http client (proxy, special headers, etc.)
     * @param ClientFactoryInterface|null     $clientFactory
     * @param SerializerFactoryInterface|null $serializerFactory
     *
     * @return Configuration
     */
    public static function create($publicKey, $secretKey, array $clientOptions = [], ClientFactoryInterface $clientFactory = null, SerializerFactoryInterface $serializerFactory = null)
    {
        $signature = new Signature($secretKey);
        $framework = isset($clientOptions['framework']) ? $clientOptions['framework'] : null;
        $client = $clientFactory !== null ? $clientFactory::createClient($clientOptions) : ClientFactory::createClient($clientOptions);
        $serializer = $serializerFactory !== null ? $serializerFactory::create() : SerializerFactory::create();

        $config = new static($publicKey, $signature, $client, $serializer);
        $config->setFrameworkOptions($framework);

        return $config;
    }

    public function setFrameworkOptions($framework = null)
    {
        if (\is_array($framework)) {
            $framework = \implode('/', $framework);
        }

        if (\is_string($framework)) {
            $this->frameworkVersion = $framework;
        }
    }

    /**
     * Configuration constructor.
     *
     * @param string              $publicKey
     * @param SignatureInterface  $secureSignature
     * @param ClientInterface     $client
     * @param SerializerInterface $serializer
     */
    public function __construct($publicKey, SignatureInterface $secureSignature, ClientInterface $client, SerializerInterface $serializer)
    {
        $this->publicKey = $publicKey;
        $this->secureSignature = $secureSignature;
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param AuthUrlConfigInterface $config
     *
     * @return $this
     */
    public function setAuthUrlConfig(AuthUrlConfigInterface $config)
    {
        $this->authUrlConfig = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = $this->client->getConfig('headers');
        if (!\is_array($headers) || empty($headers)) {
            $headers = [];
        }
        $this->setUserAgent($headers);

        return $headers;
    }

    protected function setUserAgent(array &$headers)
    {
        $info = [
            '{lib-version}' => self::LIBRARY_VERSION,
            '{publicKey}' => $this->publicKey,
            '{lang-version}' => sprintf('%s.%s.%s', PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION),
        ];
        if ($this->frameworkVersion !== null) {
            $info['{lang-version}'] .= '; ' . $this->frameworkVersion;
        }

        $value = \strtr(self::USER_AGENT_TEMPLATE, $info);

        $headers['User-Agent'] = $value;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return SignatureInterface
     */
    public function getSecureSignature()
    {
        return $this->secureSignature;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param string                  $method
     * @param string                  $uri
     * @param string                  $data
     * @param string                  $contentType
     * @param \DateTimeInterface|null $date
     *
     * @return array
     */
    public function getAuthHeaders($method, $uri, $data, $contentType = 'application/json', $date = null)
    {
        return [
            'Date' => $this->getSecureSignature()->getDateHeaderString($date),
            'Authorization' => \sprintf('Uploadcare %s:%s', $this->getPublicKey(), $this->getSecureSignature()->getAuthHeaderString($method, $uri, $data, $contentType, $date)),
            'Content-Type' => $contentType,
        ];
    }

    /**
     * @return AuthUrlConfigInterface|null
     */
    public function getAuthUrlConfig()
    {
        return $this->authUrlConfig;
    }
}
