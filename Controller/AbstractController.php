<?php


namespace Im\CommonBundle\Controller;

use Im\CommonBundle\Entity\AbstractEntity,
    Im\CommonBundle\Utils\Controller,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * Abstract controller.
 */
abstract class  AbstractController extends BaseController
{
    use Controller;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        AbstractEntity::setEntityManager($this->getEntityManager());
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
