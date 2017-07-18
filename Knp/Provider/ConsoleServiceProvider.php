<?php

namespace Knp\Provider;

use Knp\Command\Twig\DebugCommand;
use Knp\Command\Twig\LintCommand;
use Knp\Console\Application as ConsoleApplication;
use Knp\Console\ConsoleEvent;
use Knp\Console\ConsoleEvents;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Bridge\Twig\Command\DebugCommand as TwigBridgeDebugCommand;
use Symfony\Component\Yaml\Command\LintCommand as LintYamlCommand;

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
        $app['console.class'] = ConsoleApplication::class;
        // List of command service ids indexed by command name (i.e: array('my:command' => 'my.command.service.id'))
        $app['console.command.ids'] = [];

        // Maintain BC with projects that depend on the old behavior (application gets booted from console constructor)
        $app['console.boot_in_constructor'] = false;

        $app['console'] = function () use ($app) {
            /** @var ConsoleApplication $console */
            $console = new $app['console.class'](
                $app,
                $app['console.project_directory'],
                $app['console.name'],
                $app['console.version']
            );
            $console->setDispatcher($app['dispatcher']);

            foreach ($app['console.command.ids'] as $id) {
                $console->add($app[$id]);
            }

            if ($app['dispatcher']->hasListeners(ConsoleEvents::INIT)) {
                @trigger_error('Listening to the Knp\Console\ConsoleEvents::INIT event is deprecated and will be removed in v3 of the service provider. You should extend the console service instead.', E_USER_DEPRECATED);

                $app['dispatcher']->dispatch(ConsoleEvents::INIT, new ConsoleEvent($console));
            }

            return $console;
        };

        $commands = [];

        if (isset($app['twig']) && class_exists(TwigBridgeDebugCommand::class)) {
            $app['console.command.twig.debug'] = function (Container $container) {
                return new DebugCommand($container);
            };

            $app['console.command.twig.lint'] = function (Container $container) {
                return new LintCommand($container);
            };

            $commands['debug:twig'] = 'console.command.twig.debug';
            $commands['lint:twig'] = 'console.command.twig.lint';
        }

        if (class_exists(LintYamlCommand::class)) {
            $app['console.command.yaml.lint'] = function () {
                return new LintYamlCommand();
            };
            $commands['lint:yaml'] = 'console.command.yaml.lint';
        }

        $app['console.command.ids'] = $commands;
    }
}
