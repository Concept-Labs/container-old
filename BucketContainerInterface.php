<?php
declare(strict_types=1);
namespace Cl\Container;

use Cl\Able\Resettable\ResettableInterface;
use Countable;
use IteratorAggregate;

interface BucketContainerInterface extends Countable, IteratorAggregate, ResettableInterface
{
    function attach(ContainerInterface $item, string $section): void;
    function has(string $section): bool;
    function get(string $section): ContainerInterface;
}