<?php

namespace Uploadcare\Exception;

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
        $messages[] = $exception->getMessage();

        if (\method_exists($exception, 'getRequest') && $exception->getRequest() instanceof RequestInterface) {
            $messages[] = $this->messageString($exception->getRequest(), $exception->getMessage());
        }

        return \implode("\n", $messages);
    }

    private function messageString(RequestInterface $request, $message = '')
    {
        if (empty($message)) {
            $message = 'Fail';
        }

        return \sprintf('%s: %s', (string) $request->getUri(), $message);
    }
}
