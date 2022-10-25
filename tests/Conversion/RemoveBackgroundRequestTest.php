<?php declare(strict_types=1);

namespace Tests\Conversion;

use PHPUnit\Framework\TestCase;
use Uploadcare\Configuration;
use Uploadcare\Conversion\RemoveBackgroundRequest;

class RemoveBackgroundRequestTest extends TestCase
{
    public function testSerialization(): void
    {
        $object = (new RemoveBackgroundRequest())
            ->setCrop(true)
            ->setCropMargin('10px')
            ->setScale('80%')
            ->setAddShadow(true)
            ->setTypeLevel('2')
            ->setType('person')
            ->setSemitransparency(false)
            ->setChannels('alpha')
            ->setRoi('0% 0% 100% 100%')
            ->setPosition('original')
        ;

        $configuration = Configuration::create('public', 'private');
        $result = $configuration->getSerializer()->serialize($object);

        $resultArray = \json_decode($result, true);
        self::assertTrue($resultArray['crop']);
        self::assertSame($resultArray['crop_margin'], '10px');
        self::assertSame($resultArray['scale'], '80%');
        self::assertTrue($resultArray['add_shadow']);
        self::assertSame($resultArray['type_level'], '2');
        self::assertSame($resultArray['type'], 'person');
        self::assertFalse($resultArray['semitransparency']);
        self::assertSame($resultArray['channels'], 'alpha');
        self::assertSame($resultArray['roi'], '0% 0% 100% 100%');
        self::assertSame($resultArray['position'], 'original');
    }
}
