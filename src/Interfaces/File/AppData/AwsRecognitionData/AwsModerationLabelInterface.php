<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData\AwsRecognitionData;

interface AwsModerationLabelInterface
{
    public function getConfidence(): ?float;

    public function getName(): ?string;

    public function getParentName(): ?string;
}
