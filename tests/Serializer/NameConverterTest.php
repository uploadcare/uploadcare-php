<?php

namespace Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Uploadcare\Serializer\SnackCaseConverter;

class NameConverterTest extends TestCase
{
    public function propertyNamesGenerator()
    {
        return [
            ['camelCased', 'camel_cased'],
            ['threePartsName', 'three_parts_name'],
            ['simple', 'simple'],
        ];
    }

    /**
     * @dataProvider propertyNamesGenerator
     *
     * @param string $cc
     * @param string $normal
     */
    public function testNormalizeNames($cc, $normal)
    {
        $result = (new SnackCaseConverter())->normalize($cc);
        $this->assertSame($normal, $result);
    }

    /**
     * @dataProvider propertyNamesGenerator
     *
     * @param string $cc
     * @param string $normal
     */
    public function testDenormalizeNames($cc, $normal)
    {
        $result = (new SnackCaseConverter())->denormalize($normal);
        $this->assertSame($cc, $result);
    }

    public function testActionIfNotSet()
    {
        $attributes = [
            'actAttribute',
        ];

        $converter = new SnackCaseConverter($attributes);
        $this->assertSame('notActAttribute', $converter->normalize('notActAttribute'));
        $this->assertSame('act_attribute', $converter->normalize($attributes[0]));

        $this->assertSame('notActAttribute', $converter->denormalize('notActAttribute'));
        $this->assertSame($attributes[0], $converter->denormalize('act_attribute'));
    }
}
