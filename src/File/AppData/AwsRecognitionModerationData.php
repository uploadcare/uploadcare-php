<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsRecognitionDataInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AwsRecognitionModerationData implements AwsRecognitionDataInterface, SerializableInterface
{
    private ?string $labelModelVersion = null;

    /**
     * @var AwsModerationLabel[]
     */
    private array $moderationLabels = [];

    public static function rules(): array
    {
        return [
            'moderationModelVersion' => 'string',
            'moderationLabels' => [AwsModerationLabel::class],
        ];
    }

    public function getLabelModelVersion(): ?string
    {
        return $this->labelModelVersion;
    }

    public function setModerationModelVersion(?string $labelModelVersion): self
    {
        $this->labelModelVersion = $labelModelVersion;

        return $this;
    }

    /**
     * @return AwsModerationLabel[]
     */
    public function getLabels(): iterable
    {
        return $this->moderationLabels;
    }

    public function addModerationLabel(AwsModerationLabel $label): self
    {
        if (!\in_array($label, $this->moderationLabels, true)) {
            $this->moderationLabels[] = $label;
        }

        return $this;
    }
}
