<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsInstanceInterface;
use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\BoundingBoxInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AwsInstance implements AwsInstanceInterface, SerializableInterface
{
    private ?float $confidence = null;
    private ?BoundingBox $boundingBox = null;

    public static function rules(): array
    {
        return [
            'confidence' => 'float',
            'boundingBox' => BoundingBox::class,
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

    public function getBoundingBox(): ?BoundingBoxInterface
    {
        return $this->boundingBox;
    }

    public function setBoundingBox(?BoundingBox $boundingBox): self
    {
        $this->boundingBox = $boundingBox;

        return $this;
    }
}
