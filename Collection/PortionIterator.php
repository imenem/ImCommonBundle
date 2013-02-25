<?php

namespace Im\CommonBundle\Collection;

use ArrayIterator as DataIterator,
    Iterator,
    IteratorAggregate;

/**
 * Класс позволяет выполнять итерацию по большим спискам данных
 * с помощью порций определенного размера. В конструктор класса должно быть передано замыкание,
 * которое позволяет получить порцию данных, и принимает два параметра -
 * индекс первого элемента порции и кол-во элементов в порции.
 * Замыкание не должно бросать исключение, если по указанным параметрам не обнаружено данных.
 */
class           PortionIterator
    implements  Iterator
{
    /**
     * Замыкание, которое позволяет получить порцию данных.
     * Замыкание должно принимать в качестве параметров
     * индекс первого элемента порции и кол-во элементов в порции,
     * и возвращать массив с порцией данных.
     *
     * @var     callable
     */
    protected $data_loader;

    /**
     * Размер порции данных.
     *
     * @var     int
     */
    protected $portion_size     = 100;

    /**
     * Текущая порция данных.
     *
     * @var     \Iterator
     */
    protected $data_portion;

    /**
     * Порядковый номер текущей порции данных.
     *
     * @var     int
     */
    protected $portion_number   = 0;

    /**
     * Конструктор класса
     *
     * @param       callable        $data_loader            Замыкание, которое позволяет получить порцию данных.
     * @param       int             $portion_size           Размер порции данных.
     */
    public function __construct(callable $data_loader, $portion_size = null)
    {
        $this->data_loader = $data_loader;

        if ((int) $portion_size > 0)
        {
            $this->portion_size = (int) $portion_size;
        }

        $this->rewind();
    }

    /**
     * Метод возвращает текущий элемент итератора.
     *
     * @return      mixed
     */
    public function current()
    {
        return $this->data_portion->current();
    }

    /**
     * Метод возвращает ключ текущего элемента итератора.
     *
     * @return      scalar
     */
    public function key()
    {
        return $this->data_portion->key();
    }

    /**
     * Метод сдвигает указатель итератора на один элемент вперед.
     */
    public function next()
    {
        $this->data_portion->next();
    }

    /**
     * Метод устанавливает указатель итератора на первый элемент.
     */
    public function rewind()
    {
        $this->portion_number   = 0;
        $this->data_portion     = new DataIterator;
    }

    /**
     * Метод проверяет, вышел указатель за пределы списка данных или нет.
     *
     * @return      bool        Истина, если указатель в пределах списка данных,
     *                          ложь, если нет
     */
    public function valid()
    {
        if ($this->data_portion->valid() === false)
        {
            $this->loadDataPortion();
        }

        return $this->data_portion->valid();
    }

    /**
     * Метод загружает следующую порцию данных, используя загрузчик, полученный в конструкторе.
     * Если был установлен фильтр для данных, то порция будет подвергнута фильтрации с его помощью.
     * Если после фильтрации порция окажется пустой, то будет получена следующая порция данных.
     */
    protected function loadDataPortion()
    {
        $offset = $this->portion_number * $this->portion_size;
        $limit  = $this->portion_size;

        // получим порцию данных и создадим для нее итератор
        $data_portion = call_user_func($this->data_loader, $offset, $limit);

        // если получение данных прошло успешно, увеличим номер порции
        ++$this->portion_number;

        if ($data_portion instanceof Iterator)
        {
            $this->data_portion = $data_portion;
        }
        elseif ($data_portion instanceof IteratorAggregate)
        {
            $this->data_portion = $data_portion->getIterator();
        }
        else
        {
            $this->data_portion = new DataIterator($data_portion);
        }
    }
}
