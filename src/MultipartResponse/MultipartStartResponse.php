<?php

namespace Uploadcare\MultipartResponse;

/**
 * Response of "Start multipart request" representation.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartStartResponse
{
    /**
     * @var array|MultipartPreSignedUrl[]
     */
    private $parts = [];

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
     * @param string $uuid
     *
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
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
        $partUrl = (new MultipartPreSignedUrl())->setUrl($part);
        if (!\in_array($partUrl, $this->parts, true)) {
            $this->parts[] = $partUrl;
        }

        return $this;
    }
}
