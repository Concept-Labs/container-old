<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;

use Cl\DIContainer\DIContainerInterface;

class PrioritizedContainerFactory
{
    protected $diContainer = null;
    protected $instanceId = null;

    public function __construct(
        DIContainerInterface $diContainer/* @TODO not null */ = null,
        $instanceId = "Cl\Container\Iterator\Prioritized\PrioritizedContainer"
    ) {
        $this->diContainer = $diContainer;
        $this->instanceId = $instanceId;
    }

    /**
     * Get the instance
     * 
     * @param array ...$args 
     *
     * @return \Cl\Container\Iterator\Prioritized\PrioritizedContainer
     */
    public function create(...$args)
    {
        return ($this->factory($))();
    }

    public function factory()
    {
        //@TODO remove. keep container create method
        $instanceId = $this->instanceId;
        return static function () use ($instanceId) {
            return new ($instanceId)();
        }
        //return $this->diContainer->create($this->instanceId, ...$args);
    }
}