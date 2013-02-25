<?php

namespace Im\CommonBundle\Utils;

use LogicException,
    RuntimeException;

trait Controller
{
    use Common;

    /**
     * Метод возвращает контейнер с сервисами
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * Метод возвращает текущий запрос
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    abstract protected function getRequest();

    /**
     * Метод возвращает сервис по его ID
     *
     * @return  object  Сервис
     */
    abstract protected function get($key);

    /**
     * Метод возвращает True, если сервис с переданным ID есть в контейнере
     *
     * @return boolean
     */
    abstract protected function has($key);

    /**
     * Метод возвращает менеджер сущностей Doctrine.
     *
     * @return      Doctrine\ORM\EntityManager      Менеджер сущностей
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()
                    ->getManager();
    }

    /**
     * Метод возвращает реестр Doctrine.
     *
     * @return Doctrine\Bundle\DoctrineBundle\Registry          Реестр Doctrine
     *
     * @throws LogicException                                   Пакет Doctrine не доступен
     */
    public function getDoctrine()
    {
        if (!$this->getContainer()->has('doctrine'))
        {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->getContainer()->get('doctrine');
    }

    /**
     * Метод возвращает все сущности, которые выводит контроллер
     *
     * @return  array
     */
    protected function findAll()
    {
        return $this->getRepo()->findAll();
    }

    /**
     * Метод возвращает сущность по ее ID.
     *
     * @param       int     $id         ID сущности
     *
     * @return      Entity
     *
     * @throws      Symfony\Component\HttpKernel\Exception\NotFoundHttpException        Сущность не найдена
     */
    protected function findOne($id)
    {
        $entity = $this->getRepo()->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException();
        }

        return $entity;
    }

    /**
     * Метод рендерит переданные данные.
     * Если имена шаблона, сущности и пакета не переданы,
     * то они определяются автоматически на основе
     * текущего экшена, имени и пространства имен контроллера.
     *
     * @param       array       $data       Данные для заполнения шаблона
     * @param       string      $view       Имя шаблона
     * @param       string      $entity     Имя сущности, для которой создан шаблон
     * @param       string      $bundle     Имя пакета, в котором находится сущность
     *
     * @return      \Symfony\Component\HttpFoundation\Response
     */
    protected function renderData(array $data, $view = '', $entity = '', $bundle = '')
    {
        $template = "{$this->getBundleName($bundle)}:{$this->getEntityName($entity)}:{$this->getActionName($view)}.html.twig";

        return $this->render($template, $data);
    }

    /**
     * Метод возвращает текущий экшен
     *
     * @param       string      $action     Имя экшена
     *
     * @return      string                  Имя экшена
     */
    protected function getActionName($action = '')
    {
        if (!empty ($action))
        {
            return $action;
        }

        $result = [];

        // Найдем в имени метода контроллера имя экшена
        preg_match('#(?<=::)\w+(?=Action)#i', $this->getRequest()->get('_controller'), $result);

        if (empty ($result))
        {
            throw new RuntimeException('No action name specified');
        }

        return $result[0];
    }

    /**
     * Метод возвращает пейджер
     *
     * @param       Doctrine\Common\Collections\Collection          $data       Данные для пейджинга
     *
     * @return      Knp\Component\Pager\Pagination\PaginationInterface          Пейджер
     */
    protected function getPager($data, $per_page = 10)
    {
        $page  = $this->get('request')->query->get('page', 1);

        return $this->get('knp_paginator')
                    ->paginate($data, $page, $per_page);
    }

    /**
     * Метод устанавливает flash-сообщение с произвольным названием
     *
     * @param       string      $key            Название flash-сообщения
     * @param       string      $message        Текст сообщения
     */
    protected function addFlash($key, $message)
    {
        $this->get('session')
             ->getFlashBag()
             ->add($key, $message);
    }

    /**
     * Метод устанавливает flash-сообщение,
     * которое просто выводит текст
     *
     * @param       string      $message        Текст сообщения
     */
    protected function addFlashInfo($message)
    {
        $this->get('session')
             ->getFlashBag()
             ->add('info', $message);
    }

    /**
     * Метод устанавливает flash-сообщение,
     * которое сообщает об успешном выполнении
     *
     * @param       string      $message        Текст сообщения
     */
    protected function addFlashSuccess($message)
    {
        $this->get('session')
             ->getFlashBag()
             ->add('success', $message);
    }

    /**
     * Метод устанавливает flash-сообщение,
     * которое сообщает о предупреждении
     *
     * @param       string      $message        Текст сообщения
     */
    protected function addFlashWarning($message)
    {
        $this->get('session')
             ->getFlashBag()
             ->add('warning', $message);
    }

    /**
     * Метод устанавливает flash-сообщение,
     * которое сообщает об ошибке
     *
     * @param       string      $message        Текст сообщения
     */
    protected function addFlashError($message)
    {
        $this->get('session')
             ->getFlashBag()
             ->add('error', $message);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     *
     * @api
     */
    public function getParameter($name)
    {
        return $this->getContainer()->getParameter($name);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return Boolean The presence of parameter in container
     *
     * @api
     */
    public function hasParameter($name)
    {
        return $this->getContainer()->hasParameter($name);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @api
     */
    public function setParameter($name, $value)
    {
        return $this->getContainer()->setParameter($name, $value);
    }
}
