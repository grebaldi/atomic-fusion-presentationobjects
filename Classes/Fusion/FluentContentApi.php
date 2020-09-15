<?php declare(strict_types=1);
namespace PackageFactory\AtomicFusion\PresentationObjects\Fusion;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\NodeType\NodeTypeConstraintFactory;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Projection\Content\TraversableNodeInterface;
use Neos\Neos\Service\ContentElementEditableService;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\Content;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\ContentInterface;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\SlotInterface;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\Value;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\ValueInterface;

/**
 * @Flow\Proxy(false)
 */
final class FluentContentApi
{
    /**
     * @var TraversableNodeInterface
     */
    private $node;

    /**
     * @var ContentElementEditableService
     */
    protected $contentElementEditableService;

    /**
     * @var NodeTypeConstraintFactory
     */
    protected $nodeTypeConstraintFactory;

    /**
     * @param TraversableNodeInterface $node
     * @param ContentElementEditableService $contentElementEditableService
     * @param NodeTypeConstraintFactory $nodeTypeConstraintFactory
     */
    public function __construct(
        TraversableNodeInterface $node,
        ContentElementEditableService $contentElementEditableService,
        NodeTypeConstraintFactory $nodeTypeConstraintFactory
    ) {
        $this->node = $node;
        $this->contentElementEditableService = $contentElementEditableService;
        $this->nodeTypeConstraintFactory = $nodeTypeConstraintFactory;
    }

    /**
     * @param SlotInterface $wrappedSlot
     * @return ContentInterface
     */
    public function element(SlotInterface $wrappedSlot): ContentInterface
    {
        return new Content($this->node, $wrappedSlot);
    }

    /**
     * @param string $propertyName
     * @return ValueInterface
     */
    public function block(string $propertyName): ValueInterface
    {
        /** @var NodeInterface $node */
        $node = $this->node;

        return new Value(
            $this->contentElementEditableService->wrapContentProperty(
                $node,
                $propertyName,
                '<div>' . $node->getProperty($propertyName) . '</div>'
            )
        );
    }

    /**
     * @param string $propertyName
     * @return ValueInterface
     */
    public function inline(string $propertyName): ValueInterface
    {
        /** @var NodeInterface $node */
        $node = $this->node;

        return new Value(
            $this->contentElementEditableService->wrapContentProperty(
                $node,
                $propertyName,
                $node->getProperty($propertyName)
            )
        );
    }

    /**
     * @param string $nodeTypeFilterString
     * @return \Iterator<mixed, FluentContentApi>
     */
    public function children(string $nodeTypeFilterString): \Iterator
    {
        foreach ($this->node->findChildNodes(
            $this->nodeTypeConstraintFactory->parseFilterString($nodeTypeFilterString)
        ) as $node) {
            yield new self(
                $node,
                $this->contentElementEditableService,
                $this->nodeTypeConstraintFactory
            );
        }
    }

    /**
     * @return boolean
     */
    public function inBackend(): bool
    {
        /** @var NodeInterface $node */
        $node = $this->node;
        return $node->getContext()->inBackend();
    }
}
