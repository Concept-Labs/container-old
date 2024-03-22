<?php
namespace Cl\Container\ArrayPath;

use ArrayAccess;
use Countable;
use Iterator;
use SeekableIterator;
use Serializable;
use Traversable;

interface ArrayPathInterface extends SeekableIterator, Traversable, Iterator, ArrayAccess, Serializable, Countable
{
    
}