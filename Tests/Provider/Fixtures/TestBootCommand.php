<?php

namespace Knp\Tests\Provider\Fixtures;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestBootCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('test:boot')
            ->setDescription('Test boot command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TestBootApplication $silex */
        $silex = $this->getSilexApplication();

        $output->write($silex->isBooted() ? 'Booted' : 'Not booted');
    }
}
