<?php

namespace Uploadcare\Response;

use Uploadcare\Conversion\ConvertedItem;
use Uploadcare\Conversion\DocumentConvertCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Response for conversion request.
 */
class BatchConvertDocumentResponse implements BatchResponseInterface, SerializableInterface
{
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
        $this->result = new DocumentConvertCollection();
    }

    public static function rules()
    {
        return [
            'problems' => 'array',
            'result' => DocumentConvertCollection::class,
        ];
    }

    public function getStatus()
    {
        return 'ok';
    }

    public function getProblems()
    {
        return $this->problems;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setProblems(array $problems)
    {
        foreach ($problems as $k => $problem) {
            $item = (new ResponseProblem())
                ->setId($k)
                ->setReason($problem);
            $this->addProblem($item);
        }
    }

    public function addProblem(ResponseProblemInterface $problem)
    {
        if (!\in_array($problem, $this->problems, true)) {
            $this->problems[] = $problem;
        }

        return $this;
    }

    public function addResult(ConvertedItem $item)
    {
        if (!$this->result->contains($item)) {
            $this->result->add($item);
        }

        return $this;
    }
}
