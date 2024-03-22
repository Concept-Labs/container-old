<?php

namespace Cl\Container\ArrayPath;

use Cl\Container\ArrayPath\Exception\InvalidPathException;

trait ArrayPathTrait
{
    /**
     * Get the value at the specified offset or at the specified path.
     *
     * @param mixed $key 
     * 
     * @return mixed
     * @throws InvalidPathException
     */
    public function offsetGet(mixed $key): mixed
    {
        $result = match (true) {
            parent::offsetExists($key) => parent::offsetGet($key),
            default => $this->pathGet((string)$key),
        };
        
        return 
            is_array($result) 
            ? $this->getChild($result, sprintf("%s%s%s", $this->getPath(), $this->getSeparator(), $key))
            : $result; // Return found flat value
    }
    
    /**
     * Set the value at the specified offset or at the specified path.
     *
     * @param mixed $key 
     * @param mixed $value 
     * 
     * @return void
     * @throws InvalidPathException
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        match (true) {
            $this->offsetExists($key) => parent::offsetSet($key, $value),
            default => $this->pathSet($key, $value),//If the offset not found than
        };

        //Set the value for the parent instance the so whole config is updated
        $this->getParent()?->offsetSet(sprintf("%s.%s", $this->getPath(), $key), $value);
    }

    /**
     * Unset the value at the specified offset or at the specified path.
     *
     * @param mixed $key 
     * @param mixed $value 
     * 
     * @return void
     * @throws InvalidPathException
     */
    public function offsetUnset(mixed $key): void
    {
        match (true) {
            $this->offsetExists($key) => parent::offsetUnset($key),
            default => $this->pathUnset($key),
        };

        //Unset the value for the parent instance the so whole config is updated
        $this->getParent()?->offsetUnset(sprintf("%s.%s", $this->getPath(), $key));
    }
    
    /**
     * Get the value at the specified path.
     *
     * @param string $path 
     *
     * @return mixed
     * @throws InvalidPathException
     */
    public function pathGet(string $path) : mixed
    {
        return array_reduce(// Lookup by the path
            $this->splitPath($path), 
            function ($reference, $key) use ($path) {
                return match (true) {
                    !is_array($reference) || !key_exists($key, $reference) => throw new InvalidPathException(sprintf('Given path "%s" not found', $path)),
                    default => $reference[$key],
                };
            },
            $this->getArrayCopy()
        );
    }

    /**
     * Set the value at the specified path.
     *
     * @param string $path 
     * @param mixed  $value 
     *
     * @throws InvalidPathException
     * @return void
     */
    public function pathSet(string $path, mixed $value) : void
    {
        $origArray = $this->getArrayCopy();
        $reference = &$origArray;

        foreach ($this->splitPath($path) as $key) {// Lookup by the path
            match (true) {
                !is_array($reference) || !key_exists($key, $reference) => throw new InvalidPathException(sprintf('Given path "%s" not found', $path)),
                default => $reference = &$reference[$key],
            };
        };

        $reference = $value;// Set the value
        $this->setStorageArray($origArray);// Update the internal storage
    }
}