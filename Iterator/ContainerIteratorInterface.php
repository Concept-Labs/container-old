<?php
declare(strict_types=1);
namespace Cl\Container\Iterator;

use Cl\Able\Resettable\ResettableInterface;
use Cl\Container\ContainerInterface;
use IteratorAggregate;

interface ContainerIteratorInterface  extends ContainerInterface, IteratorAggregate, ResettableInterface
{
    function attach($item);
    function get(string $id): mixed;

    function has(string $id): bool;

    function getMultiple();
}