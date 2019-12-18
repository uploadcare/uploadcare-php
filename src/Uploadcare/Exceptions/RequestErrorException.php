<?php

namespace Uploadcare\Exceptions;

use Exception;

/**
 * Class RequestErrorException
 * @package Uploadcare\Exceptions
 */
class RequestErrorException extends Exception
{
    private $requestData;

    /**
     * RequestErrorException constructor.
     * @param $message
     * @param $requestData
     */
    public function __construct($message, $requestData)
    {
        parent::__construct($message);
        $this->requestData = $requestData;
    }

    /**
     * Return array with request data.
     *
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }
}
