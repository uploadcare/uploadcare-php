<?php

namespace Uploadcare\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchFileResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class BatchFileResponse implements BatchFileResponseInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var ResponseProblemInterface[]
     */
    private $problems;

    /**
     * @var CollectionInterface
     */
    private $result;

    public function __construct()
    {
        $this->result = new FileCollection();
    }

    public static function rules()
    {
        return [
            'status' => 'string',
            'problems' => [ResponseProblem::class],
            'result' => FileCollection::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProblems()
    {
        return $this->problems;
    }

    public function addProblem(ResponseProblemInterface $problem)
    {
        $this->problems = $problem;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResult()
    {
        return $this->result;
    }

    public function addResult(FileInfoInterface $fileInfo)
    {
        if (!$this->result->contains($fileInfo)) {
            $this->result->add($fileInfo);
        }

        return $this;
    }
}
