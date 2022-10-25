<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

use Uploadcare\Interfaces\File\AppData\ClamAvVirusScanInterface;
use Uploadcare\Interfaces\File\AppData\RemoveBgInterface;

interface AppDataInterface
{
    public function getAwsRekognitionDetectLabels(): ?AppData\AwsRecognitionLabelsInterface;

    public function getClamAvVirusScan(): ?ClamAvVirusScanInterface;

    public function getRemoveBg(): ?RemoveBgInterface;
}
