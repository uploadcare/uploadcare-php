<?php

namespace Tests\Serializer\Examples;

use Uploadcare\Interfaces\SerializableInterface;

class ExampleIncluded implements SerializableInterface
{
    /**
     * @var \DateTimeInterface|null
     */
    private $dateTime;

    public static function rules(): array
    {
        return [
            'dateTime' => \DateTime::class,
        ];
    }

    public function setDateTime(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }
}
