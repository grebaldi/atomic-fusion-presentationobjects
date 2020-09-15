<?php declare(strict_types=1);
namespace PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package.
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\PresentationObjects\Fusion\ComponentPresentationObjectInterface;

/**
 * @Flow\Proxy(false)
 */
final class Value implements ValueInterface
{
    /**
     * @var mixed
     */
    private $wrappedValue;

    /**
     * @param mixed $wrappedValue
     */
    public function __construct($wrappedValue)
    {
        $this->wrappedValue = $wrappedValue;
    }

    /**
     * @return string
     */
    public function getPrototypeName(): string
    {
        return 'PackageFactory.AtomicFusion.PresentationObjects:Value';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::convertToString($this->wrappedValue);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function convertToString($value): string
    {
        if (is_null($value)) {
            return '';
        } elseif (is_scalar($value)) {
            return (string) $value;
        } elseif (is_resource($value)) {
            return '[resource]';
        } elseif (is_array($value)) {
            return join('', array_map([self::class, 'convertToString'], $value));
        } elseif (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        } elseif (is_object($value)) {
            return '[' . get_class($value) . ']';
        } elseif (is_callable($value)) {
            return '[callable]';
        } else {
            return '[unknown type]';
        }
    }
}
