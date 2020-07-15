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
        $this->problems = [];
        $this->result = new FileCollection();
    }

    public static function rules()
    {
        return [
            'status' => 'string',
            'problems' => 'array',
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

    /**
     * @param array $problems
     *
     * @return $this
     */
    public function setProblems(array $problems)
    {
        foreach ($problems as $key => $value) {
            $item = (new ResponseProblem())
                ->setId($key)
                ->setReason($value);
            $this->addProblem($item);
        }

        return $this;
    }

    public function addProblem(ResponseProblemInterface $problem)
    {
        if (!\in_array($problem, $this->problems)) {
            $this->problems[] = $problem;
        }

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
