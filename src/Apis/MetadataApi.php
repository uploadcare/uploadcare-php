<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Exception\HttpException;
use Uploadcare\Exception\MetadataException;
use Uploadcare\File\Metadata;
use Uploadcare\Interfaces\Api\MetadataApiInterface;

class MetadataApi extends AbstractApi implements MetadataApiInterface
{
    public function getMetadata($id): Metadata
    {
        $uri = \sprintf('/files/%s/metadata/', (string) $id);
        $responseData = $this->request('GET', $uri)->getBody()->getContents();
        if (empty($responseData)) {
            $responseData = '{}';
        }

        try {
            $elements = \json_decode($responseData, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new MetadataException($e->getMessage());
        }

        return new Metadata($elements);
    }

    public function setKey($id, string $key, string $value): Metadata
    {
        if (Metadata::validateKey($key) === false) {
            throw new MetadataException('Key should be string up to 64 characters length. Allowed symbols are A-z, 0-9, underscore, hyphen, dot and colon.');
        }
        if (\strlen($value) > 512) {
            throw new MetadataException('Up to 512 characters value allowed.');
        }

        $uri = \sprintf('/files/%s/metadata/%s/', (string) $id, $key);
        $response = $this->request('PUT', $uri, [
            'body' => \sprintf('"%s"', $value),
        ]);

        if ($response->getStatusCode() > 201) {
            throw new HttpException('Wrong response. Call to support');
        }

        return $this->getMetadata($id);
    }
}
