<?php declare(strict_types=1);

namespace Uploadcare\Serializer;

use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\SerializableInterface;
use Uploadcare\Interfaces\Serializer\{NameConverterInterface, SerializerInterface};
use Uploadcare\Serializer\Exceptions\{ClassNotFoundException, ConversionException, MethodNotFoundException, SerializerException};

class Serializer implements SerializerInterface
{
    public const JSON_OPTIONS_KEY = 'json_options';
    public const EXCLUDE_PROPERTY_KEY = 'exclude_property';
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.u\Z';
    public const DATE_FORMAT_SHORT = 'Y-m-d\TH:i:s\Z';
    public const ORIGINAL_DATE_FORMATS = [ 'Y-m-d\TH:i:s', \DateTimeInterface::ATOM, 'Y-m-d\TH:i:s.u' ];

    protected static array $coreTypes = [
        'int' => true,
        'bool' => true,
        'string' => true,
        'float' => true,
        'array' => true,
    ];

    protected static array $validClasses = [
        \DateTimeInterface::class,
        SerializableInterface::class,
    ];

    protected static int $defaultJsonOptions = JSON_PRETTY_PRINT;

    private NameConverterInterface $nameConverter;

    public function __construct(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;
    }

    /**
     * @throws \RuntimeException
     */
    public function serialize(object $object, array $context = []): string
    {
        if (!$object instanceof SerializableInterface) {
            throw new SerializerException(\sprintf('Class \'%s\' must implements \'%s\' interface', \get_class($object), SerializableInterface::class));
        }

        $options = $context[self::JSON_OPTIONS_KEY] ?? self::$defaultJsonOptions;
        $normalized = [];
        $this->normalize($object, $normalized, $context);
        try {
            $result = \json_encode($normalized, JSON_THROW_ON_ERROR | $options);
        } catch (\Throwable $e) {
            throw new ConversionException(\sprintf('Unable to decode given data. Error is %s', \json_last_error_msg()));
        }

        return $result;
    }

    /**
     * @return object|array
     *
     * @throws \RuntimeException
     */
    public function deserialize(string $string, ?string $className = null, array $context = [])
    {
        $options = $context[self::JSON_OPTIONS_KEY] ?? self::$defaultJsonOptions;

        try {
            $data = \json_decode($string, true, 512, JSON_THROW_ON_ERROR | $options);
        } catch (\Throwable $e) {
            throw new ConversionException(\sprintf('Unable to decode given value. Error is %s', \json_last_error_msg()));
        }

        if ($className === null) {
            return $data;
        }
        if (!\class_exists($className)) {
            throw new ClassNotFoundException(\sprintf('Class \'%s\' not found', $className));
        }

        return $this->denormalize($data ?? [], $className, $context);
    }

    protected function normalize(SerializableInterface $object, array &$result = [], array $context = []): void
    {
        $rules = $object::rules();
        $excluded = $context[self::EXCLUDE_PROPERTY_KEY] ?? [];

        foreach ($rules as $propertyName => $rule) {
            if (\array_key_exists($propertyName, \array_flip($excluded))) {
                continue;
            }

            $convertedName = $this->nameConverter->normalize($propertyName);
            $method = $this->getMethodName($propertyName, 'get');
            if (!\method_exists($object, $method)) {
                // Method may be the same as property in case of `isSomething`
                $method = $propertyName;
            }

            if (!\method_exists($object, $method)) {
                throw new MethodNotFoundException(\sprintf('Method \'%s\' not found in class \'%s\'', $method, \get_class($object)));
            }
            $value = $object->{$method}();

            switch (true) {
                case !\is_object($value) && $value !== null && \array_key_exists($rule, self::$coreTypes):
                    \settype($value, $rule);
                    $result[$convertedName] = $value;
                    break;
                case $value instanceof \DateTime:
                    $result[$convertedName] = $this->normalizeDate($value);
                    break;
                case $value instanceof SerializableInterface:
                    $result[$convertedName] = [];
                    $this->normalize($value, $result[$convertedName], $context);
                    break;
                default:
                    $result[$convertedName] = null;
            }
        }
    }

    protected function denormalize(array $data, string $className, array $context): object
    {
        $this->validateClass($className);
        if (!\is_a($className, SerializableInterface::class, true)) {
            throw new SerializerException(\sprintf('Class \'%s\' must implements the \'%s\' interface', $className, SerializableInterface::class));
        }

        $class = new $className;
        $excluded = $context[self::EXCLUDE_PROPERTY_KEY] ?? [];

        $rules = $class::rules();
        $this->processData($class, $data, $rules, $excluded);

        return $class;
    }

