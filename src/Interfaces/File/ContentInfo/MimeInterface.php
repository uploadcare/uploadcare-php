<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\ContentInfo;

interface MimeInterface
{
    /**
     * Full MIME type.
     */
    public function getMime(): string;

    /**
     * Type of MIME type.
     */
    public function getType(): string;

    /**
     * Type of MIME type.
     */
    public function getSubType(): string;
}
