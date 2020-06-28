<?php

namespace Uploadcare\MultipartResponse;

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
