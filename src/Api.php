<?php


namespace Uploadcare;


use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\UploaderInterface;

class Api extends FileApi
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @return UploaderInterface
     */
    public function getUploader()
    {
        return new Uploader($this->configuration);
    }
}
