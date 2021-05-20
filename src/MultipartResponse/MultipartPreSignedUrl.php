<?php declare(strict_types=1);

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

    public static function rules(): array
    {
        return [
            'url' => 'string',
        ];
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
