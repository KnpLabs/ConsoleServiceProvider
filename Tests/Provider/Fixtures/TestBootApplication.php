<?php

namespace Knp\Tests\Provider\Fixtures;

use Silex\Application;

/**
 * Silex application that lets us check boot status.
 */
class TestBootApplication extends Application
{
    public function isBooted()
    {
        return $this->booted;
    }
}
