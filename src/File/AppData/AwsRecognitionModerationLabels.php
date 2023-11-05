<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsRecognitionDataInterface;
use Uploadcare\Interfaces\File\AppData\AwsRecognitionLabelsInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AwsRecognitionModerationLabels extends AbstractAwsLabels implements AwsRecognitionLabelsInterface, SerializableInterface
{
    private ?AwsRecognitionModerationData $awsRecognitionData = null;

    public static function rules(): array
    {
        return [
            'version' => 'string',
            'datetimeCreated' => \DateTime::class,
            'datetimeUpdated' => \DateTime::class,
            'data' => AwsRecognitionModerationData::class,
        ];
    }

    public function getData(): ?AwsRecognitionDataInterface
    {
        return $this->awsRecognitionData;
    }

    public function setData(?AwsRecognitionModerationData $data): self
    {
        $this->awsRecognitionData = $data;

        return $this;
    }
}
