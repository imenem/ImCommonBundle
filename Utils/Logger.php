<?php

namespace Im\CommonBundle\Utils;

use Exception;

trait Logger
{
    /**
     * Метод возвращает логгер
     *
     * @return      Psr\Log\LoggerInterface
     */
    abstract protected function getLogger();

    /**
     * Метод логгирует исключение
     *
     * @param           \Exception          $e          Исключение, которое нужно логгировать
     * @param           array               $data       Дополнительные данные для логгирования
     */
    protected function logException(Exception $e, array $data = array())
    {
        $message = '[' . \get_class($this) . ']: ' .
                   \get_class($e) . ': ' . $e->getMessage() . ' : ' . implode(' ', $data);

        $this->getLogger()
             ->error($message);
    }
}