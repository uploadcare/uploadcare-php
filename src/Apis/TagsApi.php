<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Psr\Http\Message\ResponseInterface;
use Uploadcare\File\Tags;
use Uploadcare\Interfaces\Api\TagsApiInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Response\TagsResponse;

/**
 * File tags Api.
 *
 * @see https://uploadcare.com/docs/file-tags/
 */
final class TagsApi extends AbstractApi implements TagsApiInterface
{
    public function getTags($id): Tags
    {
        return $this->deserializeTags($this->request('GET', $this->uri($id)))->getTags();
    }

    public function replaceTags($id, array $tags): TagsResponse
    {
        $response = $this->request('PUT', $this->uri($id), [
            'body' => ['tags' => Tags::assertValid($tags)],
        ]);

        return $this->deserializeTags($response);
    }

    public function updateTags($id, array $add = [], array $delete = []): TagsResponse
    {
        $body = [];
        if ($add !== []) {
            $body['add'] = Tags::assertValid($add);
        }
        if ($delete !== []) {
            $body['delete'] = Tags::assertValid($delete);
        }

        $response = $this->request('PATCH', $this->uri($id), [
            // An empty array is encoded as `[]`, while the API expects an object here.
            'body' => $body === [] ? '{}' : $body,
        ]);

        return $this->deserializeTags($response);
    }

    /**
     * @param string|FileInfoInterface $id
     */
    private function uri($id): string
    {
        $uuid = $id instanceof FileInfoInterface ? $id->getUuid() : (string) $id;

        return \sprintf('/files/%s/tags/', $uuid);
    }

    private function deserializeTags(ResponseInterface $response): TagsResponse
    {
        $contents = $response->getBody()->getContents();
        if (empty($contents)) {
            $contents = '{}';
        }

        $result = $this->configuration->getSerializer()->deserialize($contents, TagsResponse::class);
        if (!$result instanceof TagsResponse) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }
}
