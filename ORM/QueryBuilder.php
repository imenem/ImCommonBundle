<?php

namespace Im\CommonBundle\ORM;

use Im\CommonBundle\Collection\LazyCollection,
    Doctrine\ORM\QueryBuilder as BaseBuilder,
    Doctrine\ORM\Mapping\ClassMetadata;

class QueryBuilder extends BaseBuilder
{
    /**
     * Список ассоциаций для "жадной" загрузки.
     *
     * @var     array
     */
    protected $eager = [];

    /**
     * Метод добавляет границы порции данных к конструктору запроса
     *
     * @param       int                         $offset                 Индекс первого элемента, который должен попасть в выборку
     * @param       int                         $limit                  Максимальное кол-во элементов в выборке
     *
     * @return      PaynetEasy\LkrecurBundle\Repository\QueryBuilder    Конструктор запроса с добавленными границами
     */
    public function setDataPortionRange($offset = null, $limit = null)
    {
        if (!is_null($offset))
        {
            $this->setFirstResult($offset);
        }

        if (!is_null($limit))
        {
            $this->setMaxResults($limit);
        }

        return $this;
    }

    /**
     * Метод позволяет задать ассоциации, которые будут подгружены сразу же,
     * с помощью запросов с условием WHERE IN ().
     *
     * @param       string      $entity             Имя класса сущности
     * @param       array       $associations       Список ассоциаций
     *
     * @return      Doctrine\ORM\Query
     */
    public function setEagerAssociations($entity, array $associations)
    {
        $this->eager[$entity] = $associations;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        $query = parent::getQuery();

        foreach ($this->eager as $entity => $associations)
        {
            foreach ($associations as $association)
            {
                $query->setFetchMode($entity, $association, ClassMetadata::FETCH_EAGER);
            }
        }

        return $query;
    }

    /**
     * Метод возвращает результат запроса в виде коллекции с ленивой загрузкой.
     *
     * @return \Im\CommonBundle\Repository\LazyCollection
     */
    public function getLazyResult()
    {
        return new LazyCollection($this);
    }

    /**
     * Gets the single scalar result of the query.
     *
     * Alias for getSingleResult(HYDRATE_SINGLE_SCALAR).
     *
     * @return mixed
     *
     * @throws QueryException If the query result is not unique.
     */
    public function getSingleScalarResult()
    {
        return $this->getQuery()->getSingleScalarResult();
    }

    /**
     * Gets the single result of the query.
     *
     * Enforces the presence as well as the uniqueness of the result.
     *
     * If the result is not unique, a NonUniqueResultException is thrown.
     * If there is no result, a NoResultException is thrown.
     *
     * @param   integer     $hydrationMode
     *
     * @return  mixed
     *
     * @throws  Doctrine\ORM\NonUniqueResultException    If the query result is not unique.
     * @throws  Doctrine\ORM\NoResultException           If the query returned no result.
     */
    public function getSingleResult($hydrationMode = null)
    {
        return $this->getQuery()->getSingleResult($hydrationMode);
    }
}
