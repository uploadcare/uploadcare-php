<?php declare(strict_types=1);

namespace Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Tests\Serializer\Examples\ExampleIncluded;
use Tests\Serializer\Examples\ExampleParent;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\MultipartResponse\MultipartPreSignedUrl;
use Uploadcare\MultipartResponse\MultipartStartResponse;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class DeserializeArrayTest extends TestCase
{
    protected string $examplePath;

    protected function setUp(): void
    {
        $this->examplePath = \dirname(__DIR__) . '/_data/startResponse.json';
    }

    protected function getSerializer(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }

    public function testSerializeArrayInClass(): void
    {
        $content = \file_get_contents($this->examplePath);
        $result = $this->getSerializer()
            ->deserialize($content, MultipartStartResponse::class);

        self::assertInstanceOf(MultipartStartResponse::class, $result);
        self::assertArrayHasKey(0, $result->getParts());
        self::assertInstanceOf(MultipartPreSignedUrl::class, $result->getParts()[0]);
    }

    public function testDenormalizeClassesArray(): void
    {
        $dates = [
            ['date_time' => \date_create('now')->format(Serializer::DATE_FORMAT)],
            ['date_time' => \date_create('+1 day')->format(Serializer::DATE_FORMAT)],
        ];

        $exampleData = [
            'name' => 'Example with add',
            'dates' => $dates,
        ];
        $exampleJson = \json_encode($exampleData);

        $result = $this->getSerializer()->deserialize($exampleJson, ExampleParent::class);
        self::assertInstanceOf(ExampleParent::class, $result);
        self::assertCount(2, $result->getDates());
        self::assertInstanceOf(ExampleIncluded::class, $result->getDates()[0]);

        $ctrl = \date_create_from_format(Serializer::DATE_FORMAT, $dates[1]['date_time']);
        self::assertEquals($ctrl, $result->getDates()[1]->getDateTime());
    }
}
