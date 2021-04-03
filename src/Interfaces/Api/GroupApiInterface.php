<?php

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Exception\HttpException;
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;

/**
 * Uploadcare Groups.
 */
interface GroupApiInterface
{
    /**
     * @param ListResponseInterface $response
     *
     * @return ListResponseInterface|null
     */
    public function nextPage(ListResponseInterface $response);

    /**
     * Create file group.
     *
     * @param iterable $files
     *
     * @return GroupInterface
     *
     * @throws HttpException
     */
    public function createGroup(iterable $files): GroupInterface;

    /**
     * Get a paginated list of groups.
     *
     * @param int  $limit
     * @param bool $asc
     *
     * @return ListResponseInterface
     */
    public function listGroups($limit, $asc = true);

    /**
     * Get a file group by UUID.
     *
     * @param string $id Group UUID
     *
     * @return GroupInterface
     *
     * @throws HttpException
     */
    public function groupInfo($id);

    /**
     * Mark all files in a group as stored.
     *
     * @param string|GroupInterface $id Group UUID
     *
     * @return GroupInterface
     *
     * @throws HttpException
     */
    public function storeGroup($id);
}
