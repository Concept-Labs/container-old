<?php
namespace Cl\Container\ArrayPath;

class ArrayPath extends \ArrayIterator implements ArrayPathInterface
{
    use ArrayPathPropertyTrait;
    use ArrayPathSplitterTrait;
    use ArrayPathTrait;
    
    /**
     * Default path separator.
     */
    const PATH_DEFAULT_SEPARATOR = ".";

    /**
     * ArrayPath constructor.
     *
     * @param array $data  
     * @param int   $flags 
     */
    public function __construct(array $data, int $flags = 0)
    {
        parent::__construct($data, $flags);
    }

    /**
     * Create the children instance
     *
     * @param array  $data 
     * @param string $path The path  
     * 
     * @return ArrayPathInterface
     */
    public function getChild(array $data, string $path): ArrayPathInterface
    {
        $childInstance = new static($data, $this->getFlags());
        $childInstance
            ->setPath($path)
            ->setParent($this)
            ->setSeparator($this->getSeparator());

        return $childInstance;
    }

    /**
     * Set the internal array for the current instance.
     *
     * @param array $array 
     * 
     * @return void
     */
    protected function setStorageArray(array $array) : void
    {
        // Update the internal storage
        // \ArrayIterator::__construct()
        parent::__construct($array, $this->getFlags());
    }
}