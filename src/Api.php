<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Apis\{AddonsApi, ConversionApi, FileApi, GroupApi, ProjectApi, WebhookApi};
use Uploadcare\Interfaces\Api\{AddonsApiInterface, ConversionApiInterface, FileApiInterface,
    GroupApiInterface, ProjectApiInterface, WebhookApiInterface};
use Uploadcare\Interfaces\{ConfigurationInterface, RestApiInterface, UploaderInterface};
use Uploadcare\Uploader\Uploader;

/**
 * Universal API for Uploadcare.
 */
final class Api implements RestApiInterface
{
    private FileApiInterface $fileApi;
    private GroupApiInterface $groupApi;
    private UploaderInterface $uploader;
    private ProjectApiInterface $projectApi;
    private WebhookApiInterface $webhookApi;
    private ConversionApiInterface $conversionApi;
    private AddonsApiInterface $addonsApi;

    public static function create(string $publicKey, string $secretKey): self
    {
        $configuration = Configuration::create($publicKey, $secretKey);

        return new self($configuration);
    }

    /**
     * Api constructor.
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->fileApi = new FileApi($configuration);
        $this->groupApi = new GroupApi($configuration);
        $this->uploader = new Uploader($configuration);
        $this->projectApi = new ProjectApi($configuration);
        $this->webhookApi = new WebhookApi($configuration);
        $this->conversionApi = new ConversionApi($configuration);
        $this->addonsApi = new AddonsApi($configuration);
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

    public function addons(): AddonsApiInterface
    {
        return $this->addonsApi;
    }
}
