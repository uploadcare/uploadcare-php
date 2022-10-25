<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData\AwsRecognitionData;

interface AwsLabelInterface
{
    public function getConfidence(): ?float;

    public function getName(): ?string;

    /**
     * @return LabelParentInterface[]
     */
    public function getParents(): iterable;

    /**
     * @return iterable<AwsInstanceInterface>
     */
    public function getInstances(): iterable;
}
