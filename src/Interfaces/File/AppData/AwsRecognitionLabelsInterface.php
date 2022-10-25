<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData;

use Uploadcare\Interfaces\File\AppData\AwsRecognitionData\AwsRecognitionDataInterface;

interface AwsRecognitionLabelsInterface
{
    public function getVersion(): ?string;

    public function getDatetimeCreated(): ?\DateTimeInterface;

    public function getDatetimeUpdated(): ?\DateTimeInterface;

    public function getData(): ?AwsRecognitionDataInterface;
}
