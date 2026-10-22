<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Exception\SearchException;
use Uploadcare\FileCollection as FileCollectionDecorator;
use Uploadcare\Interfaces\Api\{FileApiInterface, SearchApiInterface};
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Response\SearchResponse;
use Uploadcare\Search\SearchQuery;

/**
 * File search Api.
 *
 * @see https://uploadcare.com/docs/file-search/
 */
final class SearchApi extends AbstractApi implements SearchApiInterface
{
    private FileApiInterface $fileApi;

    public function __construct(ConfigurationInterface $configuration, ?FileApiInterface $fileApi = null)
    {
        parent::__construct($configuration);
        $this->fileApi = $fileApi ?? new FileApi($configuration);
    }

    public function search(SearchQuery $query, int $limit = self::DEFAULT_LIMIT, int $offset = 0, bool $includeAppData = false): SearchResponse
    {
        if ($limit < 1 || $limit > self::MAX_LIMIT) {
            throw new SearchException(\sprintf('Limit must be from 1 to %d, %d given.', self::MAX_LIMIT, $limit));
        }
        if ($offset < 0) {
            throw new SearchException(\sprintf('Offset should not be negative, %d given.', $offset));
        }
        if ($offset + $limit > self::MAX_WINDOW) {
            throw new SearchException(\sprintf('offset + limit must be <= %d, %d given.', self::MAX_WINDOW, $offset + $limit));
        }

        $parameters = ['limit' => $limit, 'offset' => $offset];
        if ($includeAppData) {
            $parameters['include'] = 'appdata';
        }

        $response = $this->request('POST', '/files/search/', [
            'query' => $parameters,
            'body' => $query->toArray(),
        ]);
        $contents = $response->getBody()->getContents();

        $result = $this->configuration->getSerializer()->deserialize($contents, SearchResponse::class);
        if (!$result instanceof SearchResponse) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        $result
            ->setResults(new FileCollectionDecorator($result->getResults(), $this->fileApi))
            ->setHighlights($this->highlights($contents))
            ->setQuery($query);

        return $result;
    }

    public function nextPage(SearchResponse $response): ?SearchResponse
    {
        $parameters = $this->nextParameters($response);
        if ($parameters === null) {
            return null;
        }

        $query = $response->getQuery();
        if ($query === null) {
            throw new SearchException('The response does not contain the search query it was requested with.');
        }

        $limit = isset($parameters['limit']) ? (int) $parameters['limit'] : $response->getPerPage();
        $offset = isset($parameters['offset']) ? (int) $parameters['offset'] : 0;
        if ($offset + $limit > self::MAX_WINDOW) {
            return null;
        }

        return $this->search($query, $limit, $offset, ($parameters['include'] ?? null) === 'appdata');
    }

    /**
     * The serializer drops fields unknown to the file entity, so highlights are read separately.
     *
     * @return array<string, array>
     */
    private function highlights(string $contents): array
    {
        $data = \json_decode($contents, true);
        if (!\is_array($data) || !\is_array($data['results'] ?? null)) {
            return [];
        }

        $highlights = [];
        foreach ($data['results'] as $item) {
            if (\is_array($item) && \is_string($item['uuid'] ?? null) && \is_array($item['highlight'] ?? null)) {
                $highlights[$item['uuid']] = $item['highlight'];
            }
        }

        return $highlights;
    }
}
