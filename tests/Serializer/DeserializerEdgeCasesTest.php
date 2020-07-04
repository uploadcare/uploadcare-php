<?php

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
    /**
     * @var string
     */
    private $startResponse;

    /**
     * @var string
     */
    private $example;
    /**
     * @var string
     */
    private $tz = 'UTC';

    protected function setUp()
    {
        $this->tz = \ini_get('date.timezone');
        $this->startResponse = \dirname(__DIR__) . '/_data/startResponse.json';
        $this->example = \dirname(__DIR__) . '/_data/file-example.json';
    }

    public function tearDown()
    {
        \ini_set('date.timezone', $this->tz);
    }

    /**
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return new Serializer(new SnackCaseConverter());
    }

    protected function getImageInfoString()
    {
        $data = \file_get_contents($this->example);
        $imageInfo = \json_decode($data, true)['image_info'];

        return \json_encode($imageInfo);
    }

    public function testNoClassGiven()
    {
        $data = \file_get_contents($this->startResponse);
        $result = $this->getSerializer()->deserialize($data);
        $this->assertArrayHasKey('uuid', $result);
        $this->assertArrayHasKey('parts', $result);
    }

    public function testInvalidClassGiven()
    {
        $this->expectException(ClassNotFoundException::class);
        $data = \file_get_contents($this->example);
        $this->getSerializer()->deserialize($data, 'Class\\Does\\Not\\Exists');
        $this->expectExceptionMessageRegExp('not found');
    }

    public function testInvalidDataGiven()
    {
        $this->expectException(ConversionException::class);
        $data = \substr(\file_get_contents($this->example), 2, 155);
        $this->getSerializer()->deserialize($data, File::class);
        $this->expectExceptionMessageRegExp('Unable to decode given value');
    }

    public function testExcludeProperty()
    {
        /** @var ImageInfo $result */
        $result = $this->getSerializer()->deserialize($this->getImageInfoString(), ImageInfo::class, [
            Serializer::EXCLUDE_PROPERTY_KEY => ['colorMode'],
        ]);
        $this->assertInstanceOf(ImageInfo::class, $result);
        $this->assertEmpty($result->getColorMode());
    }

    public function testDenormalizeDateWithoutTimezone()
    {
        $this->expectException(\PHPUnit_Framework_Error_Warning::class);

        $serializer = $this->getSerializer();
        $denormalizeDate = (new \ReflectionObject($serializer))->getMethod('denormalizeDate');
        $denormalizeDate->setAccessible(true);
        \ini_set('date.timezone', null);
        $denormalizeDate->invokeArgs($serializer, [\date_create()->format('Y-m-d\TH:i:s.u\Z')]);
        $this->expectOutputString('You should set your date.timezone in php.ini');
    }

    public function testDenormalizeWrongDate()
    {
        $this->expectException(ConversionException::class);

        $serializer = $this->getSerializer();
        $denormalizeDate = (new \ReflectionObject($serializer))->getMethod('denormalizeDate');
        $denormalizeDate->setAccessible(true);

        $denormalizeDate->invokeArgs($serializer, ['not-a-valid-date']);
    }

    public function testValidateNotValidClass()
    {
        $this->expectException(SerializerException::class);
        $serializer = $this->getSerializer();
        $validateClass = (new \ReflectionObject($serializer))->getMethod('validateClass');
        $validateClass->setAccessible(true);

        $validateClass->invokeArgs($serializer, [\get_class($this)]);
    }
}
