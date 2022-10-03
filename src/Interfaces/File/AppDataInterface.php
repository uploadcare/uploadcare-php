<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

interface AppDataInterface
{
    public function getAwsRekognitionDetectLabels(): ?AppData\AwsRecognitionLabelsInterface;
}
