<?php declare(strict_types=1);

namespace Uploadcare;

use GuzzleHttp\ClientInterface;
use Uploadcare\Client\ClientFactory;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;
use Uploadcare\Interfaces\Serializer\{SerializerFactoryInterface, SerializerInterface};
use Uploadcare\Interfaces\SignatureInterface;
use Uploadcare\Interfaces\{ClientFactoryInterface, ConfigurationInterface};
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

/**
 * Uploadcare Api Configuration.
 */
final class Configuration implements ConfigurationInterface
{
    public const LIBRARY_VERSION = 'v4.0.1';
    public const API_VERSION = '0.7';
    public const API_BASE_URL = 'api.uploadcare.com';
    public const USER_AGENT_TEMPLATE = 'PHPUploadcare/{lib-version}/{publicKey} (PHP/{lang-version})';

    private string $publicKey;
    private SignatureInterface $secureSignature;
    private ClientInterface $client;
    private SerializerInterface $serializer;
    private ?AuthUrlConfigInterface $authUrlConfig = null;
    private ?string $frameworkVersion = null;
    private array $clientOptions = [];

    /**
     * @param string $publicKey     Uploadcare API public key
     * @param string $secretKey     Uploadcare API private key
     * @param array  $clientOptions Parameters for Http client (proxy, special headers, etc.)
     */
    public static function create(string $publicKey, string $secretKey, array $clientOptions = [], ClientFactoryInterface $clientFactory = null, SerializerFactoryInterface $serializerFactory = null): Configuration
    {
        $signature = new Signature($secretKey);
        $framework = $clientOptions['framework'] ?? null;
        $client = $clientFactory !== null ? $clientFactory::createClient($clientOptions) : ClientFactory::createClient($clientOptions);
        $serializer = $serializerFactory !== null ? $serializerFactory::create() : SerializerFactory::create();

        return (new self($publicKey, $signature, $client, $serializer))
            ->setFrameworkOptions($framework)
            ->setClientOptions($clientOptions);
    }

    /**
     * @param array|string $framework
     *
     * @return $this
     */
    public function setFrameworkOptions($framework = null): self
    {
        if (\is_array($framework)) {
            $framework = \implode('/', $framework);
        }

        if (\is_string($framework)) {
            $this->frameworkVersion = $framework;
        }

        return $this;
    }

    private function setClientOptions(array $options): self
    {
        $this->clientOptions = $options;

        return $this;
    }

    /**
     * Configuration constructor.
     */
    public function __construct(string $publicKey, SignatureInterface $secureSignature, ClientInterface $client, SerializerInterface $serializer)
    {
        $this->publicKey = $publicKey;
        $this->secureSignature = $secureSignature;
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @return $this
     */
    public function setAuthUrlConfig(AuthUrlConfigInterface $config): ConfigurationInterface
    {
        $this->authUrlConfig = $config;

        return $this;
    }

    public function getHeaders(): array
    {
        $headers = $this->clientOptions['headers'] ?? [];
        $headers['User-Agent'] = $this->getUserAgent();

        return $headers;
    }

    private function getUserAgent(): string
    {
        $info = [
            '{lib-version}' => self::LIBRARY_VERSION,
            '{publicKey}' => $this->publicKey,
            '{lang-version}' => \sprintf('%s.%s.%s', PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION),
        ];
        if ($this->frameworkVersion !== null) {
            $info['{lang-version}'] .= '; ' . $this->frameworkVersion;
        }

        return \strtr(self::USER_AGENT_TEMPLATE, $info);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecureSignature(): SignatureInterface
    {
        return $this->secureSignature;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function getAuthHeaders(string $method, string $uri, string $data, string $contentType = 'application/json', ?\DateTimeInterface $date = null): array
    {
        return [
            'Date' => $this->getSecureSignature()->getDateHeaderString($date),
            'Authorization' => \sprintf('Uploadcare %s:%s', $this->getPublicKey(), $this->getSecureSignature()->getAuthHeaderString($method, $uri, $data, $contentType, $date)),
            'Content-Type' => $contentType,
        ];
    }

    public function getAuthUrlConfig(): ?AuthUrlConfigInterface
    {
        return $this->authUrlConfig;
    }
}
