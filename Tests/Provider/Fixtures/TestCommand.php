<?php

namespace Knp\Tests\Provider\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('test:test')
            ->setDescription('Test command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Test command');
    }
}
