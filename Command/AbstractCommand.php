<?php

namespace Im\CommonBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Im\CommonBundle\Utils\Command,

    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends ContainerAwareCommand
{
    use Command;

    /**
     * Имя команды, которое будет выведено в консоли
     *
     * @var     string
     */
    protected $command_alias = '';

    /**
     * Объектное представление STDIN
     *
     * @var     Symfony\Component\Console\Output\InputInterface
     */
    protected $input;

    /**
     * Объектное представление STDOUT
     *
     * @var     Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Метод сохраняет объектные представления STDIN и STDOUT в свойствах объекта
     *
     * @param       InputInterface      $input          Объектное представление STDIN
     * @param       OutputInterface     $output         Объектное представление STDOUT
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input  = $input;
    }
}

?>
