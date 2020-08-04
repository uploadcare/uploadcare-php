<?php

namespace Tests\Conversion;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\ConversionApi;
use Uploadcare\Configuration;
use Uploadcare\Conversion\ConvertedCollection;
use Uploadcare\Conversion\ConvertedItem;
use Uploadcare\Conversion\DocumentConversionRequest;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\Conversion\ConversionRequest;
use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;
use Uploadcare\Interfaces\Conversion\StatusResultInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class ConversionApiMethodsTest extends TestCase
{
    protected function fakeApi($responses = [])
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, SerializerFactory::create());

        return new ConversionApi($config);
    }

    public function testFileConversion()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('conversion/one-document-conversion-no-problem-response.json')),
        ]);

        $file = $this->createMock(FileInfoInterface::class);
        $file->method('getUuid')->willReturn(\uuid_create());

        $request = new DocumentConversionRequest();
        $request->setPageNumber(1);
        $request->setTargetFormat('jpg');
        $request->setStore(true);
        $request->setThrowError(false);

        $result = $api->convertDocument($file, $request);
        self::assertInstanceOf(ConvertedItem::class, $result);

        self::assertNotEmpty($result->getUuid());
        self::assertNotEmpty($result->getToken());
        self::assertNotEmpty($result->getOriginalSource());
        self::assertNull($result->getThumbnailsGroupUuid());
    }

    public function testConvertNotValidUuid()
    {
        $this->expectException(InvalidArgumentException::class);
        $api = $this->fakeApi();
        $request = $this->createMock(DocumentConversionRequestInterface::class);
        $api->convertDocument('not-valid-uuid', $request);
    }

    public function testConvertWithNotValidInterface()
    {
        $this->expectException(\RuntimeException::class);
        $api = $this->fakeApi();
        $request = $this->createMock(ConversionRequest::class);
        $api->convertDocument(\uuid_create(), $request);
    }

    public function testWrongFormatInConversionRequestConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new DocumentConversionRequest('jpeg');
    }

    public function testWrongFormatInConversionRequestSetter()
    {
        $this->expectException(InvalidArgumentException::class);
        (new DocumentConversionRequest())->setTargetFormat('some crap');
    }

    public function testConvertDocumentsCollection()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('conversion/document-conversion-response.json')),
        ]);
        $fileOne = $this->createMock(FileInfoInterface::class);
        $fileTwo = $this->createMock(FileInfoInterface::class);
        $fileOne->method('getUuid')->willReturn(\uuid_create());
        $fileTwo->method('getUuid')->willReturn(\uuid_create());

        $files = new FileCollection([$fileOne, $fileTwo]);
        $request = new DocumentConversionRequest();

        $result = $api->batchConvertDocuments($files, $request);
        self::assertInstanceOf(BatchResponseInterface::class, $result);
        self::assertInstanceOf(ConvertedCollection::class, $result->getResult());
        self::assertNotEmpty($result->getProblems());
        self::assertInstanceOf(ResponseProblemInterface::class, $result->getProblems()[0]);
    }

    public function testConversionStatusResult()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('conversion/conversion-status.json')),
        ]);
        $item = $this->createMock(ConvertedItem::class);
        $item->method('getToken')->willReturn(\random_int(65535, 1065535));

        $result = $api->documentJobStatus($item);
        self::assertInstanceOf(ConversionStatusInterface::class, $result);
        self::assertEmpty($result->getError());
        self::assertNotEmpty($result->getStatus());
        self::assertInstanceOf(StatusResultInterface::class, $result->getResult());
        self::assertNotEmpty($result->getResult()->getUuid());
        self::assertEmpty($result->getResult()->getThumbnailsGroupUuid());
    }
}
