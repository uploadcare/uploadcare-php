<?php declare(strict_types=1);

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\VideoInfo;
use Uploadcare\Interfaces\File\AudioInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;
use Uploadcare\Interfaces\File\VideoInterface;

class VideoInfoTest extends TestCase
{
    public function testOnlyCreatedClass(): void
    {
        $item = new VideoInfo();
        $this->assertInstanceOf(VideoInterface::class, $item->getVideo());
        $this->assertInstanceOf(AudioInterface::class, $item->getAudio());
    }

    public function provideMethods(): array
    {
        return [
            ['setDuration', 'getDuration', 1024],
            ['setFormat', 'getFormat', 'Format'],
            ['setBitrate', 'getBitrate', 24],
            ['setVideo', 'getVideo', $this->createMock(VideoInterface::class)],
            ['setAudio', 'getAudio', $this->createMock(AudioInterface::class)],
        ];
    }

    /**
     * @dataProvider provideMethods
     */
    public function testMethods(string $setter, string $getter, $value): void
    {
        $item = new VideoInfo();
        $this->assertInstanceOf(VideoInfoInterface::class, $item->{$setter}($value));
        $this->assertEquals($value, $item->{$getter}());
    }
}
