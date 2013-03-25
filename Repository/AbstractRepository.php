<?php

namespace Im\CommonBundle\Repository;

use Im\CommonBundle\Utils\Common,
    Im\CommonBundle\ORM\QueryBuilder,
    Doctrine\ORM\EntityRepository;

abstract class  AbstractRepository
extends         EntityRepository
{
    use Common
    {
        Common::getEntityName as getShortEntityName;
    }

    /**
     * Алиас для таблицы сущностей
     *
     * @var     string
     */
    protected $alias        = '';

    /**
     * Объект для порождения выражений
     *
     * @var     ExpressionBuilder
     */
    protected static $expression_builder;

    /**
     * Набор ламбда-функций для удобного доступа к выражениями Doctrine DQL
     *
     * @var     array
     */
    protected $expressions = [];

    /**
     * Список ассоциаций для "жадной" подгрузки
     *
     * @var     array
     */
    protected $eager = [];

    /**
     * Метод ищет сущности с именем, похожим на переданное в запросе.
     *
     * @param       string      $query      Поисковый запрос
     *
     * @return      array
     */
    public function search($query)
    {
        extract($this->getExpressions());

        return $this
            ->createSelect()
            ->andWhere($like($alias('name'), ':name'))
            ->setParameter('name', "%{$query}%")
            ->setEagerAssociations($this->getEntityName(), $this->getEager())
            ->getLazyResult();
    }

    /**
     * Метод возвращает общее кол-во сущностей
     *
     * @return      int
     */
    public function count()
    {
        return $this->createCount()->getSingleScalarResult();
    }

    /**
     * Метод возвращает список всех сущностей.
     * Ассоциации, указанные в свойстве eager выбираются запросами с условием WHERE IN ().
     *
     * @return array
     */
    public function findAll()
    {
        return $this->createSelect()
            ->setEagerAssociations($this->getEntityName(), $this->getEager())
            ->getLazyResult();
    }


    /**
     * Метод выполняет SELECT-запрос по заданным полям.
     *
     * @param       array       $criteria       Массив условий выборки
     * @param       array       $orderBy        Массив условий сортировки
     * @param       int         $limit          Максимальное кол-во результатов
     * @param       int         $offset         Смещение первого результата
     *
     * @return      Doctrine\ORM\Query          Сформированный запрос
     */
    public function findBy(array $criteria, array $orderBy = array(), $limit = null, $offset = null)
    {
        extract($this->getExpressions());

        $query_builder = $this->createSelect();

        foreach ($criteria as $column => $value)
        {
            if (is_array($value))
            {
                $query_builder->andWhere($in($alias($column), ':' . $column));
            }
            else
            {
                $query_builder->andWhere($eq($alias($column), ':' . $column));
            }

            $query_builder->setParameter($column, $value);
        }

        foreach ($orderBy as $column => $direction)
        {
            $query_builder->orderBy($alias($column), $direction);
        }

        $query_builder->setDataPortionRange($offset, $limit);
        $query_builder->setEagerAssociations($this->getEntityName(), $this->getEager());

        return $query_builder->getLazyResult();
    }

    /**
     * Метод создает новый SELECT-запрос
     * с указанием алиаса и сущности
     *
     * @param       array       $joins      Массив с алиасами дополнительных таблиц, которые необходимо выбрать
     *
     * @return      Doctrine\ORM\QueryBuilder   Конструктор для дальнейшего создания запроса
     */
    public function createSelect(array $joins = [])
    {
        array_unshift($joins, $this->getEntityAlias());

        return $this->getQueryBuilder()
                ->select(implode(', ', $joins));
    }

    /**
     * Метод создает новый SELECT-запрос для получения кол-ва строк
     *
     * @return      Doctrine\ORM\QueryBuilder   Конструктор для дальнейшего создания запроса
     */
    public function createCount()
    {
        extract($this->getExpressions());

        return $this->getQueryBuilder()
            ->select($count($alias('id')));
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata()
    {
        return $this->_class;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName()
    {
        return parent::getEntityName();
    }

    /**
     * Метод возвращает имя сущности, которую выводит контроллер.
     * При этом пробуются следующие варианты:
     * - Переданное в параметре имя
     * - Имя, заданное в свойстве entity
     * - Часть имени контроллера (при условии, что оно имеет формат Namespace\EntityNameController)
     *
     * @return      string                  Имя сущности
     *
     * @throws      RuntimeException        Имя сущности определить не удалось
     */
    public function getEntityAlias()
    {
        if (empty ($this->alias))
        {
            $this->alias = strtolower($this->getShortEntityName());
        }

        return $this->alias;
    }

    /**
     * Метод возвращает новый конструктор запроса
     *
     * @return      Im\CommonBundle\Repository\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return (new QueryBuilder($this->getEntityManager()))
            ->select($this->getEntityAlias())
            ->from($this->getEntityName(), $this->getEntityAlias());
    }

    /**
     * Метод возвращает набор лямбда-функций
     * для удобного доступа к выражениями Doctrine DQL.
     *
     * @example
     *
     * <code>
     * <?php
     *     $query_builder = $this->createSelect();
     *
     *     // импортируем все лямбда-функции в текущую область видимости
     *     extract($this->getExpressions());
     *
     *     $query_builder->where
     *     (
     *          // построим WHERE с помощью лямбда-функций
     *          $andx
     *          (
     *              $eq($alias('user'), ':user'),
     *              $eq($alias('type'), ':type'),
     *              $in($alias('status'), array(':active', ':disabled'))
     *          )
     *     );
     * ?>
     * </code>
     *
     * @return      array
     */
    final protected function getExpressions()
    {
        if (empty ($this->expressions))
        {
            $this->expressions['alias'] = function($field)
            {
                return $this->getEntityAlias() . '.' . $field;
            };

            $builder = $this->getEntityManager()->getExpressionBuilder();

            static $expressions =
            [
                /** Conditional objects **/
                'andx', 'orx',
                /** Comparison objects **/
                'eq', 'neq', 'lt', 'lte', 'gt', 'gte', 'isNull', 'isNotNull',
                /** Arithmetic objects **/
                'prod', 'diff', 'sum', 'quot',
                /** Pseudo-function objects **/
                'exists', 'all', 'some', 'any', 'not', 'in', 'notIn', 'like', 'between',
                /** Function objects **/
                'trim', 'concat', 'substr', 'lower', 'upper', 'length',
                'avg', 'max', 'min', 'abs', 'sqrt', 'count', 'countDistinct'
            ];

            foreach ($expressions as $method)
            {
                // :TRICKY:         Imenem          22.03.12
                //
                // Эта лямбда-функция вызывает одноименный метод ExpressionsBuilder,
                // передавая ему полученные параметры.
                $this->expressions[$method] = function() use ($builder, $method)
                {
                    return call_user_func_array([$builder, $method], func_get_args());
                };
            }
        }

        return $this->expressions;
    }

    /**
     * Метод возвращает список ассоциаций для "жадной" подгрузки
     *
     * @return     array
     */
    protected function getEager()
    {
        return $this->eager;
    }
}
