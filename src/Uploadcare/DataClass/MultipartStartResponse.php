<?php

namespace Uploadcare\DataClass;

/**
 * Response of "Start multipart request" representation.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartStartResponse
{
    /**
     * @param $data
     *
     * @return MultipartStartResponse
     */
    public static function create($data)
    {
        $item = new self();
        if (\property_exists($data, 'uuid')) {
            $item->uuid = $data->uuid;
        }
        if (\property_exists($data, 'parts') && \is_array($data->parts)) {
            foreach ($data->parts as $part) {
                $item->addPart($part);
            }
        }

        return $item;
    }

    private function __construct()
    {
        // Only self::create, no direct creation
    }

    /**
     * @var array|MultipartPreSignedUrl[]
     */
    private $parts = array();

    /**
     * @var string
     */
    private $uuid;

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return array|MultipartPreSignedUrl[]
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param string $part
     *
     * @return MultipartStartResponse
     */
    public function addPart($part)
    {
        $partUrl = new MultipartPreSignedUrl($part);
        if (!\in_array($partUrl, $this->parts, true)) {
            $this->parts[] = $partUrl;
        }

        return $this;
    }
}
