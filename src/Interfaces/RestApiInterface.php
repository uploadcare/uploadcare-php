<?php

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
    const BASE_URL = 'api.uploadcare.com';

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
    public function getFileApi();

    /**
     * Group operations:
     *      - List of groups
     *      - Group info
     *      - Store group.
     *
     * @return GroupApiInterface
     */
    public function getGroupApi();

    /**
     * Project operations:
     *      - Project info.
     *
     * @return ProjectApiInterface
     */
    public function getProjectApi();

    /**
     * Webhook operations:
     *      - List of webhooks
     *      - Create webhook
     *      - Update webhook
     *      - Delete webhook.
     *
     * @return WebhookApiInterface
     */
    public function getWebhookApi();

    /**
     * Conversion operations:
     *      - Convert document
     *      - Document conversion job status
     *      - Convert video
     *      - Video conversion job status.
     *
     * @return ConversionApiInterface
     */
    public function getConversionApi();
}
