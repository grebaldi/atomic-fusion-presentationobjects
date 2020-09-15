<?php declare(strict_types=1);
namespace PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Projection\Content\TraversableNodeInterface;

/**
 * @Flow\Proxy(false)
 */
final class Content implements ContentInterface
{
    /**
     * @var TraversableNodeInterface
     */
    private $contentNode;

    /**
     * @var SlotInterface
     */
    private $wrappedSlot;

    public function __construct(TraversableNodeInterface $contentNode, SlotInterface $wrappedSlot)
    {
        $this->contentNode = $contentNode;
        $this->wrappedSlot = $wrappedSlot;
    }

    /**
     * @return TraversableNodeInterface
     */
    public function getContentNode(): TraversableNodeInterface
    {
        return $this->contentNode;
    }

    /**
     * @return SlotInterface
     */
    public function getWrappedSlot(): SlotInterface
    {
        return $this->wrappedSlot;
    }

    /**
     * @return string
     */
    public function getPrototypeName(): string
    {
        return 'PackageFactory.AtomicFusion.PresentationObjects:Slot.Content';
    }
}
