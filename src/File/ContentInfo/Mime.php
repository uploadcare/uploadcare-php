<?php declare(strict_types=1);

namespace Uploadcare\File\ContentInfo;

use Uploadcare\Interfaces\File\ContentInfo\MimeInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class Mime implements MimeInterface, SerializableInterface
{
    private string $mime = '';
    private string $type = '';
    private string $subType = '';

    public static function rules(): array
    {
        return [
            'mime' => 'string',
            'type' => 'string',
            'subType' => 'string',
        ];
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setSubType(string $subType): self
    {
        $this->subType = $subType;

        return $this;
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }
}
