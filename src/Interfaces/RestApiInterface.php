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
     */
    public function file(): FileApiInterface;

    /**
     * Group operations:
     *      - List of groups
     *      - Group info
     *      - Store group.
     */
    public function group(): GroupApiInterface;

    /**
     * Project operations:
     *      - Project info.
     */
    public function project(): ProjectApiInterface;

    /**
     * Webhook operations:
     *      - List of webhooks
     *      - Create webhook
     *      - Update webhook
     *      - Delete webhook.
     */
    public function webhook(): WebhookApiInterface;

    /**
     * Conversion operations:
     *      - Convert document
     *      - Document conversion job status
     *      - Convert video
     *      - Video conversion job status.
     */
    public function conversion(): ConversionApiInterface;
}
