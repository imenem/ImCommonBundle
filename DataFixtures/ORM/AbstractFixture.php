<?php

namespace Im\CommonBundle\DataFixtures\ORM;

use Im\CommonBundle\Utils\DataFixture,
    Doctrine\Common\DataFixtures\AbstractFixture as BaseFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Base data fixture.
 */
abstract class  AbstractFixture
extends         BaseFixture
implements      OrderedFixtureInterface
{
    use DataFixture;
}
