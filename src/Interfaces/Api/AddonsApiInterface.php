<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Exception\HttpException;
use Uploadcare\Interfaces\Conversion\RemoveBackgroundRequestInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;

interface AddonsApiInterface
{
    /**
     * Execute AWS Rekognition Moderation Add-On for a given target to detect moderation labels in an image.
     * Note: Detected moderation labels are stored in the file's appdata.
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#tag/Add-Ons/operation/awsRekognitionDetectModerationLabelsExecute
     * @see https://docs.aws.amazon.com/rekognition/latest/dg/moderation.html
     *
     * @param FileInfoInterface|string $id
     *
     * @return string Request ID
     *
     * @throws HttpException|\RuntimeException
     */
    public function requestAwsRecognitionModeration($id): string;

    /**
     * Check the status of an Add-On execution request that had been started using the Execute Add-On operation.
     *
     * @param string $id Request ID
     *
     * @return string Status of AWS recognition. Could be "in_progress", "error", "done", "unknown"
     */
    public function checkAwsRecognitionModeration(string $id): string;

    /**
     * Execute AWS Rekognition Add-On for a given target to detect labels in an image.
     * Note: Detected labels are stored in the file's appdata.
     *
     * @see https://docs.aws.amazon.com/rekognition/latest/dg/labels-detect-labels-image.html
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/awsRekognitionExecute
     *
     * @param FileInfoInterface|string $id FileInfo or file uuid
     *
     * @return string Request ID
     *
     * @throws HttpException|\RuntimeException
     */
    public function requestAwsRecognition($id): string;

    /**
     * Check the status of an Add-On execution request that had been started using the Execute Add-On operation.
     *
     * @param string $id Request ID
     *
     * @return string Status of AWS recognition. Could be "in_progress", "error", "done", "unknown"
     *
     * @throws HttpException|\RuntimeException
     */
    public function checkAwsRecognition(string $id): string;

    /**
     * Execute ClamAV virus checking Add-On for a given target.
     *
     * @see https://www.clamav.net/
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/ucClamavVirusScanExecute
     *
     * @param FileInfoInterface|string $id    FileInfo or file uuid
     * @param bool                     $purge Purge infected file
     *
     * @return string Request ID
     */
    public function requestAntivirusScan($id, bool $purge = false): string;

    /**
     * Check the status of an Add-On execution request that had been started using the Execute Add-On operation.
     *
     * @param string $id Request ID
     *
     * @return string Status of antivirus scan. Could be "in_progress", "error", "done", "unknown"
     */
    public function checkAntivirusScan(string $id): string;

    /**
     * Execute remove.bg background image removal Add-On for a given target.
     *
     * @see https://remove.bg/
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/removeBgExecute
     *
     * @param FileInfoInterface|string              $id
     * @param RemoveBackgroundRequestInterface|null $backgroundRequest Object with Add-On specific parameters
     *
     * @return string Request ID
     */
    public function requestRemoveBackground($id, ?RemoveBackgroundRequestInterface $backgroundRequest = null): string;

    /**
     * Check the status of an Add-On execution request that had been started using the Execute Add-On operation.
     *
     * @param string $id Request ID
     */
    public function checkRemoveBackground(string $id): string;
}
