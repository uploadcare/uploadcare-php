<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Apis\ConversionApi;
use Uploadcare\Apis\FileApi;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Apis\ProjectApi;
use Uploadcare\Apis\WebhookApi;
use Uploadcare\Interfaces\Api\ConversionApiInterface;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\Api\ProjectApiInterface;
use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\RestApiInterface;
use Uploadcare\Interfaces\UploaderInterface;
use Uploadcare\Uploader\Uploader;

/**
 * Universal API for Uploadcare.
 */
final class Api implements RestApiInterface
{
    private $fileApi;
    private $groupApi;
    private $uploader;
    private $projectApi;
    private $webhookApi;
    private $conversionApi;

    /**
     * @param string $publicKey
     * @param string $secretKey
     *
     * @return Api
     */
    public static function create(string $publicKey, string $secretKey): self
    {
        $configuration = Configuration::create($publicKey, $secretKey);

        return new static($configuration);
    }

    /**
     * Api constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->fileApi = new FileApi($configuration);
        $this->groupApi = new GroupApi($configuration);
        $this->uploader = new Uploader($configuration);
        $this->projectApi = new ProjectApi($configuration);
        $this->webhookApi = new WebhookApi($configuration);
        $this->conversionApi = new ConversionApi($configuration);
    }

    public function file(): FileApiInterface
    {
        return $this->fileApi;
    }

    public function group(): GroupApiInterface
    {
        return $this->groupApi;
    }

    public function project(): ProjectApiInterface
    {
        return $this->projectApi;
    }

    public function uploader(): UploaderInterface
    {
        return $this->uploader;
    }

    public function webhook(): WebhookApiInterface
    {
        return $this->webhookApi;
    }

    public function conversion(): ConversionApiInterface
    {
        return $this->conversionApi;
    }
}
