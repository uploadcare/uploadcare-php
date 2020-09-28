<?php

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\Video;
use Uploadcare\Interfaces\File\VideoInterface;

class VideoTest extends TestCase
{
    public function provideMethods()
    {
        return [
            ['setHeight', 'getHeight', 1024],
            ['setWidth', 'getWidth', 768],
            ['setFrameRate', 'getFrameRate', .55],
            ['setBitrate', 'getBitrate', 24],
            ['setCodec', 'getCodec', 'DivX'],
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
        $item = new Video();
        $this->assertInstanceOf(VideoInterface::class, $item->{$setter}($value));
        $this->assertEquals($value, $item->{$getter}());
    }
}
