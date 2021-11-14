<?php declare(strict_types=1);

namespace Uploadcare\Exception\Upload;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Message;

abstract class AbstractClientException extends \RuntimeException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        if ($previous instanceof ClientException) {
            $code = $previous->getCode();
            $message = Message::toString($previous->getResponse());
        }

        parent::__construct($message, $code, $previous);
    }
}
