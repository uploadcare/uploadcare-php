<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\File\AppData\AwsRecognitionLabels;
use Uploadcare\Interfaces\File\AppDataInterface;
use Uploadcare\Interfaces\SerializableInterface;

class AppData implements AppDataInterface, SerializableInterface
{
    private ?AwsRecognitionLabels $awsRecognitionLabels = null;

    public static function rules(): array
    {
        return [
            'awsRecognitionLabels' => AwsRecognitionLabels::class,
        ];
    }

    public function getAwsRekognitionDetectLabels(): ?AwsRecognitionLabels
    {
        return $this->awsRecognitionLabels;
    }

    public function setAwsRecognitionLabels(?AwsRecognitionLabels $awsRecognitionLabels): self
    {
        $this->awsRecognitionLabels = $awsRecognitionLabels;

        return $this;
    }
}
