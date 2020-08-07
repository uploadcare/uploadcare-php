<?php

namespace Uploadcare\Interfaces;

use GuzzleHttp\ClientInterface;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;

/**
 * Uploadcare API configuration.
 */
interface ConfigurationInterface
{
    /**
     * Http request headers.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * @return string
     */
    public function getPublicKey();

    /**
     * @return SignatureInterface
     */
    public function getSecureSignature();

    /**
     * @return ClientInterface
     */
    public function getClient();

    /**
     * @return SerializerInterface
     */
    public function getSerializer();

    /**
     * @param string                  $method
     * @param string                  $uri
     * @param string                  $data
     * @param string                  $contentType
     * @param \DateTimeInterface|null $date
     *
     * @return array
     */
    public function getAuthHeaders($method, $uri, $data, $contentType = 'application/json', $date = null);

    /**
     * @return AuthUrlConfigInterface|null
     */
    public function getAuthUrlConfig();
}
