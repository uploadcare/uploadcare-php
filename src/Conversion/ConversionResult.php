<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\StatusResultInterface;
use Uploadcare\Interfaces\SerializableInterface;

class ConversionResult implements StatusResultInterface, SerializableInterface
{
    private ?string $uuid = null;
    private ?string $thumbnailsGroupUuid = null;

    public static function rules(): array
    {
        return [
            'uuid' => 'string',
            'thumbnailsGroupUuid' => 'string',
        ];
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getThumbnailsGroupUuid(): ?string
    {
        return $this->thumbnailsGroupUuid;
    }

    public function setThumbnailsGroupUuid(?string $thumbnailsGroupUuid): self
    {
        $this->thumbnailsGroupUuid = $thumbnailsGroupUuid;

        return $this;
    }
}
