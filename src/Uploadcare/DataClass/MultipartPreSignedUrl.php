<?php

namespace Uploadcare\DataClass;

/**
 * Part of service response.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartPreSignedUrl
{
    /**
     * @var string pre-signed and ready to upload file-part url
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
