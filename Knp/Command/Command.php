<?php

namespace Knp\Command;

use Knp\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Class Command
 * @package Knp\Command
 */
class Command extends BaseCommand
{
    /**
     * @return Application;
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    /**
     * @param Application|null $application
     */
    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);
    }

    /**
     * @return \Silex\Application
     */
    public function getSilexApplication()
    {
        return $this->getApplication()->getSilexApplication();
    }

    /**
     * @return string
     */
    public function getProjectDirectory()
    {
        return $this->getApplication()->getProjectDirectory();
    }
}
