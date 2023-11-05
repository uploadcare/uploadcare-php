<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

interface AppDataInterface
{
    public function getAwsRekognitionDetectModerationLabels(): ?AppData\AwsRecognitionLabelsInterface;

    public function getAwsRekognitionDetectLabels(): ?AppData\AwsRecognitionLabelsInterface;

    public function getClamAvVirusScan(): ?AppData\ClamAvVirusScanInterface;

    public function getRemoveBg(): ?AppData\RemoveBgInterface;
}
