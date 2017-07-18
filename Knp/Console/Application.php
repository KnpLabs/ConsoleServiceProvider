<?php

namespace Knp\Console;

use Silex\Application as SilexApplication;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        if (isset($application['console.boot_in_constructor']) && $application['console.boot_in_constructor']) {
            @trigger_error('Booting the Silex application from the console constructor is deprecated and won\'t be possble in v3 of the console provider.', E_USER_DEPRECATED);
            $application->boot();
        }
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

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->getSilexApplication()->boot();

        return parent::doRun($input, $output);
    }
}
