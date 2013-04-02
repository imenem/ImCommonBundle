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
                   \get_class($e) . ': ' . $e->getMessage();

        if (!empty ($data))
        {
            $message .= ' : ';

            foreach ($data as $item)
            {
                if (is_array($item))
                {
                    $message .= print_r($item, true);
                }
                elseif (is_scalar($item))
                {
                    $message .= $item;
                }
                else
                {
                    throw new Exception('Can not cast ' . gettype($item) . ' to string');
                }

                $message .= ' : ';
            }
        }

        $this->getLogger()
             ->error($message);
    }
}