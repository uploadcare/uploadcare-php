<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Exception\HttpException;
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;

/**
 * Uploadcare Groups.
 */
interface GroupApiInterface
{
    public function nextPage(ListResponseInterface $response): ?ListResponseInterface;

    /**
     * Create file group.
     *
     * @throws HttpException
     */
    public function createGroup(iterable $files): GroupInterface;

    /**
     * Get a paginated list of groups.
     */
    public function listGroups(int $limit, bool $asc = true): ListResponseInterface;

    /**
     * Get a file group by UUID.
     *
     * @param string $id Group UUID
     *
     * @throws HttpException
     */
    public function groupInfo(string $id): GroupInterface;

    /**
     * Mark all files in a group as stored.
     *
     * @param string|GroupInterface $id Group UUID
     *
     * @throws HttpException
     *
     * @deprecated
     */
    public function storeGroup($id): GroupInterface;

    /**
     * Delete a file group.
     * The operation only removes the group object itself. All the files that were part of the group are left as is.
     *
     * @param string|GroupInterface $id
     *
     * @throws HttpException
     */
    public function removeGroup($id): void;
}
