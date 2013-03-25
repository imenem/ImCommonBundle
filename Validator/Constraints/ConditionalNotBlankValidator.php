<?php

namespace Im\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator,
    Symfony\Component\Validator\Constraint,
    Symfony\Component\DependencyInjection\ContainerInterface;

class ConditionalNotBlankValidator extends ConstraintValidator
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

        $value;
        $constraint;
    }
}
