<?php

namespace Uploadcare\MultipartResponse;

use Uploadcare\Interfaces\SerializableInterface;

/**
 * Response of "Start multipart request" representation.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartStartResponse implements SerializableInterface
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
     * @return array|string[]
     */
    public static function rules(): array
    {
        return [
            'parts' => 'array',
            'uuid' => 'string',
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
     * @param array|string[] $parts Url-parts
     *
     * @return $this
     */
    public function setParts(array $parts)
    {
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    /**
     * @param string $part
     *
     * @return MultipartStartResponse
     */
    public function addPart($part)
    {
        if (empty($part)) {
            return $this;
        }

        $partUrl = (new MultipartPreSignedUrl())->setUrl($part);
        if (!\in_array($partUrl, $this->parts, true)) {
            $this->parts[] = $partUrl;
        }

        return $this;
    }
}
