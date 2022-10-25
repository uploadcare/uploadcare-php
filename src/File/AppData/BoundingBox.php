<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\BoundingBoxInterface;
use Uploadcare\Interfaces\SerializableInterface;

class BoundingBox implements BoundingBoxInterface, SerializableInterface
{
    private ?float $top = null;
    private ?float $left = null;
    private ?float $width = null;
    private ?float $height = null;

    public static function rules(): array
    {
        return [
            'top' => 'float',
            'left' => 'float',
            'width' => 'float',
            'height' => 'float',
        ];
    }

    public function getTop(): ?float
    {
        return $this->top;
    }

    public function setTop(?float $top): self
    {
        $this->top = $top;

        return $this;
    }

    public function getLeft(): ?float
    {
        return $this->left;
    }

    public function setLeft(?float $left): self
    {
        $this->left = $left;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }
}
