<?php declare(strict_types=1);

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\ContentInfo\ImageInfo;
use Uploadcare\Interfaces\File\ContentInfo\GeoLocationInterface;
use Uploadcare\Interfaces\File\ContentInfo\ImageInfoInterface;

class ImageInfoTest extends TestCase
{
    public function provideMethods(): array
    {
        return [
            ['setColorMode', 'getColorMode', 'RGBA'],
            ['setOrientation', 'getOrientation', \random_int(0, 8)],
            ['setFormat', 'getFormat', 'Format'],
            ['setIsSequence', 'isSequence', true],
            ['setHeight', 'getHeight', 1024],
            ['setWidth', 'getWidth', 768],
            ['setGeoLocation', 'getGeoLocation', $this->createMock(GeoLocationInterface::class)],
            ['setDatetimeOriginal', 'getDatetimeOriginal', \date_create()],
            ['setDpi', 'getDpi', [96, 96]],
        ];
    }

    /**
     * @dataProvider provideMethods
     */
    public function testMethods(string $setter, string $getter, $value): void
    {
        $item = new ImageInfo();
        $this->assertInstanceOf(ImageInfoInterface::class, $item->{$setter}($value));
        $this->assertEquals($item->{$getter}(), $value);
    }
}
