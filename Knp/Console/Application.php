<?php

namespace Knp\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Silex\Application as SilexApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    private $silexApplication;

    private $projectDirectory;

    public function __construct(SilexApplication $application, $projectDirectory, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->silexApplication = $application;
        $this->projectDirectory = $projectDirectory;
    }

    public function getSilexApplication()
    {
        return $this->silexApplication;
    }

    public function getProjectDirectory()
    {
        return $this->projectDirectory;
    }
    
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->silexApplication->boot();
        parent::run($input, $output);
    }
}
