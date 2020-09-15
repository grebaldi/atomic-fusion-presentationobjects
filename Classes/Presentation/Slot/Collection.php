<?php declare(strict_types=1);
namespace PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package.
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class Collection implements CollectionInterface
{
    /**
     * @var array|SlotInterface[]
     */
    private $items;

    /**
     * @param SlotInterface ...$items
     */
    public function __construct(SlotInterface ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return array|SlotInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getPrototypeName(): string
    {
        return 'PackageFactory.AtomicFusion.PresentationObjects:Collection';
    }
}
