<?php declare(strict_types=1);

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
    private array $parts = [];

    private ?string $uuid = null;

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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return $this
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return array|MultipartPreSignedUrl[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param array|string[] $parts Url-parts
     *
     * @return $this
     */
    public function setParts(array $parts): self
    {
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    public function addPart(string $part): self
    {
        if (empty($part)) {
            return $this;
        }

        $partUrl = new MultipartPreSignedUrl($part);
        if (!\in_array($partUrl, $this->parts, true)) {
            $this->parts[] = $partUrl;
        }

        return $this;
    }
}
