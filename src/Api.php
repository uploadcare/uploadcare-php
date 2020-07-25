<?php

namespace Uploadcare;

use Uploadcare\Apis\FileApi;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\UploaderInterface;

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
     * @return UploaderInterface
     */
    public function uploader()
    {
        return $this->uploader;
    }
}
