<?php

namespace Im\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator,
    Symfony\Component\Validator\Constraint,
    Symfony\Component\DependencyInjection\ContainerInterface,
    RuntimeException;

class FileExistsValidator extends ConstraintValidator
{
    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $path = '';

        // добавим префикс из описания валидации
        if (!empty ($constraint->prefix))
        {
            $path .= $constraint->prefix . DIRECTORY_SEPARATOR;
        }

        // добавим префикс из настроек
        if (!empty ($constraint->prefix_parameter))
        {
            if (!$this->container->hasParameter($constraint->prefix_parameter))
            {
                throw new RuntimeException("Parameter '{$constraint->prefix_parameter}' for path prefix not found");
            }

            $path .= $this->container->getParameter($constraint->prefix_parameter) . DIRECTORY_SEPARATOR;
        }

        $path .= $value;

        if (!file_exists($path))
        {
            $this->context->addViolation($constraint->message, ['%path%' => $path]);
        }
    }
}
