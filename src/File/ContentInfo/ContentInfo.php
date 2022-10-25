<?php declare(strict_types=1);

namespace Uploadcare\File\ContentInfo;

use Uploadcare\Interfaces\File\ContentInfo\{ImageInfoInterface, MimeInterface, VideoInfoInterface};
use Uploadcare\Interfaces\File\ContentInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class ContentInfo implements ContentInfoInterface, SerializableInterface
{
    private ?MimeInterface $mime = null;
    private ?ImageInfoInterface $image = null;
    private ?VideoInfoInterface $video = null;

    public static function rules(): array
    {
        return [
            'mime' => Mime::class,
            'image' => ImageInfo::class,
            'video' => VideoInfo::class,
        ];
    }

    public function setMime(?MimeInterface $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function setImage(?ImageInfoInterface $imageInfo): self
    {
        $this->image = $imageInfo;

        return $this;
    }

    public function setVideo(?VideoInfoInterface $videoInfo): self
    {
        $this->video = $videoInfo;

        return $this;
    }

    public function getMime(): ?MimeInterface
    {
        return $this->mime;
    }

    public function getImage(): ?ImageInfoInterface
    {
        return $this->image;
    }

    public function getVideo(): ?VideoInfoInterface
    {
        return $this->video;
    }
}
