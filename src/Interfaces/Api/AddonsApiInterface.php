<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Exception\HttpException;
use Uploadcare\Interfaces\File\FileInfoInterface;

interface AddonsApiInterface
{
    /**
     * @param FileInfoInterface|string $id FileInfo or file uuid
     *
     * @return string Request ID
     *
     * @throws HttpException|\RuntimeException
     */
    public function requestAwsRecognition($id): string;

    /**
     * @param string $id Request ID
     *
     * @return string Status of AWS recognition. Could be "in_progress", "error", "done", "unknown"
     *
     * @throws HttpException|\RuntimeException
     */
    public function checkAwsRecognition(string $id): string;

    /**
     * @param FileInfoInterface|string $id    FileInfo or file uuid
     * @param bool                     $purge Purge infected file
     *
     * @return string Request ID
     */
    public function requestAntivirusScan($id, bool $purge = false): string;

    /**
     * @param string $id Request ID
     *
     * @return string Status of antivirus scan. Could be "in_progress", "error", "done", "unknown"
     */
    public function checkAntivirusScan(string $id): string;
}
