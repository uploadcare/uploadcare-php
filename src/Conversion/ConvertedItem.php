<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Conversion request result.
 */
class ConvertedItem implements ConvertedItemInterface, SerializableInterface
{
    private ?string $originalSource = null;
    private ?string $uuid = null;
    private int $token = 0;
    private ?string $thumbnailsGroupUuid = null;

    public static function rules(): array
    {
        return [
            'originalSource' => 'string',
            'uuid' => 'string',
            'token' => 'int',
            'thumbnailsGroupUuid' => 'string',
        ];
    }

    public function getOriginalSource(): string
    {
        return $this->originalSource ?? '';
    }

    public function setOriginalSource(string $originalSource): self
    {
        $this->originalSource = $originalSource;

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid ?? '';
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getToken(): int
    {
        return $this->token;
    }

    public function setToken(int $token): self
    {
        $this->token = $token;

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
