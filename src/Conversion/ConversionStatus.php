<?php

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\StatusResultInterface;
use Uploadcare\Interfaces\SerializableInterface;

class ConversionStatus implements ConversionStatusInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @var StatusResultInterface
     */
    private $result;

    public static function rules()
    {
        return [
            'status' => 'string',
            'error' => 'string',
            'result' => ConversionResult::class,
        ];
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return ConversionStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     *
     * @return ConversionStatus
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return StatusResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param StatusResultInterface $result
     *
     * @return ConversionStatus
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}
