<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Exception\HttpException;
use Uploadcare\File\Group;
use Uploadcare\Group as GroupDecorator;
use Uploadcare\GroupCollection;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Response\GroupListResponse;
use Uploadcare\Uploader\Uploader;

/**
 * Group Api.
 */
class GroupApi extends AbstractApi implements GroupApiInterface
{
    public function nextPage(ListResponseInterface $response): ?ListResponseInterface
    {
        $parameters = $this->nextParameters($response);
        if ($parameters === null) {
            return null;
        }

        /** @noinspection VariableFunctionsUsageInspection */
        $result = \call_user_func_array([$this, 'listGroups'], [
            isset($parameters['limit']) ? (int) $parameters['limit'] : 100,
            !isset($parameters['asc']) || (bool) $parameters['asc'],
        ]);

        return $result instanceof ListResponseInterface ? $result : null;
    }

    /**
     * {@inheritDoc}
     */
    public function createGroup(iterable $files): GroupInterface
    {
        $request = [];
        foreach ($files as $file) {
            if ($file instanceof FileInfoInterface) {
                $request[] = $file->getUuid();
            }
            if (\is_string($file) && \uuid_is_valid($file)) {
                $request[] = $file;
            }
        }

        $response = (new Uploader($this->configuration))
            ->groupFiles($request);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), Group::class);

        if (!$result instanceof GroupInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return (new GroupDecorator($result, $this))->setConfiguration($this->configuration);
    }

    /**
     * {@inheritDoc}
     */
    public function listGroups($limit = 100, $asc = true): GroupListResponse
    {
        $parameters = [
            'limit' => (int) $limit,
            'ordering' => $asc ? 'datetime_created' : '-datetime_created',
        ];
        $response = $this->request('GET', '/groups/', ['query' => $parameters]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), GroupListResponse::class);

        if (!$result instanceof GroupListResponse) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }
        $activeCollection = new GroupCollection($result->getResults(), $this);
        $result->setResults($activeCollection);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function groupInfo($id): GroupInterface
    {
        $response = (new Uploader($this->configuration))->groupInfo($id);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), Group::class);

        if (!$result instanceof GroupInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return (new GroupDecorator($result, $this))->setConfiguration($this->configuration);
    }

    /**
     * {@inheritDoc}
     */
    public function storeGroup($id): GroupInterface
    {
        if ($id instanceof GroupInterface) {
            $id = $id->getId();
        }

        $uri = \sprintf('/groups/%s/storage/', $id);
        $response = $this->request('PUT', $uri);
        if ($response->getStatusCode() === 200) {
            return $this->groupInfo($id);
        }

        throw new HttpException('Wrong response from API', $response->getStatusCode());
    }
}
