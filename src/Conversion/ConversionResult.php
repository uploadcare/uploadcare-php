<?php

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

    public static function rules()
    {
        return [
            'uuid' => 'string',
            'thumbnailsGroupUuid' => 'string',
        ];
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
     * @return ConversionResult
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

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
     * @return ConversionResult
     */
    public function setThumbnailsGroupUuid($thumbnailsGroupUuid)
    {
        $this->thumbnailsGroupUuid = $thumbnailsGroupUuid;

        return $this;
    }
}
