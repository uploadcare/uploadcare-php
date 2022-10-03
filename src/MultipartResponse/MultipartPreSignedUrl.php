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
    private string $url;

    public function __construct(string $url = '')
    {
        $this->url = $url;
    }

    public static function rules(): array
    {
        return [
            'url' => 'string',
        ];
    }

    /**
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
