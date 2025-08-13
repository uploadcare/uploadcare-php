<?php declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Uploadcare\Apis\FileApi;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\File\File;
use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\ConfigurationInterface;

class FileApiConvertCollectionTest extends TestCase
{
    protected function getFileApi(): FileApi
    {
        return new FileApi($this->getMockBuilder(ConfigurationInterface::class)->getMock());
    }

    /**
     * @throws \ReflectionException
     */
    protected function getConvertCollection(FileApi $api): \ReflectionMethod
    {
        $convertCollection = (new \ReflectionObject($api))->getMethod('convertCollection');
        $convertCollection->setAccessible(true);

        return $convertCollection;
    }

    public function testWithStringIds(): void
    {
        $ids = [\uuid_create(), \uuid_create(), 'not-a-uuid'];
        $api = $this->getFileApi();

        $convertCollection = $this->getConvertCollection($api);
        $result = $convertCollection->invokeArgs($api, [$ids]);
        self::assertCount(2, $result);
        self::assertNotContains('not-a-uuid', $result);
    }

    public function testWithCollection(): void
    {
        $files = new FileCollection();
        $files->add((new File())->setUuid(\uuid_create()));

        $api = $this->getFileApi();
        $convertCollection = $this->getConvertCollection($api);

        $uuid = $files->first()->getUuid();
        $result = $convertCollection->invokeArgs($api, [$files]);
        self::assertCount(1, $result);
        self::assertContains($uuid, $result);
    }

    public function provideWrongData(): array
    {
        return [
            [false],
            [null],
            [(object) []],
            ['string'],
        ];
    }

    /**
     * @dataProvider provideWrongData
     *
     * @throws \ReflectionException
     */
    public function testExceptionWithWrongObject($item): void
    {
        $this->expectException(InvalidArgumentException::class);
        $api = $this->getFileApi();
        $convertCollection = $this->getConvertCollection($api);

        $convertCollection->invokeArgs($api, [$item]);
        $this->expectExceptionMessageMatches('/must be an instance of/');
    }
}
