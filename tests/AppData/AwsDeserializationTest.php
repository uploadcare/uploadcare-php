<?php declare(strict_types=1);

namespace Tests\AppData;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\AppData\AwsInstance;
use Uploadcare\File\AppData\AwsLabel;
use Uploadcare\File\AppData\AwsRecognitionData;
use Uploadcare\File\AppData\AwsRecognitionLabels;
use Uploadcare\File\AppData\BoundingBox;
use Uploadcare\File\AppData\LabelParent;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class AwsDeserializationTest extends TestCase
{
    private string $awsLabels;

    protected function setUp(): void
    {
        parent::setUp();
        $fileInfo = \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json');
        $fileInfoArray = \json_decode($fileInfo, true, 512, JSON_THROW_ON_ERROR);
        $labels = $fileInfoArray['appdata']['aws_rekognition_detect_labels'] ?? null;
        self::assertIsArray($labels);

        $this->awsLabels = \json_encode($labels, JSON_THROW_ON_ERROR);
    }

    protected function getSerializer(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }

    public function testDeserialization(): void
    {
        $result = $this->getSerializer()->deserialize($this->awsLabels, AwsRecognitionLabels::class);
        self::assertInstanceOf(AwsRecognitionLabels::class, $result);
        self::assertInstanceOf(\DateTimeInterface::class, $result->getDatetimeCreated());
        self::assertInstanceOf(\DateTimeInterface::class, $result->getDatetimeUpdated());
        self::assertSame('2022-09-18', $result->getDatetimeCreated()->format('Y-m-d'));
        self::assertSame('2016-06-27', $result->getVersion());

        $data = $result->getData();
        self::assertInstanceOf(AwsRecognitionData::class, $data);
        self::assertSame('2.0', $data->getLabelModelVersion());

        $labels = $data->getLabels();
        self::assertNotEmpty($labels);
        $parents = \current($labels)->getParents();
        self::assertNotEmpty($parents);
        self::assertInstanceOf(LabelParent::class, \current($parents));
        self::assertNotEmpty(\current($parents)->getName());

        $label = null;
        foreach ($labels as $l) {
            if ($l->getName() === 'Person') {
                $label = $l;
            }
        }

        self::assertInstanceOf(AwsLabel::class, $label);
        self::assertIsNumeric($label->getConfidence());
        self::assertNotEmpty($label->getInstances());

        $instance = \current($label->getInstances());
        self::assertInstanceOf(AwsInstance::class, $instance);
        self::assertNotEmpty($instance->getConfidence());
        self::assertInstanceOf(BoundingBox::class, $instance->getBoundingBox());
    }
}
