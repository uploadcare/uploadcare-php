<?php

namespace Tests\File;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Uploadcare\File\GeoLocation;
use Uploadcare\Interfaces\File\GeoLocationInterface;

class GeoLocationTest extends TestCase
{
    public function provideMethods()
    {
        return [
            ['setLatitude', 'getLatitude', Factory::create()->latitude],
            ['setLongitude', 'getLongitude', Factory::create()->longitude],
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
        $item = new GeoLocation();
        $this->assertInstanceOf(GeoLocationInterface::class, $item->{$setter}($value));
        $this->assertEquals($value, $item->{$getter}());
    }
}
