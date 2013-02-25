<?php

namespace Im\CommonBundle\Utils;

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Persistence\ObjectManager,
    Faker\Factory as FakerFactory;

trait DataFixture
{
    use Common;

    /**
     * Фабрика для получения случайных данных в определенном формате
     *
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * Менеджер для работы с сущностями
     *
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * Метод возвращает фабрику для получения случайных данных в определенном формате
     *
     * @return Faker\Generator
     */
    protected function getFaker()
    {
        if (empty($this->faker))
        {
            $this->faker = FakerFactory::create();
        }

        return $this->faker;
    }

    /**
     * Метод устанавливает менеджер сущностей
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    protected function setObjectManager(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    protected function getEntityManager()
    {
        return $this->manager;
    }

    /**
     * Метод возвращает массив всех сущностей переданного класса
     *
     * @param       string      $class      Имя класса (без пространства имен)
     *
     * @return      array                   Все сущности класса
     */
    protected function getEntities($class)
    {
        static $cache = [];

        if (empty($cache[$class]))
        {
            $entities = $this->getRepo($class)->findAll();

            if ($entities instanceof Collection)
            {
                $cache[$class] = $entities->toArray();
            }
            else
            {
                $cache[$class] = (array) $entities;
            }
        }

        return $cache[$class];
    }

    /**
     * Метод возвращает замыкание, которое возвращает случайный элемент переданного массива
     *
     * <code>
     * <?php
     * $tracks        = $this->getEntities('Genre');
     * $bitrates      = [96, 128, 192, 256, 320, 512];
     * $rand_bitrate  = $this->getRandomizer($bitrates);
     *
     * foreach ($tracks as $track)
     * {
     *     $track->setBitrate($rand_bitrate());
     * }
     * ?>
     * </code>
     *
     * @param       array       $array      Массив, случайное значение которого необходимо получить
     *
     * @return      callable                Замыкание, возвращающее случайный элемент
     */
    protected function getRandomizer(array $array)
    {
        return function() use ($array)
        {
            return $array[array_rand($array)];
        };
    }
}
