<?php

namespace Im\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ConditionalNotBlank extends Constraint
{
    /**
     * Сообщение об ошибке
     *
     * @var string
     */
    public $message = 'This value should not be blank';

    /**
     * Список свойств, при наличии которые значение не должно быть пустым
     *
     * @var array
     */
    public $conditional_properties = [];

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'im.validator.conditional_not_blank';
    }
}