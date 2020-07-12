<?php

namespace Tests\Serializer\Examples;

use Uploadcare\Interfaces\SerializableInterface;

class ExampleParent implements SerializableInterface
{
    /**
     * @var string|null
     */
    private $name = null;

    /**
     * @var array|ExampleIncluded[]
     */
    private $dates = [];

    public static function rules()
    {
        return [
            'name' => 'string',
            'dates' => [ExampleIncluded::class],
        ];
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addDate(ExampleIncluded $included)
    {
        $this->dates[] = $included;

        return $this;
    }

    public function getDates()
    {
        return $this->dates;
    }
}
