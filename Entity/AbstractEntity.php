<?php

namespace Im\CommonBundle\Entity;

use Im\CommonBundle\Utils\Entity,
    Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractEntity
{
    use Entity;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    static protected $entity_manager;

    /**
     * Метод устанавливает менеджер сущностей
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $entity_manager
     */
    static public function setEntityManager(ObjectManager $entity_manager)
    {
        static::$entity_manager = $entity_manager;
    }

    /**
     * Метод возвращает менеджер сущностей
     *
     * @return      \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getEntityManager()
    {
        return static::$entity_manager;
    }
}