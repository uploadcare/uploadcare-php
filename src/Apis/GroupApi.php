<?php

namespace Uploadcare\Apis;

use Uploadcare\Exception\HttpException;
use Uploadcare\File\Group;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Response\GroupListResponse;
use Uploadcare\Uploader;

/**
 * Group Api.
 */
class GroupApi extends AbstractApi implements GroupApiInterface
{
    public function nextPage(ListResponseInterface $response)
    {
        $parameters = $this->nextParameters($response);
        if ($parameters === null) {
            return null;
        }

        /** @noinspection VariableFunctionsUsageInspection */
        $result = \call_user_func_array([$this, 'listGroups'], [
            isset($parameters['limit']) ? (int) $parameters['limit'] : 100,
            isset($parameters['asc']) ? (bool) $parameters['asc'] : true,
        ]);

        return $result instanceof ListResponseInterface ? $result : null;
    }

    /**
     * @inheritDoc
     */
    public function createGroup($files)
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

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function listGroups($limit = 100, $asc = true)
    {
        $parameters = [
            'limit' => (int) $limit,
            'ordering' => $asc ? 'datetime_created' : '-datetime_created',
        ];
        $response = $this->request('GET', '/groups/', ['query' => $parameters]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), GroupListResponse::class);

        if (!$result instanceof ListResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function groupInfo($id)
    {
        $uri = \sprintf('/groups/%s/', $id);
        $response = $this->request('GET', $uri);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), Group::class);

        if (!$result instanceof GroupInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function storeGroup($id)
    {
        $uri = \sprintf('/groups/%s/storage/', $id);
        $response = $this->request('PUT', $uri);
        if ($response->getStatusCode() === 200) {
            return $this->groupInfo($id);
        }

        throw new HttpException('Wrong response from API', $response->getStatusCode());
    }
}
