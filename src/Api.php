<?php

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
use Uploadcare\Interfaces\UploaderInterface;
use Uploadcare\Uploader\Uploader;

/**
 * Universal API for Uploadcare.
 */
class Api
{
    /**
     * @var FileApiInterface
     */
    private $fileApi;

    /**
     * @var GroupApiInterface
     */
    private $groupApi;

    /**
     * @var UploaderInterface
     */
    private $uploader;

    /**
     * @var ProjectApiInterface
     */
    private $projectApi;

    /**
     * @var WebhookApiInterface
     */
    private $webhookApi;

    /**
     * @var ConversionApiInterface
     */
    private $conversionApi;

    /**
     * @param string $publicKey
     * @param string $privateKey
     *
     * @return Api
     */
    public static function create($publicKey, $privateKey)
    {
        $configuration = Configuration::create($publicKey, $privateKey);

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

    /**
     * @return FileApiInterface
     */
    public function file()
    {
        return $this->fileApi;
    }

    /**
     * @return GroupApiInterface
     */
    public function group()
    {
        return $this->groupApi;
    }

    /**
     * @return ProjectApiInterface
     */
    public function project()
    {
        return $this->projectApi;
    }

    /**
     * @return UploaderInterface
     */
    public function uploader()
    {
        return $this->uploader;
    }

    /**
     * @return WebhookApiInterface
     */
    public function webhook()
    {
        return $this->webhookApi;
    }

    /**
     * @return ConversionApiInterface
     */
    public function conversion()
    {
        return $this->conversionApi;
    }
}
