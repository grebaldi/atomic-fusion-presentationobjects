<?php declare(strict_types=1);
namespace PackageFactory\AtomicFusion\PresentationObjects\Domain\Enum;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\ComponentName;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\FileWriterInterface;

/**
 * The enum generator domain service
 *
 * @Flow\Proxy(false)
 */
final class EnumGenerator
{
    protected \DateTimeImmutable $now;

    private FileWriterInterface $fileWriter;

    public function __construct(?\DateTimeImmutable $now = null, FileWriterInterface $fileWriter)
    {
        $this->now = $now ?? new \DateTimeImmutable();
        $this->fileWriter = $fileWriter;
    }

    /**
     * @param ComponentName $componentName
     * @param string $name
     * @param string $type
     * @param array|string[] $values
     * @param string $packagePath
     * @return void
     */
    public function generateEnum(
        ComponentName $componentName,
        string $name,
        string $type,
        array $values,
        string $packagePath
    ): void {
        $enumType = EnumType::fromInput($type);
        $enumName = new EnumName(
            $componentName,
            $name
        );
        $enum = new Enum($enumName, $enumType, $enumType->processValueArray($values));

        $this->fileWriter->writeFile($enumName->getClassPath($packagePath), $enum->getClassContent());
        $this->fileWriter->writeFile($enumName->getExceptionPath($packagePath), $enum->getExceptionContent($this->now));
        $this->fileWriter->writeFile($enumName->getProviderPath($packagePath), $enum->getProviderContent());
    }
}
