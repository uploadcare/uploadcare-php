<?php

namespace Uploadcare\Serializer;

use Uploadcare\Interfaces\SerializableInterface;
use Uploadcare\Interfaces\Serializer\NameConverterInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Exceptions\ClassNotFoundException;
use Uploadcare\Serializer\Exceptions\ConversionException;
use Uploadcare\Serializer\Exceptions\MethodNotFoundException;
use Uploadcare\Serializer\Exceptions\SerializerException;

class Serializer implements SerializerInterface
{
    const JSON_OPTIONS_KEY = 'json_options';
    const EXCLUDE_PROPERTY_KEY = 'exclude_property';

    protected static $coreTypes = [
        'int' => true,
        'bool' => true,
        'string' => true,
        'float' => true,
        'array' => true,
    ];

    protected static $validClasses = [
        \DateTimeInterface::class,
        SerializableInterface::class,
    ];

    protected static $defaultJsonOptions = JSON_PRETTY_PRINT;
    /**
     * @var NameConverterInterface
     */
    private $nameConverter;

    public function __construct(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;
    }

    /**
     * @param object $object
     * @param array  $context
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function serialize($object, array $context = [])
    {
        throw new \RuntimeException('Not implemented yet');
    }

    /**
     * @param string      $string
     * @param string|null $className
     * @param array       $context
     *
     * @return object|array
     *
     * @throws \RuntimeException
     */
    public function deserialize($string, $className = null, array $context = [])
    {
        $options = isset($context[self::JSON_OPTIONS_KEY]) ? $context[self::JSON_OPTIONS_KEY] : self::$defaultJsonOptions;

        $data = \json_decode($string, true, 512, $options);
        if ($className === null) {
            return $data;
        }
        if (!\class_exists($className)) {
            throw new ClassNotFoundException(\sprintf('Class \'%s\' not found', $className));
        }

        return $this->denormalize($data, $className, $context);
    }

    /**
     * @param array  $data
     * @param string $className
     * @param array  $context
     *
     * @return object
     */
    protected function denormalize(array $data, $className, array $context)
    {
        $this->validateClass($className);

        $class = new $className;
        $excluded = isset($context[self::EXCLUDE_PROPERTY_KEY]) ? $context[self::EXCLUDE_PROPERTY_KEY] : [];

        if (!$class instanceof SerializableInterface) {
            throw new SerializerException(\sprintf('Class \'%s\' must implements the \'%s\' interface', $className, SerializableInterface::class));
        }

        $rules = $class::rules();
        foreach ($data as $propertyName => $value) {
            $convertedName = $this->nameConverter->denormalize($propertyName);
            if (\array_key_exists($convertedName, \array_flip($excluded))) {
                continue;
            }
            $methodName = $this->getMethodName($convertedName);

            if (!\array_key_exists($convertedName, $rules)) {
                continue;
            }
            if (!\method_exists($class, $methodName)) {
                throw new MethodNotFoundException(\sprintf('Method \'%s\' not found in class \'%s\'', $methodName, $className));
            }

            $rule = $rules[$convertedName];

            if (\array_key_exists($rule, self::$coreTypes) && $value !== null) {
                \settype($value, $rule);
            }

            if ($value !== null && \is_a($rule, \DateTimeInterface::class, true)) {
                $value = $this->denormalizeDate($value);
            }

            if (\is_array($value) && !\array_key_exists($rule, self::$coreTypes)) {
                if (!\class_exists($rule)) {
                    throw new ClassNotFoundException(\sprintf('Class \'%s\' not found', $rule));
                }

                $value = $this->denormalize($value, $rule, $class::rules());
            }

            $class->{$methodName}($value);
        }

        return $class;
    }

    /**
     * @param string $dateTime Date string in `Y-m-d\TH:i:s.u\Z` format
     *
     * @return \DateTimeInterface
     */
    private function denormalizeDate($dateTime)
    {
        if (empty(\ini_get('date.timezone'))) {
            @\trigger_error('You should set your date.timezone in php.ini');
        }
        $date = \date_create($dateTime);
        if ($date === false) {
            throw new ConversionException(\sprintf('Unable to convert \'%s\' to \'%s\'', $dateTime, \DateTime::class));
        }

        return $date;
    }

    /**
     * @param string $className
     *
     * @return void
     */
    private function validateClass($className)
    {
        foreach (self::$validClasses as $validClass) {
            if (\is_a($className, $validClass, true)) {
                return;
            }
        }

        throw new SerializerException(\sprintf('Class \'%s\' must implements any of \'%s\' interfaces', $className, \implode(', ', self::$validClasses)));
    }

    private function getMethodName($propertyName)
    {
        return \sprintf('set%s', \ucfirst($propertyName));
    }
}
