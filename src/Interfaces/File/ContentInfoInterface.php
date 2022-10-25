<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

use Uploadcare\Interfaces\File\ContentInfo\{ImageInfoInterface, MimeInterface, VideoInfoInterface};

interface ContentInfoInterface
{
    /**
     * MIME type.
     */
    public function getMime(): ?MimeInterface;

    /**
     * Image metadata.
     */
    public function getImage(): ?ImageInfoInterface;

    /**
     * Video metadata.
     */
    public function getVideo(): ?VideoInfoInterface;
}
