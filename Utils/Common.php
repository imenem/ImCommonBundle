<?php

namespace Im\CommonBundle\Utils;

use RuntimeException;

trait Common
{
    /**
     * Имя пакета, к которому относится класс
     *
     * @var     string
     */
    protected $bundle        = '';

    /**
     * Типы объектов, для которых возможен поиск имени сущности в имени класса.
     *
     * @var     string
     */
    protected $object_types  = 'Controller|Repository|Fixture';

    /**
     * Имя сущности, с которой работает объект
     *
     * @var     string
     */
    protected $entity = '';

    /**
     * Метод логгирует исключение
     *
     * @param           \Exception          $e          Исключение, которое нужно логгировать
     */
    protected function logException(\Exception $e)
    {
        $message = '[' . \get_class($this) . ']: ' .
                   \get_class($e) . ': ' . $e->getMessage();

        $this->get('logger')
             ->error($message);
    }

    /**
     * Метод возвращает репозиторий сущностей.
     * Если имя репозитория не передано в параметре метода,
     * то берется имя репозитория по умолчанию из свойства класса.
     *
     * @param       string              $entity             Имя сущности
     *
     * @return      Doctrine\ORM\EntityRepository           Репозиторий
     *
     * @throws      \RuntimeException                       Бросается, если имя репозитория не найдено
     */
    protected function getRepo($entity = '', $bundle = '')
    {
        return $this->getEntityManager()
                    ->getRepository("{$this->getBundleName($bundle)}:{$this->getEntityName($entity)}");
    }

    /**
     * Метод возвращает менеджер сущностей Doctrine.
     *
     * @return      Doctrine\ORM\EntityManager      Менеджер сущностей
     */
    abstract protected function getEntityManager();

    /**
     * Метод возвращает имя сущности, которую выводит контроллер.
     * При этом пробуются следующие варианты:
     * - Переданное в параметре имя
     * - Имя, заданное в свойстве entity
     * - Часть имени контроллера (при условии, что оно имеет формат Namespace\EntityNameController)
     *
     * @param       string      $entity     Имя сущности (опционально)
     *
     * @return      string                  Имя сущности
     *
     * @throws      RuntimeException        Имя сущности определить не удалось
     */
    protected function getEntityName($entity = '')
    {
        if (!empty ($entity))
        {
            return $entity;
        }

        if (empty($this->entity))
        {
            $result = [];

            // Найдем в полном имени класса имя сущности, которую он выводит
            preg_match('#(?<=\\\\)\w+(?=' . $this->object_types . ')#i', get_called_class(), $result);

            if (empty ($result))
            {
                throw new RuntimeException('No repository name specified');
            }

            $this->entity = $result[0];
        }

        return $this->entity;
    }

    /**
     * Метод возвращает имя пакета, к которому относится объект
     *
     * @return      string
     */
    protected function getBundleName($bundle = '')
    {
        if (!empty ($bundle))
        {
            return $bundle;
        }

        if (empty($this->bundle))
        {
            $class_name = str_replace(['\\Bundle\\', '\\'], '', get_called_class());

            $result = [];

            // Найдем в полном имени класса название пакета
            preg_match('#^.+Bundle#', $class_name, $result);

            if (empty ($result))
            {
                throw new RuntimeException('No bundle name specified');
            }

            $this->bundle = $result[0];
        }

        return $this->bundle;
    }

    /**
     * Метод сохраняет переданную сущность.
     *
     * @param       object      $entity     Сущность с данными, которую нужно сохранить
     */
    protected function saveEntity($entity)
    {
        $em = $this->getEntityManager();

        $em->persist($entity);
        $em->flush();
    }

    /**
     * Метод сохраняет все измененные ранее сущности в БД
     */
    protected function saveEntities()
    {
        $this->getEntityManager()
             ->flush();
    }
}