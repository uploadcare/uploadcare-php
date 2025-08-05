<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsModerationLabelInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AwsModerationLabel implements AwsModerationLabelInterface, SerializableInterface
{
    private ?float $confidence = null;
    private ?string $name = null;
    private ?string $parentName = null;

    public static function rules(): array
    {
        return [
            'confidence' => 'float',
            'name' => 'string',
            'parentName' => 'string',
        ];
    }

    public function getConfidence(): ?float
    {
        return $this->confidence;
    }

    public function setConfidence(?float $confidence): self
    {
        $this->confidence = $confidence;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParentName(): ?string
    {
        return $this->parentName;
    }

    public function setParentName(?string $parentName): self
    {
        $this->parentName = $parentName;

        return $this;
    }
}
