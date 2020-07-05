<?php

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\File;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;

class FileTest extends TestCase
{
    public function onlyCreateMethodsProvider()
    {
        return [
            ['isImage', 'boolean'],
            ['isReady', 'boolean'],
            ['getMimeType', 'string'],
            ['getOriginalFileName', 'string'],
            ['getSize', 'integer'],
            ['getUrl', 'string'],
            ['getUuid', 'string'],
        ];
    }

    /**
     * @dataProvider onlyCreateMethodsProvider
     *
     * @param string $method
     * @param string $type
     */
    public function testFileClassCreation($method, $type)
    {
        $item = new File();
        switch ($type) {
            case 'boolean':
                $this->assertTrue(\is_bool($item->{$method}()));
                break;
            case 'string':
                $this->assertTrue(\is_string($item->{$method}()));
                break;
            case 'integer':
                $this->assertTrue(\is_int($item->{$method}()));
                break;
            default:
                $this->fail('Unknown type received');
        }
    }

    /**
     * @return array
     */
    public function methodsProvider()
    {
        return [
            ['setDateTimeRemoved', 'getDateTimeRemoved', \date_create()],
            ['setDateTimeStored', 'getDateTimeStored', \date_create()],
            ['setDateTimeUploaded', 'getDateTimeUploaded', \date_create()],
            ['setImageInfo', 'getImageInfo', $this->createMock(ImageInfoInterface::class)],
            ['setIsImage', 'isImage', true],
            ['setIsReady', 'isReady', true],
            ['setMimeType', 'getMimeType', 'image/heic-sequence'],
            ['setOriginalFileUrl', 'getOriginalFileUrl', 'https://example.com/original'],
            ['setOriginalFileName', 'getOriginalFileName', 'original-name.heic'],
            ['setSize', 'getSize', \random_int(1000, 1500)],
            ['setUrl', 'getUrl', 'https://example.com/'],
            ['setUuid', 'getUuid', \uuid_create()],
            ['setVariations', 'getVariations', null],
            ['setVideoInfo', 'getVideoInfo', $this->createMock(VideoInfoInterface::class)],
            ['setSource', 'getSource', 'some-source'],
            ['setRekognitionInfo', 'getRekognitionInfo', ['foo' => 'bar']],
        ];
    }

    /**
     * @dataProvider methodsProvider
     *
     * @param string $setter
     * @param string $getter
     * @param mixed  $value
     */
    public function testAllMethods($setter, $getter, $value)
    {
        $file = new File();
        $setResult = $file->{$setter}($value);
        $this->assertInstanceOf(FileInfoInterface::class, $setResult);
        $this->assertEquals($file->{$getter}(), $value);
    }
}
