<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData\AwsRecognitionData;

interface AwsRecognitionDataInterface
{
    public function getLabelModelVersion(): ?string;

    /**
     * @return iterable<AwsLabelInterface>
     */
    public function getLabels(): iterable;
}
