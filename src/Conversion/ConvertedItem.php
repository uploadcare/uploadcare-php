<?php

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
    public function getOriginalSource()
    {
        return $this->originalSource;
    }

    /**
     * @param string $originalSource
     *
     * @return ConvertedItem
     */
    public function setOriginalSource($originalSource)
    {
        $this->originalSource = $originalSource;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return ConvertedItem
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return int
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param int $token
     *
     * @return ConvertedItem
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnailsGroupUuid()
    {
        return $this->thumbnailsGroupUuid;
    }

    /**
     * @param string|null $thumbnailsGroupUuid
     *
     * @return ConvertedItem
     */
    public function setThumbnailsGroupUuid($thumbnailsGroupUuid)
    {
        $this->thumbnailsGroupUuid = $thumbnailsGroupUuid;

        return $this;
    }
}
