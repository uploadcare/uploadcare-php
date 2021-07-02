<?php declare(strict_types=1);

namespace Uploadcare\Exception\Upload;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class ThrottledException extends AbstractClientException
{
    private $retryAfter = 10;

    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($previous instanceof ClientException) {
            $response = $previous->getResponse();
            if ($response instanceof ResponseInterface) {
                $retryHeader = $response->getHeader('Retry-After');

                $this->retryAfter = $retryHeader[0] ?? 10;
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
        return (int) $this->retryAfter;
    }
}
