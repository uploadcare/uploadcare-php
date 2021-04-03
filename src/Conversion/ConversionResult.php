<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\StatusResultInterface;
use Uploadcare\Interfaces\SerializableInterface;

class ConversionResult implements StatusResultInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string|null
     */
    private $thumbnailsGroupUuid;

    public static function rules(): array
    {
        return [
            'uuid' => 'string',
            'thumbnailsGroupUuid' => 'string',
        ];
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
     * @return ConversionResult
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

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
     * @return ConversionResult
     */
    public function setThumbnailsGroupUuid(?string $thumbnailsGroupUuid): self
    {
        $this->thumbnailsGroupUuid = $thumbnailsGroupUuid;

        return $this;
    }
}
