<?php

// :KLUDGE:     Imenem      04.03.13
//
// Данные классы необходимы для того, чтобы в конфигурации валидации
// не требовался ввод длинных пространств имен

namespace Symfony\Component\Validator\Constraints;

class UniqueEntity  extends \Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity {}
class FileExists    extends \Im\CommonBundle\Validator\Constraints\FileExists {}