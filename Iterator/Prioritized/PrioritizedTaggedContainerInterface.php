<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;


interface PrioritizedTaggedContainerInterface extends PrioritizedContainerInterface
//ContainerIteratorInterface
{

    public function attach(mixed $item, $tags = [], ?int $priority = null): string;

    public function getMultiple(array $tags = []): iterable;
}
