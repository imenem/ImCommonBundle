<?php

namespace Im\CommonBundle\Utils;

trait Command
{
    use Common;

    /**
     * Метод возвращает истину, если сервис с переданным ID зарегистрирован
     *
     * @param       string          $id         ID сервиса
     *
     * @return                                  Истина, если сервис зарегистрирован, ложь - если нет
     */
    public function has($id)
    {
        return $this->getContainer()
                    ->has($id);
    }

    /**
     * Метод возвращает сервис по его ID
     *
     * @param       string          $id         ID сервиса
     *
     * @return      object                      Сервис
     */
    public function get($id)
    {
        return $this->getContainer()
                    ->get($id);
    }

    /**
     * Метод возвращает менеджер сущностей Doctrine.
     *
     * @return      Doctrine\ORM\EntityManager      Менеджер сущностей
     */
    public function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }


    /**
     * Метод выводит в STDOUT сообщение, если команда запущена с ключом --verbose
     *
     * @param       string              $message        Сообщение, которое необходимо вывести
     */
    protected function displayInfo($message)
    {
        if ($this->isVerbose())
        {
            $this->output->writeln('<info>' . $this->createMessageForDisplay($message) . '</info>');
        }
    }

    /**
     * Метод выводит в STDOUT сообщение
     *
     * @param       string              $message        Сообщение, которое необходимо вывести
     */
    protected function displayComment($message)
    {
        $this->output->writeln('<comment>' . $this->createMessageForDisplay($message) . '</comment>');
    }

    /**
     * Метод выводит в STDOUT сообщение об исключении
     *
     * @param       string              $message        Сообщение, которое необходимо вывести
     */
    protected function displayException(\Exception $e)
    {
        $this->getApplication()->renderException($e, $this->output);
    }

    /**
     * Метод возвращает истину, если команда запущена с ключом --verbose
     *
     * @return      bool                                Истина, если команда запущена с ключом --verbose,
     *                                                  ложь - если нет
     */
    protected function isVerbose()
    {
        return ($this->output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * Метод добавляет к сообщению дату и краткое название команды
     *
     * @param       string      $message        Исходное сообщение
     *
     * @return      string                      Подготовленное сообщение
     */
    protected function createMessageForDisplay($message)
    {
        return $this->getDateTimeForDisplay() . ' ' . $this->command_alias . ': ' . $message;
    }

    /**
     * Метод возвращает отформатированную текущую дату
     *
     * @return      string
     */
    protected function getDateTimeForDisplay()
    {
        $date = new \DateTime;

        return $date->format('[Y-m-d H:i:s]');
    }
}

