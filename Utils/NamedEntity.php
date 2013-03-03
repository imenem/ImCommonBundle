<?php

namespace Im\CommonBundle\Utils;

trait NamedEntity
{
    abstract public function getName();

    public function __toString()
    {
        return (string) $this->getName();
    }
}