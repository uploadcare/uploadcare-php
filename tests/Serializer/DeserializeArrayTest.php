<?php

namespace Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\MultipartResponse\MultipartPreSignedUrl;
use Uploadcare\MultipartResponse\MultipartStartResponse;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class DeserializeArrayTest extends TestCase
{
    /**
     * @var string
     */
    protected $examplePath;

    protected function setUp()
    {
        $this->examplePath = \dirname(__DIR__) . '/_data/startResponse.json';
    }

    /**
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return new Serializer(new SnackCaseConverter());
    }

    public function testSerializeArrayInClass()
    {
        $content = \file_get_contents($this->examplePath);
        $result = $this->getSerializer()
            ->deserialize($content, MultipartStartResponse::class);

        $this->assertInstanceOf(MultipartStartResponse::class, $result);
        $this->assertArrayHasKey(0, $result->getParts());
        $this->assertInstanceOf(MultipartPreSignedUrl::class, $result->getParts()[0]);
    }
}
