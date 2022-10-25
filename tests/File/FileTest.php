<?php declare(strict_types=1);

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\File;
use Uploadcare\Interfaces\File\ContentInfoInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;

class FileTest extends TestCase
{
    public function onlyCreateMethodsProvider(): array
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
     */
    public function testFileClassCreation(string $method, string $type): void
    {
        $item = new File();
        switch ($type) {
            case 'boolean':
                $this->assertIsBool($item->{$method}());
                break;
            case 'string':
                $this->assertIsString($item->{$method}());
                break;
            case 'integer':
                $this->assertIsInt($item->{$method}());
                break;
            default:
                $this->fail('Unknown type received');
        }
    }

    public function methodsProvider(): array
    {
        return [
            ['setDateTimeRemoved', 'getDateTimeRemoved', \date_create()],
            ['setDateTimeStored', 'getDateTimeStored', \date_create()],
            ['setDateTimeUploaded', 'getDateTimeUploaded', \date_create()],
            ['setIsImage', 'isImage', true],
            ['setIsReady', 'isReady', true],
            ['setMimeType', 'getMimeType', 'image/heic-sequence'],
            ['setOriginalFileUrl', 'getOriginalFileUrl', 'https://example.com/original'],
            ['setOriginalFileName', 'getOriginalFileName', 'original-name.heic'],
            ['setSize', 'getSize', \random_int(1000, 1500)],
            ['setUrl', 'getUrl', 'https://example.com/'],
            ['setUuid', 'getUuid', \uuid_create()],
            ['setVariations', 'getVariations', null],
            ['setContentInfo', 'getContentInfo', $this->createMock(ContentInfoInterface::class)],
            ['setSource', 'getSource', 'some-source'],
        ];
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testAllMethods(string $setter, string $getter, $value): void
    {
        $file = new File();
        $setResult = $file->{$setter}($value);
        $this->assertInstanceOf(FileInfoInterface::class, $setResult);
        $this->assertEquals($file->{$getter}(), $value);
    }
}
