<?php

namespace Im\CommonBundle\Utils;

use Symfony\Component\Process\ExecutableFinder,
    Symfony\Component\Process\Process as SubProcess,
    RuntimeException;

trait Process
{
    use Logger;

    protected $executable_finder;

    /**
     * Метод выполняет переданную команду в консоли
     *
     * @param       strign      $executable         Программа для выполнения команды
     * @param       string      $arguments          Строка с аргументами команды
     * @param       string      $working_dir        Рабочая директория
     * @param       string      $error_message      Сообщение для исключения, которое будет брошено,
     *                                              если выполнение команды не удалось
     *
     * @return      string                          Результат выполнения команды
     *
     * @throws      \RuntimeException               Команда завершена с ошибкой
     */
    protected function executeProcess($executable, $arguments, $working_dir = null, $error_message = '')
    {
        $bin_path = $this->getExecutableFinder()->find($executable, $executable);

        $process = new SubProcess(escapeshellcmd("$bin_path $arguments"), $working_dir);

        $process->run();

        $this->checkProcessState($process, $error_message);

        return $process->getOutput();
    }

    /**
     * Метод проверяет, завершена команда успешно или нет
     *
     * @param       \Symfony\Component\Process\Process      $process            Объект процесса команды
     * @param       string                                  $error_message      Сообщение для исключения, которое будет брошено,
     *                                                                          если выполнение команды не удалось
     *
     * @throws      \RuntimeException       Команда завершена с ошибкой
     */
    protected function checkProcessState(SubProcess $process, $error_message = '')
    {
        if (!$process->isSuccessful())
        {
            $e = new RuntimeException($error_message);

            $this->logException($e, [$process->getCommandLine(), $process->getErrorOutput()]);

            throw $e;
        }
    }

    /**
     * Метод возвращает сервис для поиска исполныемых файлов.
     *
     * @return     \Symfony\Component\Process\ExecutableFinder
     */
    protected function getExecutableFinder()
    {
        if (!is_object($this->executable_finder))
        {
            $this->executable_finder = new ExecutableFinder;
        }

        return $this->executable_finder;
    }
}