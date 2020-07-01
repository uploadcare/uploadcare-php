<?php

namespace Uploadcare;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\UploaderInterface;

/**
 * Main Uploader.
 */
abstract class AbstractUploader implements UploaderInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Upload file from resource opened by `\fopen()`.
     *
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return string
     */
    abstract public function fromResource($handle, $mimeType = null, $filename = null, $store = 'auto');

    /**
     * Upload file from local path.
     *
     * @param string      $path
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return string
     */
    public function fromPath($path, $mimeType = null, $filename = null, $store = 'auto')
    {
        if (!\file_exists($path) || !\is_readable($path)) {
            throw new InvalidArgumentException(\sprintf('Unable to read \'%s\': file not found or not readable', $path));
        }

        return $this->fromResource(\fopen($path, 'rb'), $mimeType, $filename, $store);
    }

    /**
     * Upload file from remote URL.
     *
     * @param string      $url
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return string
     */
    public function fromUrl($url, $mimeType = null, $filename = null, $store = 'auto')
    {
        $resource = @\fopen($url, 'rb');
        if ($resource === false) {
            throw new InvalidArgumentException(\sprintf('Unable to open \'%s\' url', $url));
        }

        return $this->fromResource($resource, $mimeType, $filename, $store);
    }

    /**
     * Upload file from content string.
     *
     * @param string      $content
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return string
     */
    public function fromContent($content, $mimeType = null, $filename = null, $store = 'auto')
    {
        $res = \fopen('php://temp', 'rb+');
        \fwrite($res, $content);
        \rewind($res);

        return $this->fromResource($res, $mimeType, $filename, $store);
    }

    /**
     * @param mixed|resource $handle
     *
     * @throws \Exception
     */
    protected function checkResource($handle)
    {
        if (!\is_resource($handle)) {
            throw new InvalidArgumentException(\sprintf('Expected resource, %s given', (\is_object($handle) ? \get_class($handle) : \gettype($handle))));
        }

        $this->checkResourceMetadata(\stream_get_meta_data($handle));
    }

    /**
     * @param array $metadata
     *
     * @throws \Exception
     */
    protected function checkResourceMetadata(array $metadata)
    {
        $parameters = [
            'wrapper_type' => ['STDIO', 'http'],
            'stream_type' => ['tcp_socket/ssl', 'plainfile'],
            'mode' => ['rb', 'rb+', 'r+b', 'r', 'r+'],
        ];
        $required = \array_keys($parameters);
        $received = \array_keys($metadata);
        if (!empty($needle = \array_diff($required, $received))) {
            throw new \UnexpectedValueException(\sprintf('Required keys %s not exists in metadata', \implode(', ', $needle)));
        }

        foreach ($parameters as $parameterName => $values) {
            if (!\in_array($metadata[$parameterName], $values, false)) {
                $expectedValues = \implode(', ', $values);
                throw new \UnexpectedValueException(\sprintf('\'%s\' metadata parameter can be %s only, %s given', $parameterName, $expectedValues, $metadata[$parameterName]));
            }
        }
    }

    /**
     * Make multipart-form request parameters.
     * Set nested array as part og parameters (without key) for use it directly.
     * ```
     * makeMultipartParameters([
     *      'foo' => 'bar',
     *      ['contents' => 'hello', 'name' => 'file'],
     *      'baz' => 'foo'
     * ])
     * ```
     * Result
     * ```
     * [
     *      ['name' => 'foo', 'contents' => 'bar'],
     *      ['contents' => 'hello', 'name' => 'file'],
     *      ['name' => 'baz', 'contents' => 'foo'],
     * ].
     *
     * @see http://docs.guzzlephp.org/en/stable/quickstart.html#sending-form-files
     *
     * @param array<array-key, mixed> $parameters
     *
     * @return array[] Multidimensional array for Guzzle multipart/form-data
     */
    protected function makeMultipartParameters(array $parameters)
    {
        $result = [];
        foreach ($parameters as $key => $value) {
            if (\is_int($key)) {
                $result[] = $value;
            } else {
                $result[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        }

        return ['multipart' => $result];
    }

    /**
     * Default parameters for request.
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        return [
            self::UPLOADCARE_STORE_KEY => self::UPLOADCARE_DEFAULT_STORE,
            self::UPLOADCARE_PUB_KEY_KEY => $this->configuration->getPublicKey(),
            self::UPLOADCARE_SIGNATURE_KEY => $this->configuration->getSecureSignature()->getSignature(),
            self::UPLOADCARE_EXPIRE_KEY => $this->configuration->getSecureSignature()->getExpire()->getTimestamp(),
        ];
    }

    /**
     * @param ResponseInterface $response
     * @param string            $arrayKey
     *
     * @return string
     */
    protected function serializeFileResponse(ResponseInterface $response, $arrayKey = 'file')
    {
        $result = $this->configuration->getSerializer()->deserialize($response->getBody()->getContents());
        if (!isset($result[$arrayKey])) {
            throw new \RuntimeException(\sprintf('Unable to get \'%s\' key from response. Call to support', $arrayKey));
        }

        return (string) $result[$arrayKey];
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    protected function sendRequest($method, $uri, array $data)
    {
        if (\strpos($uri, 'https://') !== 0) {
            $uri = \sprintf('https://%s/%s', \rtrim(self::UPLOAD_BASE_URL, '/'), \ltrim($uri, '/'));
        }
        $data['headers'] = $this->configuration->getHeaders();

        return $this->configuration->getClient()
            ->request($method, $uri, $data);
    }

    /**
     * Size of target file in bytes.
     *
     * @see https://www.php.net/manual/en/function.stat.php
     *
     * @param resource $handle
     *
     * @return int
     */
    protected function getSize($handle)
    {
        $stat = \fstat($handle);
        if (\is_array($stat) && \array_key_exists(7, $stat)) {
            return $stat[7];
        }

        return 0;
    }
}
