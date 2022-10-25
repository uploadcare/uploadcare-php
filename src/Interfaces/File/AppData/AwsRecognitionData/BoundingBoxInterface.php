<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData\AwsRecognitionData;

interface BoundingBoxInterface
{
    public function getTop(): ?float;

    public function getLeft(): ?float;

    public function getWidth(): ?float;

    public function getHeight(): ?float;
}
