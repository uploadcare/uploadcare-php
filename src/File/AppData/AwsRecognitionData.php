<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsRecognitionDataInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AwsRecognitionData implements AwsRecognitionDataInterface, SerializableInterface
{
    private ?string $labelModelVersion = null;

    private array $labels = [];

    public static function rules(): array
    {
        return [
            'labelModelVersion' => 'string',
            'labels' => [AwsLabel::class],
        ];
    }

    public function getLabelModelVersion(): ?string
    {
        return $this->labelModelVersion;
    }

    public function setLabelModelVersion(?string $labelModelVersion): self
    {
        $this->labelModelVersion = $labelModelVersion;

        return $this;
    }

    /**
     * @return AwsLabel[]
     */
    public function getLabels(): iterable
    {
        return $this->labels;
    }

    public function addLabel(AwsLabel $label): self
    {
        if (!\in_array($label, $this->labels, true)) {
            $this->labels[] = $label;
        }

        return $this;
    }
}
