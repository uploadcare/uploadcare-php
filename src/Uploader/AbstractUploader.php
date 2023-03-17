<?php declare(strict_types=1);

namespace Uploadcare\Uploader;

use GuzzleHttp\Exception\{ClientException, GuzzleException};
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Apis\FileApi;
use Uploadcare\Exception\Upload\{AccountException, FileTooLargeException, RequestParametersException, ThrottledException};
use Uploadcare\Exception\{HttpException, InvalidArgumentException};
use Uploadcare\File\Metadata;
use Uploadcare\Interfaces\{ConfigurationInterface, File\FileInfoInterface, UploaderInterface};

/**
 * Main Uploader.
 */
abstract class AbstractUploader implements UploaderInterface
{
    protected ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param array<array-key, string> $files List of file ID's
     */
    public function groupFiles(array $files): ResponseInterface
    {
        $parameters = [
            'files' => $files,
            'pub_key' => $this->configuration->getPublicKey(),
            self::UPLOADCARE_SIGNATURE_KEY => $this->configuration->getSecureSignature()->getSignature(),
            self::UPLOADCARE_EXPIRE_KEY => $this->configuration->getSecureSignature()->getExpire()->getTimestamp(),
        ];

        try {
            return $this->sendRequest('POST', 'group/', ['form_params' => $parameters]);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, $e instanceof \Exception ? $e : null);
        }
    }

    public function groupInfo(string $id): ResponseInterface
    {
        $parameters = [
            'pub_key' => $this->configuration->getPublicKey(),
            'group_id' => $id,
        ];

        try {
            return $this->sendRequest('GET', 'group/info/', ['query' => $parameters]);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, $e instanceof \Exception ? $e : null);
        }
    }

    /**
     * Upload file from resource opened by `\fopen()`.
     *
     * @param resource $handle
     *
     * @throws InvalidArgumentException
     */
    abstract public function fromResource($handle, string $mimeType = null, string $filename = null, string $store = 'auto', array $metadata = []): FileInfoInterface;

    /**
     * Upload file from local path.
     *
     * @throws InvalidArgumentException
     */
    public function fromPath(string $path, string $mimeType = null, string $filename = null, string $store = 'auto', array $metadata = []): FileInfoInterface
    {
        if (!\file_exists($path) || !\is_readable($path)) {
            throw new InvalidArgumentException(\sprintf('Unable to read \'%s\': file not found or not readable', $path));
        }

        return $this->fromResource(\fopen($path, 'rb'), $mimeType, $filename, $store, $metadata);
    }

    /**
     * Upload file from remote URL.
     *
     * @throws InvalidArgumentException
     */
    public function fromUrl(string $url, string $mimeType = null, string $filename = null, string $store = 'auto', array $metadata = []): string
    {
        $checkDuplicates = false;
        $storeDuplicates = false;
        if (\array_key_exists('checkDuplicates', $metadata)) {
            $checkDuplicates = $metadata['checkDuplicates'];
            unset($metadata['checkDuplicates']);
        }
        if (\array_key_exists('storeDuplicates', $metadata)) {
            $storeDuplicates = $metadata['storeDuplicates'];
            unset($metadata['storeDuplicates']);
        }

        $parameters = $this->makeMultipartParameters(\array_merge($this->getDefaultParameters(), [
            'source_url' => $url,
            'check_URL_duplicates' => $checkDuplicates ? '1' : '0',
            'save_URL_duplicates' => $storeDuplicates ? '1' : '0',
            'pub_key' => $this->configuration->getPublicKey(),
			'store' => $store,
			'filename' => $filename,
        ], $this->makeMetadataParameters($metadata)));

        try {
            $response = $this->sendRequest('POST', 'from_url/', $parameters)->getBody()->getContents();
        } catch (\Throwable $e) {
            throw $this->handleException($e);
        }

        try {
            $responseArray = \json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new HttpException('Wrong response', 0, $e);
        }

        if (!\array_key_exists('token', $responseArray)) {
            throw new HttpException('Unable to get \'token\' key from response');
        }

        return (string) $responseArray['token'];
    }

    public function checkStatus(string $token): string
    {
        try {
            $request = $this->sendRequest('GET', '/from_url/status/', [
                'query' => ['token' => $token],
            ])->getBody()->getContents();
            $response = \json_decode($request, true, 215, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw $this->handleException($e);
        }

        if (!\array_key_exists('status', $response)) {
            throw new HttpException('Unable to get \'status\' key from response');
        }

        return (string) $response['status'];
    }

    public function checkStatusGetFullResponse(string $token)
    {
        try {
            $request = $this->sendRequest('GET', '/from_url/status/', [
                'query' => ['token' => $token],
            ])->getBody()->getContents();
            $response = \json_decode($request, true, 215, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw $this->handleException($e);
        }

        if (!\array_key_exists('status', $response)) {
            throw new HttpException('Unable to get \'status\' key from response');
        }

        return $response;
    }

    /**
     * Upload file from content string.
     *
     * @throws InvalidArgumentException
     */
    public function fromContent(string $content, string $mimeType = null, string $filename = null, string $store = 'auto', array $metadata = []): FileInfoInterface
    {
        $res = \fopen('php://temp', 'rb+');
        \fwrite($res, $content);
        $this->rewind($res);

        return $this->fromResource($res, $mimeType, $filename, $store, $metadata);
    }

    protected function makeMetadataParameters(array $metadata): array
    {
        $result = [];
        foreach ($metadata as $key => $value) {
            if (Metadata::validateKey($key) === false) {
                continue;
            }

            $resultKey = \sprintf('metadata[%s]', $key);
            $result[$resultKey] = $value;
        }

        return $result;
    }

    /**
     * @param mixed|resource $handle
     *
     * @throws \Exception
     */
    protected function checkResource($handle): void
    {
        if (!\is_resource($handle)) {
            throw new InvalidArgumentException(\sprintf('Expected resource, %s given', \is_object($handle) ? \get_class($handle) : \gettype($handle)));
        }

        $this->checkResourceMetadata(\stream_get_meta_data($handle));
    }

    /**
     * @param resource $handle
     */
    protected function getFileName($handle): ?string
    {
        $meta = \stream_get_meta_data($handle);

        if (!isset($meta['uri'])) {
            return null;
        }
        $path = $meta['uri'];

        return \pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @throws \Exception
     */
    protected function checkResourceMetadata(array $metadata): void
    {
        $parameters = [
            'wrapper_type' => ['tcp_socket/ssl', 'plainfile', 'PHP', 'http'],
            'stream_type' => ['STDIO', 'http', 'TEMP', 'tcp_socket/ssl'],
            'mode' => ['rb', 'rb+', 'r+b', 'r', 'r+', 'w+b'],
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
    protected function makeMultipartParameters(array $parameters): array
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
     */
    protected function getDefaultParameters(): array
    {
        return [
            self::UPLOADCARE_STORE_KEY => self::UPLOADCARE_DEFAULT_STORE,
            self::UPLOADCARE_PUB_KEY_KEY => $this->configuration->getPublicKey(),
            self::UPLOADCARE_SIGNATURE_KEY => $this->configuration->getSecureSignature()->getSignature(),
            self::UPLOADCARE_EXPIRE_KEY => $this->configuration->getSecureSignature()->getExpire()->getTimestamp(),
        ];
    }

    protected function serializeFileResponse(ResponseInterface $response, string $arrayKey = 'file'): FileInfoInterface
    {
        $response->getBody()->rewind();
        $result = $this->configuration->getSerializer()->deserialize($response->getBody()->getContents());
        if (!isset($result[$arrayKey])) {
            throw new \RuntimeException(\sprintf('Unable to get \'%s\' key from response. Call to support', $arrayKey));
        }

        return $this->fileInfo((string) $result[$arrayKey]);
    }

    protected function fileInfo(string $id): FileInfoInterface
    {
        return (new FileApi($this->configuration))
            ->fileInfo($id);
    }

    /**
     * @throws GuzzleException
     */
    protected function sendRequest(string $method, string $uri, array $data): ResponseInterface
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
     */
    protected function getSize($handle): int
    {
        $stat = \fstat($handle);
        if (\is_array($stat) && \array_key_exists(7, $stat)) {
            return $stat[7];
        }

        return 0;
    }

    /**
     * @param false|resource $handle
     */
    protected function rewind($handle): void
    {
        if (!\is_resource($handle)) {
            return;
        }

        $meta = \stream_get_meta_data($handle);
        if (isset($meta['seekable']) && $meta['seekable'] === true) {
            \rewind($handle);
        }
    }

    protected function handleException(\Throwable $e): \RuntimeException
    {
        if ($e instanceof ClientException) {
            $response = $e->getResponse();
            switch ($response->getStatusCode()) {
                case 400:
                    $throw = new RequestParametersException('', 0, $e);
                    break;
                case 403:
                    $throw = new AccountException('', 0, $e);
                    break;
                case 413:
                    $throw = new FileTooLargeException('', 0, $e);
                    break;
                case 429:
                    $throw = new ThrottledException('', 0, $e);
                    break;
                default:
                    $throw = new HttpException('', 0, $e);
                    break;
            }

            return $throw;
        }

        return new HttpException('', 0, $e instanceof \Exception ? $e : null);
    }
}
