<?php

namespace Knp\Console;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Knp\Console\Application;

class Event extends BaseEvent
{
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }
}