<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\RemoveBgDataInterface;
use Uploadcare\Interfaces\SerializableInterface;

class RemoveBgData implements RemoveBgDataInterface, SerializableInterface
{
    private ?string $foregroundType = null;

    public static function rules(): array
    {
        return [
            'foregroundType' => 'string',
        ];
    }

    public function getForegroundType(): ?string
    {
        return $this->foregroundType;
    }

    public function setForegroundType(?string $foregroundType): self
    {
        $this->foregroundType = $foregroundType;

        return $this;
    }
}
