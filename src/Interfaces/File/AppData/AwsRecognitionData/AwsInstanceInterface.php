<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData\AwsRecognitionData;

interface AwsInstanceInterface
{
    public function getConfidence(): ?float;

    public function getBoundingBox(): ?BoundingBoxInterface;
}
