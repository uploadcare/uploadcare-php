<?php declare(strict_types=1);

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\Audio;
use Uploadcare\Interfaces\File\AudioInterface;

class AudioTest extends TestCase
{
    public function provideMethods(): array
    {
        return [
            ['setBitrate', 'getBitrate', \random_int(14500, 578888)],
            ['setCodec', 'getCodec', 'some codec'],
            ['setSampleRate', 'getSampleRate', \random_int(500, 1000)],
            ['setChannels', 'getChannels', '5.1'],
        ];
    }

    /**
     * @dataProvider provideMethods
     *
     * @param string $setter
     * @param string $getter
     */
    public function testMethods($setter, $getter, $value): void
    {
        $item = new Audio();
        $this->assertInstanceOf(AudioInterface::class, $item->{$setter}($value));
        $this->assertEquals($item->{$getter}(), $value);
    }
}
