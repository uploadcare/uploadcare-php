<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsLabelInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AwsLabel implements AwsLabelInterface, SerializableInterface
{
    private ?float $confidence = null;
    private ?string $name = null;
    private array $parents = [];
    private array $instances = [];

    public static function rules(): array
    {
        return [
            'confidence' => 'float',
            'name' => 'string',
            'parents' => [LabelParent::class],
            'instances' => [AwsInstance::class],
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

    /**
     * @return LabelParent[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    public function addParent(LabelParent $parent): self
    {
        if (!\in_array($parent, $this->parents, true)) {
            $this->parents[] = $parent;
        }

        return $this;
    }

    /**
     * @return AwsInstance[]
     */
    public function getInstances(): array
    {
        return $this->instances;
    }

    public function addInstance(AwsInstance $instance): self
    {
        if (!\in_array($instance, $this->instances, true)) {
            $this->instances[] = $instance;
        }

        return $this;
    }
}
