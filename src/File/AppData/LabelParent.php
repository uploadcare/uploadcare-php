<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\LabelParentInterface;
use Uploadcare\Interfaces\SerializableInterface;

class LabelParent implements LabelParentInterface, SerializableInterface
{
    private ?string $name = null;

    public static function rules(): array
    {
        return ['name' => 'string'];
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
}
