<?php

namespace Im\CommonBundle\Collection;

use Doctrine\ORM\QueryBuilder,
    Im\CommonBundle\ORM\Paginator,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection,
    Closure,
    OutOfRangeException;

/**
 * Collection, that allows query result lazy load slicing and batch lazy load iteration.
 *
 * @author  Artem Ponomarenko <imenem@inbox.ru>
 */
class LazyCollection implements Collection
{
    /**
     * Doctrine ORM query builder
     *
     * @var Doctrine\ORM\QueryBuilder
     */
    protected $query_builder;

    /**
     * Doctrine query paginator
     *
     * @var \Doctrine\ORM\Tools\Pagination\Paginator
     */
    protected $paginator;

    /**
     * Query result cache
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $result;

    /**
     * Chunk size for portion iterator
     *
     * @var int
     */
    protected $batch_size;

    /**
     * Saved Query first result and max results count to object property
     *
     * @var array
     */
    protected $saved_range = [0, null];

    /**
     * Initializes a new LazyCollection.
     *
     * @param \Doctrine\ORM\QueryBuilder        $query_builder      Doctrine ORM query builder.
     */
    public function __construct(QueryBuilder $query_builder, $batch_size = 100, $fetchJoinCollection = null)
    {
        $this->query_builder    = $query_builder;
        $this->paginator        = new Paginator($this->getQueryBuilder(), $fetchJoinCollection);
        $this->batch_size       = $batch_size;
    }

    /**
     * {@inheritdoc}
     */
    public function slice($offset, $length = null)
    {
        $this->saveQueryRange();

        try
        {
            $this->applySliceRange($offset, $length);

            $collection = $this->getCollection();
        }
        catch (OutOfRangeException $e)
        {
            $collection = new ArrayCollection;
        }

        $this->restoreQueryRange();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new PortionIterator([$this, 'slice'], $this->batch_size);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->paginator->count();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->getCollection()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->getCollection()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function last()
    {
        return $this->getCollection()->last();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->getCollection()->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->getCollection()->last();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->getCollection()->current();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->getCollection()->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($element)
    {
        return $this->getCollection()->removeElement($element);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->getCollection()->offsetExists($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getCollection()->offsetGet($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        return $this->getCollection()->offsetSet($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        return $this->getCollection()->offsetUnset($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function containsKey($key)
    {
        return $this->getCollection()->containsKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element)
    {
        return $this->getCollection()->contains($element);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Closure $p)
    {
        return $this->getCollection()->exists($p);
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf($element)
    {
        return $this->getCollection()->indexOf($element);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return $this->getCollection()->getKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->getCollection()->getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return $this->getCollection()->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($value)
    {
        return $this->getCollection()->add($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->getCollection()->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function map(Closure $func)
    {
        return $this->getCollection()->map($func);
    }

    /**
     * {@inheritdoc}
     */
    public function reduce(Closure $callback, $initial = null)
    {
        return $this->getCollection()->reduce($callback, $initial);
    }

    /**
     * {@inheritdoc}
     */
    public function unique($sort_flags = null)
    {
        return $this->getCollection()->unique($sort_flags);
    }

    /**
     * {@inheritdoc}
     */
    public function groupBy(Closure $callback)
    {
        return $this->getCollection()->groupBy($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Closure $p)
    {
        return $this->getCollection()->filter($p);
    }

    /**
     * {@inheritdoc}
     */
    public function forAll(Closure $p)
    {
        return $this->getCollection()->forAll($p);
    }

    /**
     * {@inheritdoc}
     */
    public function partition(Closure $p)
    {
        return $this->getCollection()->partition($p);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->getCollection()->clear();
    }

    /**
     * Returns collection Query
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQuery()
    {
        return $this->paginator->getQuery();
    }

    /**
     * Returns collection Query builder
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryBuilder()
    {
        return $this->query_builder;
    }

    /**
     * Returns Query result as ArrayCollection
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getCollection()
    {
        if (empty ($this->result))
        {
            $result = $this->paginator->getIterator()->getArrayCopy();
            $this->result = new ArrayCollection($result);
        }

        return $this->result;
    }

    /**
     * Applies slice range to query considering Query first result and max results count.
     *
     * @param       int     $offset         Slice offset
     * @param       int     $length         Slice length
     *
     * @throws OutOfRangeException
     */
    protected function applySliceRange($offset, $length)
    {
        list($old_offset, $old_length) = $this->saved_range;

        $new_offset = $old_offset + $offset;
        $max_offset = $old_offset + ($old_length ?: $this->count());

        if ($new_offset >= $max_offset)
        {
            throw new OutOfRangeException;
        }

        // last slice can be smallest then $length
        if (($new_offset + $length) >= $max_offset)
        {
            $new_length = $max_offset - $new_offset;
        }
        else
        {
            $new_length = $length;
        }

        $this->setQueryRange($new_offset, $new_length);
    }

    /**
     * Applies Query first result and max results count.
     *
     * @param       int     $offset         Query first result index
     * @param       int     $length         Query max results count
     */
    protected function setQueryRange($offset, $length = null)
    {
        $this->paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);
    }

    /**
     * Saves Query first result and max results count to object property
     */
    protected function saveQueryRange()
    {
        $query = $this->paginator->getQuery();

        $this->saved_range = [$query->getFirstResult(), $query->getMaxResults()];
    }

    /**
     * Restores Query first result and max results count from object property
     */
    protected function restoreQueryRange()
    {
        list($offset, $length) = $this->saved_range;

        $this->setQueryRange($offset, $length);
    }
}
