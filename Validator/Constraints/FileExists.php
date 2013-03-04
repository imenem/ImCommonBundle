<?php

namespace Im\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FileExists extends Constraint
{
    /**
     * Сообщение об ошибке
     *
     * @var string
     */
    public $message = 'File not found on the path %path%';

    /**
     * Путь который будет добавлен перед проверяемым значением
     *
     * @var string
     */
    public $prefix = '';

    /**
     * Параметр контейнера в котором хранится путь,
     * который будет добавлен перед проверяемым значением
     *
     * @var string
     */
    public $prefix_parameter = '';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'im.validator.file_exists';
    }
}