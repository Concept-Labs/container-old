<?php
declare(strict_types=1);
namespace Cl\Container\Queue;

use SplPriorityQueue;

class PriorityQueue extends SplPriorityQueue
{
    protected $serial = PHP_INT_MAX;

    /**
     * Inserts an element into the priority queue with a stable priority.
     *
     * The documentation says "Note: Multiple elements with the same priority
     * will get dequeued in no particular order."
     * This implementation ensures stability by using an additional serial number.
     *
     * {@inheritDoc}
     */
    public function insert(mixed $value, mixed $priority): bool
    {
        return parent::insert($value, [$priority, $this->serial--]);
    }

    /**
    * {@inheritDoc}
     */
    public function compare($priority1, $priority2): int
    {
        return $priority1 <=> $priority2;
    }
}