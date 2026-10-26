<?php declare(strict_types=1);

namespace Tests\Search;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Uploadcare\Api;
use Uploadcare\Apis\SearchApi;
use Uploadcare\Configuration;
use Uploadcare\Exception\SearchException;
use Uploadcare\File as FileDecorator;
use Uploadcare\Interfaces\Api\SearchApiInterface;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Response\SearchResponse;
use Uploadcare\Search\SearchQuery;

class SearchApiTest extends TestCase
{
    /**
     * @var array<int, array>
     */
    private array $history = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->history = [];
    }

    protected function fileContents(string $name): string
    {
        return \file_get_contents(\sprintf('%s/_data/%s', \dirname(__DIR__), $name));
    }

    /**
     * @param array<int, Response> $responses
     */
    protected function getConfig(array $responses = []): ConfigurationInterface
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        $configuration = Configuration::create('demo-public-key', 'demo-private-key');
        $clientProperty = (new \ReflectionObject($configuration))->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($configuration, new Client(['handler' => $stack]));

        return $configuration;
    }

    protected function response(string $fixture): Response
    {
        return new Response(200, [
            'Content-Type' => \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION),
        ], $this->fileContents($fixture));
    }

    protected function request(int $index): RequestInterface
    {
        self::assertArrayHasKey($index, $this->history, 'Request has not been made');

        return $this->history[$index]['request'];
    }

    protected function queryOf(RequestInterface $request): array
    {
        $query = [];
        \parse_str($request->getUri()->getQuery(), $query);

        return $query;
    }

    public function testApiExposesSearchApi(): void
    {
        self::assertInstanceOf(SearchApiInterface::class, (new Api($this->getConfig()))->search());
    }

    public function testSearchRequest(): void
    {
        $api = new SearchApi($this->getConfig([$this->response('file-search-response.json')]));
        $api->search((new SearchQuery())->query('vacation')->tagsAny(['cat']), 2, 0, true);

        $request = $this->request(0);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('api.uploadcare.com', $request->getUri()->getHost());
        self::assertSame('/files/search/', $request->getUri()->getPath());
        self::assertSame(['limit' => '2', 'offset' => '0', 'include' => 'appdata'], $this->queryOf($request));
        self::assertSame('application/vnd.uploadcare-v0.7+json', $request->getHeaderLine('Accept'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame(['query' => 'vacation', 'tags' => ['any' => ['cat']]], \json_decode((string) $request->getBody(), true));
    }

    public function testAppDataIsNotRequestedByDefault(): void
    {
        $api = new SearchApi($this->getConfig([$this->response('file-search-response.json')]));
        $api->search((new SearchQuery())->isImage());

        self::assertSame(['limit' => '20', 'offset' => '0'], $this->queryOf($this->request(0)));
    }

    public function testSearchResponse(): void
    {
        $api = new SearchApi($this->getConfig([$this->response('file-search-response.json')]));
        $query = (new SearchQuery())->query('vacation');
        $result = $api->search($query, 2);

        self::assertInstanceOf(SearchResponse::class, $result);
        self::assertSame(3, $result->getTotal());
        self::assertSame(2, $result->getPerPage());
        self::assertNull($result->getPrevious());
        self::assertStringContainsString('offset=2', (string) $result->getNext());
        self::assertEquals($query, $result->getQuery());
        self::assertNotSame($query, $result->getQuery());

        $files = $result->getResults();
        self::assertCount(2, $files);
        $first = $files->first();
        self::assertInstanceOf(FileDecorator::class, $first);
        self::assertSame('575ed4e8-f4e8-4c14-a58b-1527b6d9ee46', $first->getUuid());
        self::assertSame('vacation-photo.jpg', $first->getOriginalFilename());
        self::assertSame(['outdoor', 'cat'], $first->getTags()->toArray());
    }

    public function testHighlights(): void
    {
        $api = new SearchApi($this->getConfig([$this->response('file-search-response.json')]));
        $result = $api->search((new SearchQuery())->query('vacation'));

        $expected = [
            'original_filename' => ['<em>vacation</em>-photo.jpg'],
            'metadata' => ['trip' => ['<em>vacation</em> 2024']],
        ];
        self::assertSame($expected, $result->getHighlight('575ed4e8-f4e8-4c14-a58b-1527b6d9ee46'));
        self::assertSame($expected, $result->getHighlight($result->getResults()->first()));
        self::assertSame([], $result->getHighlight('a1cfbdb0-8293-4f72-b5ec-4f93c40303dc'), 'Filter-only match has no highlight');
        self::assertSame([], $result->getHighlight('00000000-0000-4000-8000-000000000000'));
    }

    public function testNextPageRepeatsTheQuery(): void
    {
        $api = new SearchApi($this->getConfig([
            $this->response('file-search-response.json'),
            $this->response('file-search-last-page-response.json'),
        ]));
        $first = $api->search((new SearchQuery())->query('vacation')->sort('-size'), 2, 0, true);

        $second = $api->nextPage($first);

        self::assertInstanceOf(SearchResponse::class, $second);
        self::assertSame('3a2b1c0d-1111-4222-8333-444455556666', $second->getResults()->first()->getUuid());
        self::assertSame(['<em>vacation</em>-plan.pdf'], $second->getHighlight('3a2b1c0d-1111-4222-8333-444455556666')['original_filename']);

        $request = $this->request(1);
        self::assertSame('POST', $request->getMethod());
        self::assertSame(['limit' => '2', 'offset' => '2', 'include' => 'appdata'], $this->queryOf($request));
        self::assertSame((string) $this->request(0)->getBody(), (string) $request->getBody());

        self::assertNull($api->nextPage($second));
        self::assertCount(2, $this->history);
    }

    public function testNextPageStopsAtTheWindow(): void
    {
        $api = new SearchApi($this->getConfig());
        $response = (new SearchResponse())
            ->setNext('https://api.uploadcare.com/files/search/?limit=100&offset=1000')
            ->setQuery((new SearchQuery())->isImage());

        self::assertNull($api->nextPage($response));
        self::assertCount(0, $this->history);
    }

    public function testNextPageShrinksTheLastPageToTheWindow(): void
    {
        $api = new SearchApi($this->getConfig([$this->response('file-search-last-page-response.json')]));
        $response = (new SearchResponse())
            ->setNext('https://api.uploadcare.com/files/search/?limit=30&offset=990')
            ->setQuery((new SearchQuery())->isImage());

        self::assertInstanceOf(SearchResponse::class, $api->nextPage($response));
        self::assertSame(['limit' => '10', 'offset' => '990'], $this->queryOf($this->request(0)));
    }

    public function testNextPageIsNotAffectedByChangesOfTheQuery(): void
    {
        $api = new SearchApi($this->getConfig([
            $this->response('file-search-response.json'),
            $this->response('file-search-last-page-response.json'),
        ]));
        $query = (new SearchQuery())->query('vacation');
        $first = $api->search($query, 2);

        $query->isImage();
        $api->nextPage($first);

        self::assertSame((string) $this->request(0)->getBody(), (string) $this->request(1)->getBody());
    }

    public function testNextPageRequiresTheQuery(): void
    {
        $api = new SearchApi($this->getConfig());
        $response = (new SearchResponse())->setNext('https://api.uploadcare.com/files/search/?limit=20&offset=20');

        $this->expectException(SearchException::class);

        $api->nextPage($response);
    }

    public function provideInvalidPagination(): array
    {
        return [
            'zero limit' => [0, 0],
            'limit over 100' => [101, 0],
            'negative offset' => [20, -1],
            'past the window' => [100, 901],
        ];
    }

    /**
     * @dataProvider provideInvalidPagination
     */
    public function testInvalidPaginationIsRejectedBeforeRequest(int $limit, int $offset): void
    {
        $api = new SearchApi($this->getConfig());

        try {
            $api->search((new SearchQuery())->isImage(), $limit, $offset);
            self::fail('SearchException expected');
        } catch (SearchException $e) {
            self::assertCount(0, $this->history);
        }
    }

    public function testLastPageOfTheWindowIsAllowed(): void
    {
        $api = new SearchApi($this->getConfig([$this->response('file-search-last-page-response.json')]));
        $api->search((new SearchQuery())->isImage(), 100, 900);

        self::assertSame(['limit' => '100', 'offset' => '900'], $this->queryOf($this->request(0)));
    }

    public function testInvalidQueryIsRejectedBeforeRequest(): void
    {
        $api = new SearchApi($this->getConfig());

        try {
            $api->search(new SearchQuery());
            self::fail('SearchException expected');
        } catch (SearchException $e) {
            self::assertCount(0, $this->history);
        }
    }
}
