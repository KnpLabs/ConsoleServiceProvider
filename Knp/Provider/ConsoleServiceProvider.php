<?php

namespace Knp\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

use Knp\Console\Application as ConsoleApplication;
use Knp\Console\ConsoleEvents;
use Knp\Console\ConsoleEvent;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['console'] = function() use ($app) {
            $application = new ConsoleApplication(
                $app,
                $app['console.project_directory'],
                $app['console.name'],
                $app['console.version']
            );

            $app['dispatcher']->dispatch(ConsoleEvents::INIT, new ConsoleEvent($application));

            return $application;
        };
    }

    public function boot(Container $app)
    {
    }
}
