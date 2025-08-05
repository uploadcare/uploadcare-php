<?php declare(strict_types=1);

namespace Uploadcare\Exception\Upload;

use GuzzleHttp\Exception\ClientException;

class ThrottledException extends AbstractClientException
{
    private int $retryAfter = 10;

    public function __construct(string $message = '', int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($previous instanceof ClientException) {
            $response = $previous->getResponse();
            $retryHeader = $response->getHeader('Retry-After');

            if (!empty($retryHeader)) {
                $retry = \current($retryHeader);
                if ((int) $retry > 10) {
                    $this->retryAfter = (int) $retry;
                }
            }
        }
    }

    public function setRetryAfter(int $retryAfter): self
    {
        $this->retryAfter = $retryAfter;

        return $this;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
