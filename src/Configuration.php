<?php

namespace Uploadcare;

use GuzzleHttp\ClientInterface;
use Uploadcare\Client\ClientFactory;
use Uploadcare\Interfaces\ClientFactoryInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Interfaces\SignatureInterface;
use Uploadcare\Security\Signature;

/**
 * Uploadcare Api Configuration.
 */
class Configuration
{
    const LIBRARY_VERSION = 'v3.0.0';

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
     * @param string                      $publicKey     Uploadcare API public key
     * @param string                      $privateKey    Uploadcare API private key
     * @param array                       $clientOptions Parameters for Http client (proxy, special headers, etc.)
     * @param ClientFactoryInterface|null $clientFactory
     *
     * @return Configuration
     */
    public static function create($publicKey, $privateKey, array $clientOptions = [], ClientFactoryInterface $clientFactory = null)
    {
        $signature = new Signature($privateKey);
        $client = $clientFactory !== null ? $clientFactory::createClient($clientOptions) : ClientFactory::createClient($clientOptions);

        return new static($publicKey, $signature, $client);
    }

    /**
     * Configuration constructor.
     *
     * @param $publicKey
     * @param SignatureInterface $secureSignature
     * @param ClientInterface    $client
     */
    public function __construct($publicKey, SignatureInterface $secureSignature, ClientInterface $client)
    {
        $this->publicKey = $publicKey;
        $this->secureSignature = $secureSignature;
        $this->client = $client;
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
        $exists = \array_filter($headers, static function ($header) {
            return \is_string($header) && \strtolower($header) === 'user-agent';
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($exists) && isset($exists[0]) && \is_string($exists[0])) {
            $headers['User-Agent'] = \sprintf('%s (php-%s)', $exists[0], PHP_VERSION);

            return;
        }

        $headers['User-Agent'] = \sprintf('Uploadcare PHP client %s, (php-%s)', self::LIBRARY_VERSION, PHP_VERSION);
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
}
