<?php

namespace Cl\Container\ArrayPath;

trait ArrayPathPropertyTrait
{

    protected string $path = '';
    protected ArrayPathInterface|null $parentInstance = null;
    protected string $separator = self::PATH_DEFAULT_SEPARATOR;
    protected int $flags = 0;

    /**
     * Get the path for the current instance.
     *
     * @return string
     */
    public function getPath(): string
    {
        //@TODO do not add dot at beginnig of children paths
        return ltrim($this->path, '.');
    }
    
    /**
     * Set the path for the current instance.
     * 
     * @param string $path The path
     *
     * @return ArrayPathInterface
     */
    public function setPath(string $path): ArrayPathInterface
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Check the parent instance.
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return !is_null($this->parentInstance);
    }

    /**
     * Get the parent instance.
     *
     * @return ArrayPathInterface|null
     */
    public function getParent(): ArrayPathInterface|null
    {
        return $this->parentInstance;
    }
    
    /**
     * Set the parent instance.
     * 
     * @param $parentInstance The parent instance
     *
     * @return ArrayPathInterface|null
     */
    public function setParent(ArrayPathInterface $parentInstance): ArrayPathInterface|null
    {
        $this->parentInstance = $parentInstance;

        return $this;
    }

    /**
     * Get the path separator.
     *
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }
    
    /**
     * Set the path separator.
     *
     * @param string $separator The separator
     * 
     * @return string
     */
    public function setSeparator(string $separator): string
    {
        return $this->separator = $separator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlags(): int
    {
        return parent::getFlags();
    }


}