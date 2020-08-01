<?php


namespace Tests\Api;


use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Uploadcare\Api;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\SignatureInterface;
use Uploadcare\Interfaces\UploaderInterface;
use Uploadcare\Serializer\SerializerFactory;

class CommonApiTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /** @noinspection PhpParamsInspection */
    protected function setUp()
    {
        parent::setUp();
        $this->configuration = new Configuration(
            'public-key',
            $this->getMockBuilder(SignatureInterface::class)->getMock(),
            $this->getMockBuilder(ClientInterface::class)->getMock(),
            SerializerFactory::create()
        );
    }

    public function testGetFileApi()
    {
        self::assertInstanceOf(FileApiInterface::class, (new Api($this->configuration))->file());
    }

    public function testGetGroupApi()
    {
        self::assertInstanceOf(GroupApiInterface::class, (new Api($this->configuration))->group());
    }

    public function testGetUploaderApi()
    {
        self::assertInstanceOf(UploaderInterface::class, (new Api($this->configuration))->uploader());
    }

    public function testCreateApi()
    {
        $api = Api::create('public-key', 'private-key');
        self::assertInstanceOf(Api::class, $api);
    }
}
