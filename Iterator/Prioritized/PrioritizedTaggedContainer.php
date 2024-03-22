<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;

use Cl\Container\Iterator\ContainerIteratorInterface;
use Cl\EventDispatcher\ListenerProvider\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

use Traversable;
use ArrayIterator;

class PrioritizedTaggedContainer extends PrioritizedContainer 
    implements PrioritizedTaggedContainerInterface
{

    
    protected array $map = [];

    /**
     * @var integer An items counter
     */
    protected $counter = 0;

    protected ?CacheInterface $cache = null;
    protected ?LoggerInterface $logger = null;

    public function __construct(?CacheInterface $cache = null, ?LoggerInterface $logger = null)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * Attach an item to the container
     *
     * @param mixed    $item     The item to attach
     * @param string[] $tags     The tags of the item. Tags arry must be not empty. 
     *                           Declaration without type because parent class compability.
     * @param int      $priority The priority of the item
     *
     * @return void
     */
    public function attach(mixed $item, $tags = [], ?int $priority = null): string
    {
        $priority = $priority ?? PrioritizedContainerInterface::DEFAULT_PRIORITY;
        /**
         * Validate tags
         */
        $this->assertTags($tags);

        /**
         * Attach the item
         */
        $id = parent::attach($item, $priority);
        
        /**
         * Map to tags
         */
        foreach ($tags as $tag) {
            if (!$this->hasTag($tag)) {
                /**
                 * Create a container for the tag
                 */
                $this->map[$tag] = [];    
            }
            $this->map[$tag][$id] = $id;
        }
        $this->counter++;
        return $id;
    }


    /**
     * Check if container has tag
     *
     * @param string $tag
     * 
     * @return boolean
     */
    public function hasTag(string $tag): bool
    {
        return array_key_exists($tag, $this->map);
    }

    /**
     * Get Multiple items for given tags
     * If the tag is null than get items for all tags
     * 
     * @return iterable<string|int, mixed>
     * @throws InvalidArgumentException
     */
    public function getMultiple(array $tags = [], bool $preserve_keys = true): Traversable
    {
        
        if (empty($tags)) {
            /**
             * Use all known tags
             */
            $tags = $this->getTags();
        }

        $this->assertTags($tags);

        $ids = [];
        foreach ($tags as $tag) {
            if ($this->hasTag($tag)) {
                /**
                 * Keys are sumof priorities  and the unique value. See @see static::uniq.
                 */
                $ids += $this->map[$tag];
            }
        }

        /**
         * Ids was received from couple of tags so must be resorted by priority
         */
        krsort($ids, SORT_NUMERIC);

        /**
         * Remove duplicated ids.
         * Duplicate appears because same items can be mapped to different tags
         * Iem with higher priority will be kept
         */
        //$ids = array_unique($ids);

        /**
         * Loop throw found ids
         */
        foreach ($ids as $id) {
            /**
             *  Get the item
             */
            $item = $this->get((string)$id);
            /**
             * Preserve key equals priority or not
             */
            match ($preserve_keys) {
                true => yield $id => $item,
                default => yield $item,
            };
        }

    }

    /**
     * Get Multiple items from the container grouped by tags
     * If the tag array is empty than get All items from the container
     * The return iterable after converting to array will looks:
     * 
     * array<string, array<mixed>> :
     * 
     * array [
     *  'tag1' => [...items],
     *  ...
     * ]
     * 
     * An items inside each "tag" section are sorted 
     * by priority and by attached turn if same priority
     * 
     * @param array<string> $tags          The tags
     * @param bool          $preserve_keys 
     * 
     * @return array<string, array<mixed>>
     * @throws InvalidArgumentException
     */
    public function getMultipleGrouped(array $tags = [], bool $preserve_keys = true): array
    {
        if (empty($tags)) {
            /**
             * Use all known tags
             */
            $tags = $this->getTags();
        }

        $this->assertTags($tags);

        $grouped = [];
        
        foreach ($tags as $tag) {

            $grouped[$tag] = [];

            foreach ($this->map[$tag] as $id) {

                $item = $this->get((string)$id);

                $grouped[$tag][$id] = $item;
            }
            krsort($grouped[$tag]);
            if (!$preserve_keys) {
                $grouped[$tag] = array_values($grouped[$tag]);
            }
        };

        return $grouped;
    }

    public function detach(string $id): bool
    {
        //@TODO
        return true;
    }

    public function detachTags(array $tags): bool
    {
        foreach ($tags as $tag) {
            if ($this->hasTag($tag)) {
                unset($this->map[$tag]);
            }
        }

        //Check items in cotainer kept in map
        foreach ($this->container as $id => $item) {
            //@TODO
        }

        return true;
    }

    /**
     * Get all attached tags
     *
     * @return array
     */
    public function getTags(): array
    {
        return array_keys($this->map);
    }

    /**
     * Reset
     *
     * @return void
     */
    public function reset(): void
    {
        parent::reset();
        $this->map = [];
    }

    /**
     * Check the tags are sting
     *
     * @param array $tags 
     * 
     * @return void
     * @throws InvalidArgumentException if a tags contains not string
     */
    protected function assertTags(&$tags = []): void
    {

        if (!is_array($tags) || empty($tags) 
            || count(
                array_filter($tags, fn ($value) => is_string($value) || ($value instanceof \Stringable))
            ) !== count($tags)
        ) {
            throw new InvalidArgumentException(_("Tags array must not empty and must be strings"));
        }
    }

     /**
     * @see getMultiple()
     *
     * @return \Generator
     */
    public function getIterator(): Traversable
    {
        yield from $this->getMultiple([]);
    }

}