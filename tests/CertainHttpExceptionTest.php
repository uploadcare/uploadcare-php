<?php declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\{Message, Request, Response};
use PHPUnit\Framework\TestCase;
use Uploadcare\Configuration;
use Uploadcare\Exception\HttpException;
use Uploadcare\Exception\Upload\{AbstractClientException, AccountException, FileTooLargeException, RequestParametersException, ThrottledException};
use Uploadcare\Uploader\Uploader;

class CertainHttpExceptionTest extends TestCase
{
    public function provideExceptions(): array
    {
        $request = new Request('GET', 'https://example.com');

        return [
            AccountException::class => [
                new ClientException('', $request, new Response(403, [], 'Account has been blocked.')),
                AccountException::class,
            ],
            FileTooLargeException::class => [
                new ClientException('', $request, new Response(413, [], 'Direct uploads only support files smaller than 100MB')),
                FileTooLargeException::class,
            ],
            RequestParametersException::class => [
                new ClientException('', $request, new Response(400, [], 'File is too large.')),
                RequestParametersException::class,
            ],
            ThrottledException::class => [
                new ClientException('', $request, new Response(429, ['Retry-After' => 40], 'Request was throttled.')),
                ThrottledException::class,
            ],
        ];
    }

    /**
     * @dataProvider provideExceptions
     */
    public function testExceptionMessage(ClientException $exception, string $class): void
    {
        /** @var AbstractClientException $ex */
        $ex = new $class('', 0, $exception);
        /** @var Response $response */
        $response = $exception->getResponse();
        self::assertEquals($ex->getMessage(), Message::toString($response));
        self::assertEquals($ex->getCode(), $response->getStatusCode());

        if ($class === ThrottledException::class) {
            self::assertNotEquals(10, $ex->getRetryAfter());
        }
    }

    /**
     * @dataProvider provideExceptions
     */
    public function testUploaderDefineExceptionMethod(ClientException $exception, string $class): void
    {
        $uploader = new Uploader(Configuration::create('demopublickey', 'demosecretkey'));
        $handleException = (new \ReflectionObject($uploader))->getMethod('handleException');
        $handleException->setAccessible(true);

        $result = $handleException->invokeArgs($uploader, [$exception]);
        self::assertInstanceOf($class, $result);
    }

    public function testUploaderWithUnknownExceptions(): void
    {
        $uploader = new Uploader(Configuration::create('demopublickey', 'demosecretkey'));
        $handleException = (new \ReflectionObject($uploader))->getMethod('handleException');
        $handleException->setAccessible(true);

        $unknownClientException = new ClientException('', new Request('GET', 'https://example.com'), new Response(411, ['Retry-After' => 40], 'Request was throttled.'));
        self::assertInstanceOf(HttpException::class, $handleException->invokeArgs($uploader, [$unknownClientException]));

        $abstractException = new \Exception();
        self::assertInstanceOf(HttpException::class, $handleException->invokeArgs($uploader, [$abstractException]));
    }
}
