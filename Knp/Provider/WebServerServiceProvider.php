<?php

namespace Knp\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Bundle\WebServerBundle\Command\ServerLogCommand;
use Symfony\Bundle\WebServerBundle\Command\ServerRunCommand;
use Symfony\Bundle\WebServerBundle\Command\ServerStartCommand;
use Symfony\Bundle\WebServerBundle\Command\ServerStatusCommand;
use Symfony\Bundle\WebServerBundle\Command\ServerStopCommand;

/**
 * Registers the server:xxx console commands from the WebServerBundle.
 */
class WebServerServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the web server console commands.
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        if (!isset($container['console'])) {
            throw new \LogicException('You must register the ConsoleServiceProvider to use the WebServerServiceProvider.');
        }

        $container['web_server.document_root'] = null;
        $container['web_server.environment'] = 'dev';

        $commands = [
            'server:run' => 'web_server.command.server_run',
            'server:start' => 'web_server.command.server_start',
            'server:stop' => 'web_server.command.server_stop',
            'server:status' => 'web_server.command.server_status',
        ];

        $container['web_server.command.server_run'] = function (Container $container) {
            if (null === $docRoot = $container['web_server.document_root']) {
                throw new \LogicException('You must set the web_server.document_root parameter to use the development web server.');
            }

            return new ServerRunCommand($docRoot, $container['web_server.environment']);
        };

        $container['web_server.command.server_start'] = function (Container $container) {
            if (null === $docRoot = $container['web_server.document_root']) {
                throw new \LogicException('You must set the web_server.document_root parameter to use the development web server.');
            }

            return new ServerStartCommand($docRoot, $container['web_server.environment']);
        };

        $container['web_server.command.server_stop'] = function () {
            return new ServerStopCommand();
        };

        $container['web_server.command.server_status'] = function () {
            return new ServerStatusCommand();
        };

        if (class_exists(ConsoleFormatter::class)) {
            $container['web_server.command.server_log'] = function () {
                return new ServerLogCommand();
            };
            $commands['server:log'] = 'web_server.command.server_log';
        }

        $container['console.command.ids'] = array_merge($container['console.command.ids'], $commands);
    }
}
