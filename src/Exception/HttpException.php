<?php

namespace Uploadcare\Exception;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Psr\Http\Message\RequestInterface;

class HttpException extends \RuntimeException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        if ($previous !== null) {
            $message = $this->makeMessage($previous, $message);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param \Exception $exception
     * @param string     $message
     *
     * @return string
     */
    protected function makeMessage(\Exception $exception, $message = '')
    {
        $messages = [];
        if (!empty($message)) {
            $messages[] = $message;
        }
        switch (true) {
            case $exception instanceof ConnectException:
                $messages[] = $this->messageString($exception->getRequest(), 'cannot connect to host');
                break;
            case $exception instanceof TooManyRedirectsException:
                $messages[] = $this->messageString($exception->getRequest(), 'too many redirects');
                break;
            case $exception instanceof ClientException:
                $messages[] = $this->messageString($exception->getRequest(), 'bad request');
                break;
            case $exception instanceof ServerException:
                $messages[] = $this->messageString($exception->getRequest(), 'server error');
                break;
            default:
                break;
        }

        return \implode("\n", $messages);
    }

    private function messageString(RequestInterface $request, $message = '')
    {
        if (empty($message)) {
            $message = 'fail';
        }

        return \sprintf('%s: %s', \GuzzleHttp\Psr7\str($request), $message);
    }
}
