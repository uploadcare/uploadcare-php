<?php declare(strict_types=1);

namespace Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\File;
use Uploadcare\File\ImageInfo;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Exceptions\ClassNotFoundException;
use Uploadcare\Serializer\Exceptions\ConversionException;
use Uploadcare\Serializer\Exceptions\SerializerException;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class DeserializerEdgeCasesTest extends TestCase
{
    private string $startResponse;

    private string $example;

    private string $tz = 'UTC';

    protected function setUp(): void
    {
        $this->tz = \ini_get('date.timezone');
        $this->startResponse = \dirname(__DIR__) . '/_data/startResponse.json';
        $this->example = \dirname(__DIR__) . '/_data/file-info.json';
    }

    public function tearDown(): void
    {
        \ini_set('date.timezone', $this->tz);
    }

    protected function getSerializer(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }

    protected function getImageInfoString(): string
    {
        $data = \file_get_contents($this->example);
        $imageInfo = \json_decode($data, true)['image_info'];

        return \json_encode($imageInfo);
    }

    public function testNoClassGiven(): void
    {
        $data = \file_get_contents($this->startResponse);
        $result = $this->getSerializer()->deserialize($data);
        self::assertArrayHasKey('uuid', $result);
        self::assertArrayHasKey('parts', $result);
    }

    public function testInvalidClassGiven(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $data = \file_get_contents($this->example);
        $this->getSerializer()->deserialize($data, 'Class\\Does\\Not\\Exists');
        $this->expectExceptionMessageMatches('/not found/');
    }

    public function testInvalidDataGiven(): void
    {
        $this->expectException(ConversionException::class);
        $data = \substr(\file_get_contents($this->example), 2, 155);
        $this->getSerializer()->deserialize($data, File::class);
        $this->expectExceptionMessageMatches('/Unable to decode given value/');
    }

    public function testExcludeProperty(): void
    {
        /** @var ImageInfo $result */
        $result = $this->getSerializer()->deserialize($this->getImageInfoString(), ImageInfo::class, [
            Serializer::EXCLUDE_PROPERTY_KEY => ['colorMode'],
        ]);
        self::assertInstanceOf(ImageInfo::class, $result);
        self::assertEmpty($result->getColorMode());
    }

    public function testDenormalizeWrongDate(): void
    {
        $this->expectException(ConversionException::class);

        $serializer = $this->getSerializer();
        $denormalizeDate = (new \ReflectionObject($serializer))->getMethod('denormalizeDate');
        $denormalizeDate->setAccessible(true);

        $denormalizeDate->invokeArgs($serializer, ['not-a-valid-date']);
    }

    public function testValidateNotValidClass(): void
    {
        $this->expectException(SerializerException::class);
        $serializer = $this->getSerializer();
        $validateClass = (new \ReflectionObject($serializer))->getMethod('validateClass');
        $validateClass->setAccessible(true);

        $validateClass->invokeArgs($serializer, [\get_class($this)]);
    }
}
