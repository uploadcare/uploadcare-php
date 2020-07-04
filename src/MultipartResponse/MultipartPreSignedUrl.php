<?php

namespace Uploadcare\MultipartResponse;

use Uploadcare\Interfaces\SerializableInterface;

/**
 * Part of service response.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartPreSignedUrl implements SerializableInterface
{
    /**
     * @var string pre-signed and ready to upload file-part url
     */
    private $url;

    public static function rules()
    {
        return [
            'url' => 'string',
        ];
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
