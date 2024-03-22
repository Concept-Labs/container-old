<?php
declare(strict_types=1);
namespace Cl\Container;

use Cl\Container\ContainerInterface;
use Cl\Container\Exception\InvalidArgumentException;
use Exception;
use IteratorAggregate;
use Traversable;

class SimpleContainer implements ContainerInterface, IteratorAggregate
{
    /**
     * @var array The arrayable storage.
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param array $container The initial container
     * 
     */
    public function __construct(array $container = [])
    {
        $this->container = $container;
    }
    
    /**
     * Attach an item to the container with a specified priority.
     *
     * @param mixed $item The item to attach.
     * 
     * @return void
     */
    public function attach(mixed $item, $key = null): void 
    {
        $this->container[] = $item;
    }

    /**
     * Detatch the item
     *
     * @param mixed $item 
     * 
     * @return void 
     */
    public function detach(mixed $item): void
    {
        while (false !== $has = $this->has($item, true)) {
            unset($this->container[$has]);
        }
        $this->container = array_values($this->container);
    }

    /**
     * Check if the container has a specific item.
     *
     * @param mixed $item The item to check for.
     * 
     * @return mixed 
     */
    public function hasItem(mixed $item): mixed
    {
        return array_search($item, $this->container);
    }
    
    /**
     * Check if the container has a specific id.
     *
     * @param mixed $item The item to check for.
     * 
     * @return mixed 
     */
    public function has(mixed $id): mixed
    {
        return !empty($this->container[$id]);
    }


    /**
     * Get the iterator for traversing the container.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->container as $id => $item ) {
                yield $this->container[$id];
        }
    }
    /**
     * Gets the container items as iterator
     * 
     * @param string $id 
     *
     * @return mixed
     */
    public function get(string $id = null, $default = null): mixed
    {
        try {
            $id = (string)$id;
        } catch (Exception $e) {
            throw new InvalidArgumentException(_("Can not convert id to string"));
        }
        return $this->has($id) ? $this->container[$id] : $default;
    }

    /**
     * Ge the container
     *
     * @return void
     */
    public function getAll(bool $preserver_keys = false): iterable 
    {
        foreach ($this->container as $id => $item) {
            if (true == $preserver_keys) {
                yield $this->container[$id];
            } else {
                yield $id => $this->container[$id];
            }
        }
    }

    /**
     * Get the count of intems in container
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->container);
    }

    /**
     * Reset the container
     *
     * @return void
     */
    public function reset()
    {
        $this->container = [];
    }
}