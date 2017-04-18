<?php

namespace Knp\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Knp\Console\Application as ConsoleApplication;
use Knp\Console\ConsoleEvents;
use Knp\Console\ConsoleEvent;

/**
 * Symfony Console service provider for Silex.
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['console.name'] = 'Silex console';
        $app['console.version'] = 'UNKNOWN';
        // Assume we are in vendor/knplabs/console-service-provider/Knp/Provider
        $app['console.project_directory'] = __DIR__.'/../../../../..';

        $app['console'] = function () use ($app) {
            $console = new ConsoleApplication(
                $app,
                $app['console.project_directory'],
                $app['console.name'],
                $app['console.version']
            );
            $console->setDispatcher($app['dispatcher']);

            $app['dispatcher']->dispatch(ConsoleEvents::INIT, new ConsoleEvent($console));

            return $console;
        };
    }
}
