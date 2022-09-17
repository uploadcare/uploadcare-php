<?php declare(strict_types=1);

namespace Tests\File;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Uploadcare\File\GeoLocation;
use Uploadcare\Interfaces\File\GeoLocationInterface;

class GeoLocationTest extends TestCase
{
    public function provideMethods(): array
    {
        return [
            ['setLatitude', 'getLatitude', Factory::create()->latitude],
            ['setLongitude', 'getLongitude', Factory::create()->longitude],
        ];
    }

    /**
     * @dataProvider provideMethods
     */
    public function testMethods(string $setter, string $getter, $value): void
    {
        $item = new GeoLocation();
        $this->assertInstanceOf(GeoLocationInterface::class, $item->{$setter}($value));
        $this->assertEquals($value, $item->{$getter}());
    }
}
