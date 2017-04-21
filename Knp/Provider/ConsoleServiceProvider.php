<?php

namespace Knp\Provider;

use Knp\Console\Application as ConsoleApplication;
use Knp\Console\ConsoleEvent;
use Knp\Console\ConsoleEvents;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Symfony Console service provider for Silex.
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the service provider.
     *
     * @param Container $app The Pimple container
     */
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

            if ($app['dispatcher']->hasListeners(ConsoleEvents::INIT)) {
                @trigger_error('Listening to the Knp\Console\ConsoleEvents::INIT event is deprecated and will be removed in v3 of the service provider. You should extend the console service instead.', E_USER_DEPRECATED);

                $app['dispatcher']->dispatch(ConsoleEvents::INIT, new ConsoleEvent($console));
            }

            return $console;
        };
    }
}
