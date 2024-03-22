<?php
declare(strict_types=1);
namespace Cl\Container;

use Cl\Container\Arrayable\Exception\SectionNotFoundException;
use Cl\Container\ContainerInterface;
use IteratorAggregate;
use Traversable;


/**
 * Class BucketContainer
 * 
 * Implementation of the BucketContainerInterface.
 * Represents a container that can hold other containers identified by sections.
 */
class BucketContainer implements BucketContainerInterface, IteratorAggregate
{
    /**
     * @var ContainerInterface[] An associative array to store containers with sections as keys.
     */
    protected array $bucket = [];

    /**
     * @var int The total count of items across all containers.
     */
    protected int $count = 0;

    /**
     * @var bool A flag indicating whether the count has been computed and cached.
     */
    protected bool $counted = false;

    /**
     * Attaches a container to the specified section.
     *
     * @param string             $section   The section to attach the container to.
     * @param ContainerInterface $container The container to attach.
     * 
     * @return void
     */
    public function attach(ContainerInterface $container, string $section): void
    {
        $this->bucket[$section] = $container;
        $this->counted = false;
    }

    /**
     * Checks if a container exists in the specified section.
     *
     * @param string $section The section to check.
     * 
     * @return bool True if the container exists, false otherwise.
     */
    public function has(string $section): bool
    {
        return !empty($this->bucket[$section]);
    }

    /**
     * Gets the container in the specified section.
     *
     * @param string $section The section to get the container from.
     * 
     * @return ContainerInterface The container in the specified section.
     * 
     * @throws SectionNotFoundException If the section is not found.
     */
    public function get(string $section): ContainerInterface
    {
        if (!$this->has($section)) {
            throw new SectionNotFoundException(sprintf(_("Bucket section '%s' not found"), $section));
        }
        return $this->bucket[$section];
    }

    /**
     * Removes a container from the specified section.
     *
     * @param string $section The section to remove the container from.
     * 
     * @return bool True if the removal was successful, false otherwise.
     */
    public function remove(string $section): bool
    {
        if (!$this->has($section)) {
            throw new SectionNotFoundException(sprintf(_("Bucket section '%s' not found"), $section));
        }
        unset($this->bucket[$section]);
        return true;
    }

    /**
     * Clears all containers from the bucket.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->reset();
    }

    /**
     * Gets the total count of items across all containers.
     *
     * @return int The total count of items.
     */
    public function count(): int
    {
        if (false === $this->counted) {
            $this->count = 0;
            foreach ($this->bucket as $container) {
                $this->count += $container->count();
            }
            $this->counted = true;
        }
        return $this->count;
    }

    /**
     * Resets the bucket by clearing all containers and resetting counts.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->bucket = [];
        $this->count = 0;
        $this->counted = false;
    }

    /**
     * Gets an iterator for traversing all containers in the bucket.
     *
     * @return Traversable The iterator.
     */
    public function getIterator(): Traversable
    {
        foreach ($this->bucket as $container) {
            yield $container;
        }
    }

}