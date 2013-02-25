<?php

namespace Im\CommonBundle\Utils;

trait Entity
{
    use Common;

    /**
     * Метод позволяет получить коллекцию ассоциированных сущностей
     * в виде коллекции с ленивой загрузкой. При попытке доступа к
     * коллекции сущности будут подгружены вместе с теми своими ассоциациями,
     * которые отмечены для жадной загрузки.
     *
     * @param       string      $assoc          Имя поля с коллекцией
     * @param       array       $order_by       Массив условий сортировки
     *
     * @return      \Im\CommonBundle\Collection\LazyCollection
     */
    protected function getLazyAssoc($assoc, array $order_by = array())
    {
        $property = 'lazy_' . $assoc;

        if (!isset ($this->$property))
        {
            $em = $this->getEntityManager();

            $class_metadata = $em->getRepository(get_called_class())
                                 ->getClassMetadata();

            $target_class = $class_metadata->getAssociationTargetClass($assoc);
            $mapped_by    = $class_metadata->getAssociationMappedByTargetField($assoc);

            $this->$property = $em->getRepository($target_class)
                                  ->findBy([$mapped_by => $this], $order_by);
        }

        return $this->$property;
    }
}
