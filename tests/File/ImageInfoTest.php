<?php

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\ImageInfo;
use Uploadcare\Interfaces\File\GeoLocationInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;

class ImageInfoTest extends TestCase
{
    public function provideMethods()
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
     *
     * @param string $setter
     * @param string $getter
     * @param mixed  $value
     */
    public function testMethods($setter, $getter, $value)
    {
        $item = new ImageInfo();
        $this->assertInstanceOf(ImageInfoInterface::class, $item->{$setter}($value));
        $this->assertEquals($item->{$getter}(), $value);
    }
}
