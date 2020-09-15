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
final class Envelope implements EnvelopeInterface
{
    /**
     * @var ComponentPresentationObjectInterface
     */
    private $presentationObject;

    /**
     * @var string
     */
    private $targetPrototypeName;

    /**
     * @param ComponentPresentationObjectInterface $presentationObject
     * @param string $targetPrototypeName
     */
    public function __construct(
        ComponentPresentationObjectInterface $presentationObject,
        string $targetPrototypeName
    ) {
        $this->presentationObject = $presentationObject;
        $this->targetPrototypeName = $targetPrototypeName;
    }

    /**
     * @return ComponentPresentationObjectInterface
     */
    public function getPresentationObject(): ComponentPresentationObjectInterface
    {
        return $this->presentationObject;
    }

    /**
     * @return string
     */
    public function getTargetPrototypeName(): string
    {
        return $this->targetPrototypeName;
    }

    /**
     * @return string
     */
    public function getPrototypeName(): string
    {
        return 'PackageFactory.AtomicFusion.PresentationObjects:Envelope';
    }
}
