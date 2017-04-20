<?php

namespace Knp\Console;

use Symfony\Component\EventDispatcher\Event;

/**
 * Lets you access the console application.
 */
class ConsoleEvent extends Event
{
    private $application;

    /**
     * ConsoleEvent constructor.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Gets the console application.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}
