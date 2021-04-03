<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Conversion request result.
 */
class ConvertedItem implements ConvertedItemInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $originalSource;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $token;

    /**
     * @var string|null
     */
    private $thumbnailsGroupUuid;

    public static function rules(): array
    {
        return [
            'originalSource' => 'string',
            'uuid' => 'string',
            'token' => 'int',
            'thumbnailsGroupUuid' => 'string',
        ];
    }

    /**
     * @return string
     */
    public function getOriginalSource(): string
    {
        return $this->originalSource;
    }

    /**
     * @param string $originalSource
     *
     * @return ConvertedItem
     */
    public function setOriginalSource(string $originalSource): self
    {
        $this->originalSource = $originalSource;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return ConvertedItem
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return int
     */
    public function getToken(): int
    {
        return $this->token;
    }

    /**
     * @param int $token
     *
     * @return ConvertedItem
     */
    public function setToken(int $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnailsGroupUuid(): ?string
    {
        return $this->thumbnailsGroupUuid;
    }

    /**
     * @param string|null $thumbnailsGroupUuid
     *
     * @return ConvertedItem
     */
    public function setThumbnailsGroupUuid(?string $thumbnailsGroupUuid): self
    {
        $this->thumbnailsGroupUuid = $thumbnailsGroupUuid;

        return $this;
    }
}
