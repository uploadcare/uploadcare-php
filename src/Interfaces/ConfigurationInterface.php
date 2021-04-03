<?php declare(strict_types=1);

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
     */
    public function getHeaders(): array;

    public function getPublicKey(): string;

    public function getSecureSignature(): SignatureInterface;

    public function getClient(): ClientInterface;

    public function getSerializer(): SerializerInterface;

    /**
     * @param string                  $method
     * @param string                  $uri
     * @param string                  $data
     * @param string                  $contentType
     * @param \DateTimeInterface|null $date
     *
     * @return array
     */
    public function getAuthHeaders(string $method, string $uri, string $data, string $contentType = 'application/json', $date = null): array;

    public function getAuthUrlConfig(): ?AuthUrlConfigInterface;

    public function setAuthUrlConfig(AuthUrlConfigInterface $config): ConfigurationInterface;
}