    private function processData(SerializableInterface $class, array $data, array $rules, array $excluded): void
    {
        foreach ($data as $propertyName => $value) {
            $convertedName = $this->nameConverter->denormalize((string) $propertyName);
            if (!isset($rules[$convertedName])) {
                // Property can be named as `isSomething`
                $convertedName = \sprintf('is%s', \ucfirst($convertedName));
            }
            if (!isset($rules[$convertedName])) {
                // There is no rule with this name
                continue;
            }

            $rule = $rules[$convertedName];
            if (\is_a($rule, \ArrayAccess::class, true) && !\is_array($value)) {
                throw new ConversionException(\sprintf('The \'%s\' property declared as array or collection, but value is \'%s\'', $convertedName, \gettype($value)));
            }

            if (\is_array($rule)) {
                // This means the property contains an array with other classes, and
                // we need to denormalize this classes first.
                $innerClassName = $rule[\key($rule)];
                $this->denormalizeClassesArray($class, $innerClassName, $convertedName, $value);
                continue;
            }

            if (\is_a($rule, CollectionInterface::class, true)) {
                $innerClassName = $rule::elementClass();
                $this->denormalizeClassesArray($class, $innerClassName, $convertedName, $value);
                continue;
            }

            if (!\array_key_exists($convertedName, $rules) || \array_key_exists($convertedName, \array_flip($excluded))) {
                continue;
            }

            $methodName = $this->getMethodName($convertedName, 'set');
            if (!\method_exists($class, $methodName)) {
                throw new MethodNotFoundException(\sprintf('Method \'%s\' not found in class \'%s\'', $methodName, \get_class($class)));
            }

            if (\array_key_exists($rule, self::$coreTypes) && $value !== null) {
                \settype($value, $rule);
            }

            if ($value !== null && \is_a($rule, \DateTimeInterface::class, true)) {
                if (!\is_string($value)) {
                    throw new ConversionException(\sprintf('Unable to convert \'%s\' to \'%s\'', \gettype($value), \DateTime::class));
                }
                $value = $this->denormalizeDate($value);
            }

            if (\is_array($value) && !\array_key_exists($rule, self::$coreTypes)) {
                if (!\class_exists($rule)) {
                    throw new ClassNotFoundException(\sprintf('Class \'%s\' not found', $rule));
                }

                $value = $this->denormalize($value, $rule, $class::rules());
            }

            if ($value !== null) {
                $class->{$methodName}($value);
            }
        }
    }

    private function denormalizeClassesArray(SerializableInterface $parentClass, string $targetClassName, string $targetProperty, array $data): void
    {
        $set = false;
        $method = $this->getMethodName($targetProperty, 'add', true);
        if (!\method_exists($parentClass, $method)) {
            $set = true;
            $method = $this->getMethodName($targetProperty);
        }
        if (!\method_exists($parentClass, $method)) {
            throw new ConversionException(\vsprintf('Neither %s nor %s defined in %s', [$this->getMethodName($targetProperty, 'add'), $this->getMethodName($targetProperty), \get_class($parentClass)]));
        }

        $result = [];
        foreach ($data as $singleItem) {
            $value = \is_array($singleItem) ? $this->denormalize($singleItem, $targetClassName, []) : $singleItem;
            if ($value === null) {
                continue;
            }

            if (!$set) {
                $parentClass->{$method}($value);
            } else {
                $result[] = $value;
            }
        }

        if ($set) {
            $parentClass->{$method}($result);
        }
    }

    /**
     * @param string $dateTime Date string in `Y-m-d\TH:i:s.u\Z` format
     */
    private function denormalizeDate(string $dateTime): \DateTimeInterface
    {
        if (empty(\ini_get('date.timezone'))) {
            @\trigger_error('You should set your date.timezone in php.ini', E_USER_WARNING);
        }
        $date = \date_create_from_format(self::DATE_FORMAT, $dateTime);
        if ($date === false) {
            $date = \date_create_from_format(self::DATE_FORMAT_SHORT, $dateTime);
        }
        foreach (self::ORIGINAL_DATE_FORMATS as $dateFormat) {
            if ($date === false) {
                $date = \date_create_from_format($dateFormat, $dateTime);
            }
        }

        if ($date === false) {
            throw new ConversionException(\sprintf('Unable to convert \'%s\' to \'%s\'', $dateTime, \DateTime::class));
        }

        return $date;
    }

    private function normalizeDate(\DateTime $dateTime): string
    {
        if (empty(\ini_get('date.timezone'))) {
            @\trigger_error('You should set your date.timezone in php.ini', E_USER_WARNING);
        }

        return $dateTime->format(self::DATE_FORMAT);
    }

    private function validateClass(string $className): void
    {
        foreach (self::$validClasses as $validClass) {
            if (\is_a($className, $validClass, true)) {
                return;
            }
        }

        throw new SerializerException(\sprintf('Class \'%s\' must implements any of \'%s\' interfaces', $className, \implode(', ', self::$validClasses)));
    }

    /**
     * @param string $propertyName      Property for method
     * @param string $prefix            Method prefix
     * @param bool   $convertToSingular Whether convert property in plural form to singular
     */
    private function getMethodName(string $propertyName, $prefix = 'set', $convertToSingular = false): string
    {
        if ($convertToSingular) {
            $conversions = WordsConverter::conversions();
            if (\array_key_exists($propertyName, $conversions)) {
                $propertyName = $conversions[$propertyName];
            }
        }

        return \sprintf('%s%s', $prefix, \ucfirst($propertyName));
    }
}
