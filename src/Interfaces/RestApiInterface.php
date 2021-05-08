<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

use Uploadcare\Interfaces\Api\ConversionApiInterface;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\Api\ProjectApiInterface;
use Uploadcare\Interfaces\Api\WebhookApiInterface;

/**
 * Uploadcare REST API.
 */
interface RestApiInterface
{
    public const BASE_URL = 'api.uploadcare.com';

    /**
     * File operations:
     *      - List of files
     *      - Store file
     *      - Delete file
     *      - File info
     *      - Batch file storing
     *      - Batch file delete
     *      - Copy file to local storage
     *      - Copy file to remote storage.
     *
     * @return FileApiInterface
     */
    public function file(): FileApiInterface;

    /**
     * Group operations:
     *      - List of groups
     *      - Group info
     *      - Store group.
     *
     * @return GroupApiInterface
     */
    public function group(): GroupApiInterface;

    /**
     * Project operations:
     *      - Project info.
     *
     * @return ProjectApiInterface
     */
    public function project(): ProjectApiInterface;

    /**
     * Webhook operations:
     *      - List of webhooks
     *      - Create webhook
     *      - Update webhook
     *      - Delete webhook.
     *
     * @return WebhookApiInterface
     */
    public function webhook(): WebhookApiInterface;

    /**
     * Conversion operations:
     *      - Convert document
     *      - Document conversion job status
     *      - Convert video
     *      - Video conversion job status.
     *
     * @return ConversionApiInterface
     */
    public function conversion(): ConversionApiInterface;
}
