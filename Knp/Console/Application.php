<?php

namespace Knp\Console;

use Silex\Application as SilexApplication;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Silex console application.
 */
class Application extends BaseApplication
{
    private $silexApplication;
    private $projectDirectory;

    /**
     * Application constructor.
     *
     * @param SilexApplication $application      The Silex application
     * @param string           $projectDirectory The project root directory
     * @param string           $name             The name of the console application
     * @param string           $version          The version of the console application
     */
    public function __construct(SilexApplication $application, $projectDirectory, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->silexApplication = $application;
        $this->projectDirectory = $projectDirectory;

        $application->boot();
    }

    /**
     * Gets the Silex application.
     *
     * @return SilexApplication
     */
    public function getSilexApplication()
    {
        return $this->silexApplication;
    }

    /**
     * Gets the project root directory.
     *
     * @return string
     */
    public function getProjectDirectory()
    {
        return $this->projectDirectory;
    }
}
