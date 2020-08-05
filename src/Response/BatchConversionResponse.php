<?php

namespace Uploadcare\Response;

use Uploadcare\Conversion\ConvertedCollection;
use Uploadcare\Conversion\ConvertedItem;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Response for conversion request.
 */
class BatchConversionResponse implements BatchResponseInterface, SerializableInterface
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
        $this->result = new ConvertedCollection();
    }

    public static function rules()
    {
        return [
            'problems' => 'array',
            'result' => ConvertedCollection::class,
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
            $item = null;
            if (\is_string($problem)) {
                $item = (new ResponseProblem())
                    ->setId($k)
                    ->setReason($problem);
            }
            if (\is_array($problem)) {
                foreach ($problem as $sKey => $sValue) {
                    $item = (new ResponseProblem())
                        ->setId($sKey)
                        ->setReason($sValue);
                }
            }

            if ($item instanceof ResponseProblemInterface)
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
