<?php declare(strict_types=1);
namespace PackageFactory\AtomicFusion\PresentationObjects\Domain\Component;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;

/**
 * The repository for all supported prop types
 *
 * @Flow\Scope("singleton")
 */
final class PropTypeRepository implements PropTypeRepositoryInterface
{
    /**
     * @param null|string $packageKey
     * @param null|string $componentName
     * @param string $type
     * @return null|PropType
     */
    public function findByType(?string $packageKey, ?string $componentName, string $type): ?PropType
    {
        if ($packageKey === null || $componentName === null) {
            return null;
        }

        if (!$this->knowsByType($packageKey, $componentName, $type)) {
            return null;
        }

        return PropType::create($packageKey, $componentName, $type, $this);
    }

    /**
     * @param string $packageKey
     * @param string $componentName
     * @param string $type
     * @return null|PropTypeIdentifier
     */
    public function findPropTypeIdentifier(string $packageKey, string $componentName, string $type): ?PropTypeIdentifier
    {
        if (!$this->knowsByType($packageKey, $componentName, $type)) {
            return null;
        }

        $nullable = false;
        if (\mb_strpos($type, '?') === 0) {
            $nullable = true;
            $type = \mb_substr($type, 1);
        }

        if ($this->knowsPrimitive($type)) {
            return new PropTypeIdentifier($type, $type, $type, $nullable, PropTypeClass::primitive());
        }

        if ($this->knowsGlobalValue($type)) {
            $className = PropType::$globalValues[$type];
            return new PropTypeIdentifier($this->getSimpleClassName($className), $this->getSimpleClassName($className), $className, $nullable, PropTypeClass::globalValue());
        }

        if ($this->knowsValue($packageKey, $componentName, $type)) {
            $className = $this->getEnumClassName($packageKey, $componentName, $type);
            return new PropTypeIdentifier($this->getSimpleClassName($className), $this->getSimpleClassName($className), $className, $nullable, PropTypeClass::value());
        }

        if ($this->knowsComponent($packageKey, $type)) {
            $interfaceName = $this->getComponentInterfaceName($packageKey, $type);
            return new PropTypeIdentifier(
                $type,
                $this->getSimpleClassName($interfaceName),
                $interfaceName,
                $nullable,
                PropTypeClass::component()
            );
        }

        if (\mb_strpos($type, 'array<') === 0 && \mb_substr($type, -1, 1) === '>') {
            if ($this->knowsGeneric($packageKey, $type)) {
                $genericName = $this->getGenericClassName($packageKey, $type);
                return new PropTypeIdentifier(
                    \mb_substr($type, 6, \mb_strlen($type) - 7),
                    $this->getSimpleClassName($genericName),
                    $genericName,
                    false,
                    PropTypeClass::generic()
                );
            }
        }

        return null;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getSimpleClassName(string $className): string
    {
        return \mb_substr($className, \mb_strrpos($className, '\\') + 1);
    }

    /**
     * @param string $packageKey
     * @param string $componentName
     * @param string $type
     * @return boolean
     */
    public function knowsByType(string $packageKey, string $componentName, string $type): bool
    {
        $type = trim($type, '?');

        return $this->knowsPrimitive($type)
            || $this->knowsGlobalValue($type)
            || $this->knowsValue($packageKey, $componentName, $type)
            || $this->knowsComponent($packageKey, $type)
            || $this->knowsGeneric($packageKey, $type);
    }

    /**
     * @param string $type
     * @return boolean
     */
    private function knowsPrimitive(string $type): bool
    {
        return isset(PropType::$primitives[$type]);
    }

    /**
     * @param string $type
     * @return boolean
     */
    private function knowsGlobalValue(string $type): bool
    {
        return isset(PropType::$globalValues[$type]);
    }

    /**
     * @param string $packageKey
     * @param string $componentName
     * @param string $type
     * @return boolean
     */
    private function knowsValue(string $packageKey, string $componentName, string $type): bool
    {
        return class_exists($this->getEnumClassName($packageKey, $componentName, $type));
    }

    /**
     * @param string $packageKey
     * @param string $componentName
     * @param string $type
     * @return string
     */
    private function getEnumClassName(string $packageKey, string $componentName, string $type): string
    {
        return \str_replace('.', '\\', $packageKey)
        . '\Presentation\\' . $componentName . '\\' . $type;
    }

    /**
     * @param string $packageKey
     * @param string $type
     * @return boolean
     */
    private function knowsComponent(string $packageKey, string $type): bool
    {
        return interface_exists($this->getComponentInterfaceName($packageKey, $type));
    }

    /**
     * @param string $packageKey
     * @param string $type
     * @phpstan-return class-string
     * @return string
     */
    private function getComponentInterfaceName(string $packageKey, string $type): string
    {
        /** @phpstan-var class-string $interfaceName */
        $interfaceName =  \str_replace('.', '\\', $packageKey)
            . '\Presentation\\' . $type . '\\' . $type . 'Interface';

        return $interfaceName;
    }

    private function knowsGeneric(string $packageKey, string $type): bool
    {
        return class_exists($this->getGenericClassName($packageKey, $type));
    }

    /**
     * @param string $packageKey
     * @param string $type
     * @phpstan-return class-string
     * @return string
     */
    private function getGenericClassName(string $packageKey, string $type): string
    {
        $type = \mb_substr($type, 6, \mb_strlen($type) - 7);
        /** @phpstan-var class-string $className */
        $className =  \str_replace('.', '\\', $packageKey)
            . '\Presentation\\' . $type . '\\' . PluralName::forName($type);

        return $className;
    }
}
