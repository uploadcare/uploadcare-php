<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Response\SearchResponse;
use Uploadcare\Search\SearchQuery;

/**
 * File search.
 *
 * Files are indexed asynchronously: new uploads and changes, including tag updates, may take up
 * to 15 seconds to be reflected in the results.
 *
 * @see https://uploadcare.com/docs/file-search/
 */
interface SearchApiInterface
{
    public const DEFAULT_LIMIT = 20;
    public const MAX_LIMIT = 100;
    public const MAX_WINDOW = 1000;

    /**
     * Search files of the project.
     *
     * @param SearchQuery $query          Search conditions, at least one is required
     * @param int         $limit          Files per page, from 1 to 100
     * @param int         $offset         Number of files to skip. `$offset + $limit` must not exceed 1000, narrow the query instead of deep paging.
     * @param bool        $includeAppData Embed application data (moderation, virus-scan results) into each result
     *
     * @throws \Uploadcare\Exception\SearchException on invalid query or pagination
     */
    public function search(SearchQuery $query, int $limit = self::DEFAULT_LIMIT, int $offset = 0, bool $includeAppData = false): SearchResponse;

    /**
     * Next page of the same search, or `null` when the given page is the last one or the
     * 1000-results window is exhausted.
     */
    public function nextPage(SearchResponse $response): ?SearchResponse;
}
