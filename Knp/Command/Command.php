<?php

namespace Knp\Command;

use Silex\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Command that depends on the Silex application.
 *
 * @method \Knp\Console\Application getApplication();
 */
class Command extends BaseCommand
{
    /**
     * Gets the Silex application.
     *
     * @return Application
     */
    public function getSilexApplication()
    {
        return $this->getApplication()->getSilexApplication();
    }

    /**
     * Gets the project root directory.
     *
     * @return string
     */
    public function getProjectDirectory()
    {
        return $this->getApplication()->getProjectDirectory();
    }
}
