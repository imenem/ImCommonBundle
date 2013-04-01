<?php

namespace Im\CommonBundle\Utils;

trait Flash
{
    /**
     * Метод возвращает сессию
     *
     * @return Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    abstract protected function getSession();

    /**
     * Метод устанавливает flash-сообщение с произвольным названием
     *
     * @param       string      $key            Название flash-сообщения
     * @param       string      $message        Текст сообщения
     */
    protected function addFlash($key, $message)
    {
        $this->getSession()
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
        $this->getSession()
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
        $this->getSession()
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
        $this->getSession()
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
        $this->getSession()
             ->getFlashBag()
             ->add('error', $message);
    }
}