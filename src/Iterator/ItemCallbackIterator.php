<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Iterator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Iterator;

/**
 * @package    Zalt
 * @subpackage Iterator
 * @since      Class available since version 1.0
 */
class ItemCallbackIterator implements \OuterIterator, \Countable
{
    /**
     *
     * @var callable
     */
    private $_callback;

    /**
     *
     * @var \Iterator
     */
    private $_iterator;

    /**
     *
     * @param \Traversable $iterator
     * @param Callable $callback
     */
    public function __construct(\Traversable $iterator, callable $callback)
    {
        while ($iterator instanceof \IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }
        if ($iterator instanceof \Iterator) {
            $this->_iterator = $iterator;
        } else {
            $iterator = new \ArrayObject($iterator);
            $this->_iterator = $iterator->getIterator();
        }

        $this->_callback = $callback;
    }

    /**
     * Count elements of an object
     *
     * Rewinding version of count
     *
     * @return int
     */
    public function count(): int
    {
        if ($this->_iterator instanceof \Countable) {
            return $this->_iterator->count();
        }

        $count = iterator_count($this->_iterator);
        $this->_iterator->rewind();
        return $count;
    }

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return call_user_func($this->_callback, $this->_iterator->current());
    }

    /**
     * Returns the inner iterator for the current entry.
     *
     * @return \Iterator
     */
    public function getInnerIterator(): ?\Iterator
    {
        return $this->_iterator;
    }

    /**
     * Return the key of the current element
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return $this->_iterator->key();
    }

    /**
     * Move forward to next element
     */
    public function next(): void
    {
        $this->_iterator->next();
    }

    /**
     * Rewind the \Iterator to the first element
     */
    public function rewind(): void
    {
        $this->_iterator->rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid(): bool
    {
        return $this->_iterator->valid();
    }
}
