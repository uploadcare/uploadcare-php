<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Psr\Http\Message\ResponseInterface;
use Uploadcare\AuthUrl\Token\AkamaiToken;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\File as FileDecorator;
use Uploadcare\File\File;
use Uploadcare\File\FileCollection;
use Uploadcare\FileCollection as FileCollectionDecorator;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Response\BatchFileResponse;
use Uploadcare\Response\FileListResponse;

/**
 * File Api.
 */
class FileApi extends AbstractApi implements FileApiInterface
{
    /**
     * {@inheritDoc}
     */
    public function nextPage(ListResponseInterface $response): ?ListResponseInterface
    {
        $parameters = $this->nextParameters($response);
        if ($parameters === null) {
            return null;
        }

        /** @noinspection VariableFunctionsUsageInspection */
        $result = \call_user_func_array([$this, 'listFiles'], [
            isset($parameters['limit']) ? (int) $parameters['limit'] : 100,
            $parameters['ordering'] ?? 'datetime_uploaded',
            $parameters['from'] ?? null,
            isset($parameters['add_fields']) ? (array) $parameters['add_fields'] : [],
            isset($parameters['stored']) ? (bool) $parameters['stored'] : null,
            isset($parameters['removed']) ? (bool) $parameters['removed'] : null,
        ]);

        return $result instanceof ListResponseInterface ? $result : null;
    }

