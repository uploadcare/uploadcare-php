<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

interface ConvertedItemInterface
{
    /**
     * @return string input file identifier including operations, if present
     */
    public function getOriginalSource(): string;

    /**
     * @return string a UUID of your converted document
     */
    public function getUuid(): string;

    /**
     * @return int a conversion job token that can be used to get a job status
     */
    public function getToken(): int;

    /**
     * @return string|null UUID of a file group with thumbnails for an output video, based on the thumbs operation parameters. Only for video conversions!
     */
    public function getThumbnailsGroupUuid(): ?string;
}
