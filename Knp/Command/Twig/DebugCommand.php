<?php

namespace Knp\Command\Twig;

use Pimple\Container;
use Symfony\Bridge\Twig\Command\DebugCommand as BaseDebugCommand;

/**
 * Twig DebugCommand that can locate the twig environment in a Pimple container.
 */
class DebugCommand extends BaseDebugCommand
{
    private $container;

    /**
     * DebugCommand constructor.
     *
     * @param Container $container A Pimple container instance
     */
    public function __construct(Container $container)
    {
        parent::__construct();

        $this->container = $container;
    }
    
    /**
     * Set the default command name
     */
    protected function configure()
    {
        $this->setName('debug:twig');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function getTwigEnvironment()
    {
        if (null === $twig = parent::getTwigEnvironment()) {
            $this->setTwigEnvironment($twig = $this->container['twig']);
        }

        return $twig;
    }
}
