<?php

namespace Uploadcare;

use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Exception\HttpException;
use Uploadcare\File\File;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchFileResponseInterface;
use Uploadcare\Interfaces\Response\FileListResponseInterface;
use Uploadcare\Response\BatchFileResponse;
use Uploadcare\Response\FileListResponse;

final class FileApi implements FileApiInterface
{
    const API_VERSION = '0.5';

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     */
    protected function request($method, $uri, array $data = [])
    {
        $date = \date_create();

        $stringData = '';
        if (!empty($data)) {
            $stringData = \json_encode($data);
        }

        $headers = $this->configuration->getAuthHeaders($method, $uri, $stringData, 'application/json', $date);
        $headers['Accept'] = \sprintf('application/vnd.uploadcare-v%s+json', self::API_VERSION);
        $headers = \array_merge($this->configuration->getHeaders(), $headers);
        if (\strpos('http', $uri) !== 0) {
            $uri = \sprintf('https://%s/%s', Configuration::API_BASE_URL, $uri);
        }

        $parameters = [
            'headers' => $headers,
        ];
        if (!empty($data)) {
            $parameters['body'] = stream_for($stringData);
        }

        try {
            return $this->configuration->getClient()->request($method, $uri, $parameters);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, ($e instanceof \Exception) ? $e : null);
        }
    }

    /**
     * Getting a paginated list of files.
     *
     * @return FileListResponseInterface
     */
    public function listFiles()
    {
        $response = $this->request('GET', '/files/');

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), FileListResponse::class);
        if (!$result instanceof FileListResponse) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * Store a single file by UUID.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function storeFile($id)
    {
        $response = $this->request('PUT', \sprintf('/files/%s/storage/', $id));

        return $this->deserializeFileInfo($response);
    }

    /**
     * Remove individual files. Returns file info.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function deleteFile($id)
    {
        $response = $this->request('DELETE', \sprintf('/files/%s/', $id));

        return $this->deserializeFileInfo($response);
    }

    /**
     * Specific file info.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function fileInfo($id)
    {
        $response = $this->request('GET', \sprintf('/files/%s/', $id));

        return $this->deserializeFileInfo($response);
    }

    /**
     * Store multiple files in one step.
     * Up to 100 files are supported per request.
     *
     * @param array $ids array of files UUIDs to store
     *
     * @return BatchFileResponseInterface
     */
    public function batchStoreFile(array $ids)
    {
        $response = $this->request('PUT', '/files/storage/', $ids);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchFileResponse::class);

        if (!$result instanceof BatchFileResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @param array $ids array of files UUIDs to store
     *
     * @return BatchFileResponseInterface
     */
    public function batchDeleteFile(array $ids)
    {
        $response = $this->request('DELETE', '/files/storage/', $ids);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchFileResponse::class);

        if (!$result instanceof BatchFileResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * Copy original files or their modified versions to default storage. Source files MAY either be stored or just uploaded and MUST NOT be deleted.
     *
     * @param string $source a CDN URL or just UUID of a file subjected to copy
     * @param bool   $store  the parameter only applies to the Uploadcare storage and MUST be boolean
     *
     * @return FileInfoInterface|object
     */
    public function copyToLocalStorage($source, $store)
    {
        $response = $this->request('POST', '/files/local_copy/', [
            'source' => $source,
            'store' => $store,
        ]);

        // Hack
        $data = \json_decode($response->getBody()->getContents(), true);
        if (!isset($data['result']) || !\is_array($data['result'])) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        $result = $this->configuration->getSerializer()
            ->deserialize(\json_encode($data['result']), File::class);

        if (!$result instanceof File) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @param string $source     a CDN URL or just UUID of a file subjected to copy
     * @param string $target     Identifies a custom storage name related to your project. Implies you are copying a file to a specified custom storage. Keep in mind you can have multiple storage's associated with a single S3 bucket.
     * @param bool   $makePublic true to make copied files available via public links, false to reverse the behavior
     * @param string $pattern    Enum: "${default}" "${auto_filename}" "${effects}" "${filename}" "${uuid}" "${ext}" The parameter is used to specify file names Uploadcare passes to a custom storage. In case the parameter is omitted, we use pattern of your custom storage. Use any combination of allowed values.
     *
     * @return string
     */
    public function copyToRemoteStorage($source, $target, $makePublic = null, $pattern = null)
    {
        $parameters = [
            'source' => $source,
            'target' => $target,
        ];
        if (\is_bool($makePublic)) {
            $parameters['make_public'] = $makePublic;
        }
        if (\is_string($pattern)) {
            $parameters['pattern'] = $pattern;
        }

        $response = $this->request('POST', '/files/remote_copy/', $parameters);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents());

        if (!isset($result['result'])) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return (string) $result['result'];
    }

    private function deserializeFileInfo(ResponseInterface $response)
    {
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), File::class);
        if (!$result instanceof File) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }
}
