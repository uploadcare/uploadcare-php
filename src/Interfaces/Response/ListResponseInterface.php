<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;

/**
 * File collection representation.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/filesList
 */
interface ListResponseInterface
{
    /**
     * Next page URL.
     *
     * @return string|null
     */
    public function getNext(): ?string;

    /**
     * Previous page URL.
     *
     * @return string|null
     */
    public function getPrevious(): ?string;

    /**
     * A total number of objects of the queried type. For files, the queried type depends on the stored and removed query parameters.
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Number of objects per page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Collection of FileInfoInterface.
     *
     * @see FileInfoInterface
     *
     * @return CollectionInterface|FileCollection|FileInfoInterface[]
     * @psalm-return CollectionInterface
     */
    public function getResults(): CollectionInterface;
}
