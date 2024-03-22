<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;

use Cl\Container\Exception\InvalidArgumentException;
use Countable;
use Traversable;

class PrioritizedContainer 
    implements 
        PrioritizedContainerInterface, 
        Countable
{
    /**
     * @var array<string, mixed> $container 
     *      Container, organized by tag and priority
     */
    protected array $container = [];

    /**
     * @var int 
     *  The unique value used as a key to map items 
     *      from a container to a priority map.
     *  This value is shared among all instances of this class 
     *      and is crucial for correct sorting and uniqueness when the items are received by handlers.
     */
    protected static $uniq = 999999;

    /**
     * Generate a unique id for accurate sorting by attachment order.
     *
     * @param int $priority The priority of the item.
     *
     * @return string The generated id.
     */
    protected function generateId(int $priority): string
    {
        return number_format($priority + --static::$uniq/1000000, 6);
    }

    /**
     * Attach an item to the container with a specified priority.
     *
     * Generates a id for accurate sorting by the order of attachment.
     *
     * @param mixed    $item     The item to attach to the container.
     * @param int|null $priority The priority of the item (default is 0).
     *
     * @return int|string The id identifier for the attached item.
     */
    public function attach(mixed $item, $priority = null): string
    {
        $priority = $priority ?? PrioritizedContainerInterface::DEFAULT_PRIORITY;
        
        /**
         * Generate id for accurate sorting by attach turn
         */

         $id = $this->generateId($priority);

        /**
         * Attach item
         */
        $this->container[$id] = $item;

        /**
         * Sort after attaching
         */
        krsort($this->container, SORT_NUMERIC);

        return $id;
    }

    /**
     * Detach item
     *
     * @param string $id
     * 
     * @return boolean
     */
    public function detach(string $id): bool
    {
        if ($this->has($id)) {
            unset($this->container[$id]);
            return true;
        }
        return false;
    }

    /**
     * Returns the items in the priority order
     * 
     * @return Traversable
     * @throws InvalidArgumentException
     */
    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            return $this->container[$id];
        }
        throw new InvalidArgumentException(sprintf(_('Item with id %s not found'), $id));
    }

    /**
     * Get multiple items
     *
     * @param array   $ids           The Ids
     * @param boolean $preserve_keys Keep original keys or not
     * 
     * @return Traversable
     * @throws InvalidArgumentException
     */
    public function getMultiple(array $ids = [], bool $preserve_keys = true): Traversable
    {
        $ids = !empty($ids) ? $ids : array_keys($this->container);
    
        foreach ($ids as $id ) {
            $item = $this->get($id);
            if ($preserve_keys) {
                yield $id => $item;
            } else {
                yield $item;
            }
        }
    }

    /**
     * Id indicates priority composition
     * 
     * @param bool $preserve_keys Keep original keys or not
     * 
     * @return void
     */
    public function getAll(bool $preserve_keys = true): Traversable
    {
        yield from $this->getMultiple([], $preserve_keys);
    }
    
    /**
     * 
     * 
     * @param bool $preserve_keys Keep original keys or not
     * 
     * @return void
     */
    public function getAllIds(): array
    {
        return array_keys($this->container);
    }

    /**
     * Check if an item exists in the container
     *
     * @param string $id The id (key)
     * 
     * @return bool True if the item exists, false otherwise
     */
    public function has(string $id): bool
    {
        return !empty($this->container[$id]);
    }

    /**
     * Counter
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->container);
    }

    /**
     * Reset
     *
     * @return void
     */
    public function reset(): void
    {
        /**
         * static::$uniq is not ressetable
         */
        $this->container = [];
    }

    /**
     * Returns the internal container representation.
     *
     * @return array<int|string, mixed>
     */
    public function getContainerRaw(): array
    {
        return $this->container;
    }

    /**
     * Returns the items in the priority order
     * 
     * {@inheritDoc}
     */
    public function getIterator(): Traversable
    {
        yield from $this->getMultiple();
    }
}