    /**
     * Getting a paginated list of files.
     *
     * @param int             $limit     A preferred amount of files in a list for a single response. Defaults to 100, while the maximum is 1000.
     * @param string          $orderBy   specifies the way files are sorted in a returned list
     * @param string|int|null $from      A starting point for filtering files. The value depends on your $orderBy parameter value.
     * @param array           $addFields Add special fields to the file object
     * @param bool|null       $stored    `true` to only include files that were stored, `false` to include temporary ones. The default is unset: both stored and not stored files are returned.
     * @param bool            $removed   `true` to only include removed files in the response, `false` to include existing files. Defaults to false.
     *
     * @return ListResponseInterface
     */
    public function listFiles($limit = 100, $orderBy = 'datetime_uploaded', $from = null, $addFields = [], $stored = null, $removed = false): ListResponseInterface
    {
        $parameters = [
            'limit' => $limit,
            'ordering' => $orderBy,
            'removed' => $removed,
            'add_fields' => $addFields,
        ];
        if (\is_bool($stored)) {
            $parameters['stored'] = $stored;
        }

        $response = $this->request('GET', '/files/', ['query' => $parameters]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), FileListResponse::class);
        if (!$result instanceof FileListResponse) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }
        $activeCollection = new FileCollectionDecorator($result->getResults(), $this);
        $result->setResults($activeCollection);

        return $result;
    }

    /**
     * Store a single file by UUID.
     *
     * @param string|FileInfoInterface $id file UUID
     *
     * @return FileInfoInterface
     */
    public function storeFile($id): FileInfoInterface
    {
        if ($id instanceof FileInfoInterface) {
            $id = $id->getUuid();
        }
        $response = $this->request('PUT', \sprintf('/files/%s/storage/', $id));

        return $this->deserializeFileInfo($response);
    }

    /**
     * Remove individual files. Returns file info.
     *
     * @param string|FileInfoInterface $id file UUID
     *
     * @return FileInfoInterface
     */
    public function deleteFile($id): FileInfoInterface
    {
        if ($id instanceof FileInfoInterface) {
            $id = $id->getUuid();
        }
        $response = $this->request('DELETE', \sprintf('/files/%s/', $id));

        return $this->deserializeFileInfo($response, false);
    }

    /**
     * Specific file info.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function fileInfo($id): FileInfoInterface
    {
        $response = $this->request('GET', \sprintf('/files/%s/', $id));

        return $this->deserializeFileInfo($response);
    }

    /**
     * Store multiple files in one step.
     * Up to 100 files are supported per request.
     *
     * @param array|CollectionInterface $ids array of files UUIDs to store
     *
     * @return BatchResponseInterface
     */
    public function batchStoreFile($ids): BatchResponseInterface
    {
        $response = $this->request('PUT', '/files/storage/', ['body' => \json_encode($this->convertCollection($ids))]);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchFileResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }
        if ($result instanceof BatchFileResponse) {
            $activeCollection = new FileCollectionDecorator($result->getResult(), $this);
            $result->setResult($activeCollection);
        }

        return $result;
    }

    /**
     * @param array|CollectionInterface $ids array of files UUIDs to store
     *
     * @return BatchResponseInterface
     */
    public function batchDeleteFile($ids): BatchResponseInterface
    {
        $response = $this->request('DELETE', '/files/storage/', ['body' => \json_encode($this->convertCollection($ids))]);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchFileResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * Copy original files or their modified versions to default storage. Source files MAY either be stored or just uploaded and MUST NOT be deleted.
     *
     * @param string|FileInfoInterface $source a CDN URL or just UUID of a file subjected to copy
     * @param bool                     $store  the parameter only applies to the Uploadcare storage and MUST be boolean
     *
     * @return FileInfoInterface
     */
    public function copyToLocalStorage($source, bool $store): FileInfoInterface
    {
        if ($source instanceof FileInfoInterface) {
            $source = $source->getUuid();
        }
        if (!\uuid_is_valid($source)) {
            throw new InvalidArgumentException(\sprintf('Uuid \'%s\' for request not valid', $source));
        }

        $parameters = [
            'source' => $source,
            'store' => $store,
        ];
        $response = $this->request('POST', '/files/local_copy/', ['body' => \json_encode($parameters)]);

        $data = \json_decode($response->getBody()->getContents(), true);
        if (!isset($data['result']) || !\is_array($data['result'])) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        $result = $this->configuration->getSerializer()
            ->deserialize(\json_encode($data['result']), File::class);

        if (!$result instanceof File) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return new FileDecorator($result, $this);
    }

    /**
     * @param string|FileInfoInterface $source     a CDN URL or just UUID of a file subjected to copy
     * @param string                   $target     Identifies a custom storage name related to your project. Implies you are copying a file to a specified custom storage. Keep in mind you can have multiple storage's associated with a single S3 bucket.
     * @param bool                     $makePublic true to make copied files available via public links, false to reverse the behavior
     * @param string|null              $pattern    Enum: "${default}" "${auto_filename}" "${effects}" "${filename}" "${uuid}" "${ext}" The parameter is used to specify file names Uploadcare passes to a custom storage. In case the parameter is omitted, we use pattern of your custom storage. Use any combination of allowed values.
     *
     * @return string
     */
    public function copyToRemoteStorage($source, string $target, bool $makePublic = true, string $pattern = null): string
    {
        if ($source instanceof FileInfoInterface) {
            $source = $source->getUuid();
        }
        if (!\uuid_is_valid($source)) {
            throw new InvalidArgumentException(\sprintf('Uuid \'%s\' for request not valid', $source));
        }

        $parameters = [
            'source' => $source,
            'target' => $target,
        ];
        if ($makePublic) {
            $parameters['make_public'] = $makePublic;
        }
        if (\is_string($pattern)) {
            $parameters['pattern'] = $pattern;
        }

        $response = $this->request('POST', '/files/remote_copy/', ['body' => \json_encode($parameters)]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents());

        if (!isset($result['result'])) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return (string) $result['result'];
    }

    /**
     * @param array|CollectionInterface $ids
     *
     * @return array<array-key, string>
     */
    protected function convertCollection($ids): array
    {
        $values = [];
        if (!\is_array($ids) && !$ids instanceof FileCollection) {
            throw new InvalidArgumentException(\vsprintf('First argument for %s must be an instance of %s or array, %s given', [__METHOD__, FileCollection::class, \is_object($ids) ? \get_class($ids) : \gettype($ids)]));
        }
        foreach ($ids as $id) {
            if ($id instanceof FileInfoInterface) {
                $values[] = $id->getUuid();
            } elseif (\uuid_is_valid($id)) {
                $values[] = $id;
            }
        }

        return $values;
    }

    /**
     * @param FileInfoInterface|string $id     FileInfo instance or File UUID
     * @param int                      $window Time window
     *
     * @return string|null
     */
    public function generateSecureUrl($id, $window = 300): ?string
    {
        if (!($authConfig = $this->configuration->getAuthUrlConfig()) instanceof AuthUrlConfigInterface) {
            return null;
        }

        if ($id instanceof FileInfoInterface) {
            $id = $id->getUuid();
        }

        if (!\uuid_is_valid($id)) {
            throw new InvalidArgumentException(\sprintf('UUID %s is not valid', (\is_string($id) ? $id : \gettype($id))));
        }

        $generator = $authConfig->getTokenGenerator();
        if ($generator instanceof AkamaiToken) {
            $generator->setAcl($id);
            $generator->setWindow((int) $window);
        }

        return \strtr($generator->getUrlTemplate(), [
            '{cdn}' => $authConfig->getCdnUrl(),
            '{uuid}' => $id,
            '{timestamp}' => $generator->getExpired(),
            '{token}' => $generator->getToken(),
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @param bool              $activeFile Whether convert to Active File
     *
     * @return FileInfoInterface
     */
    private function deserializeFileInfo(ResponseInterface $response, $activeFile = true): FileInfoInterface
    {
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), File::class);
        if (!$result instanceof File) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $activeFile ? new FileDecorator($result, $this) : $result;
    }
}
