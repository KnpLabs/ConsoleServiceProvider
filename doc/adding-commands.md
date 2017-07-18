
# Creating and registering commands

> If you are not familiar with the Console component, you may want to read the
> [Console Commands] documentation first.

## Writing commands

Your commands can extend `Knp\Command\Command` to have access to the 2 useful
following methods:

* `getSilexApplication`, which returns the silex application
* `getProjectDirectory`, which returns your project's root directory (as configured earlier)

> This is not a requirement, and any command that extends `Symfony\Component\Console\Command\Command`
should work. If you don't want to couple your command with a class of the
service provider, you may want to inject its dependencies using constructor
injection instead and [register your command as a service](#commands-as-services).

## Registering commands

There are two ways of registering commands to the console application.

### Directly access the console application from the `console` executable

Open up `bin/console`, and stuff your commands directly into the console application:

```php
#!/usr/bin/env php
<?php

set_time_limit(0);

$app = require_once __DIR__.'/bootstrap.php';

$console = $app['console'];

$console->add(new My\Command\MyCommand());

$console->run();
```

### Extend the `console` service

This way is intended for use by provider developers and exposes an unobstrusive way to register commands.

```php
use Knp\Console\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UnderpantsProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        if (isset($container['console'])) {
            $container->extend('console', function (Application $console) {
                $console->add(new CollectCommand());
                $console->add(new QuestionMarkCommand());
                $console->add(new ProfitCommand());

                return $console;
            });
        }
    }
}
```

## Commands as services

Alternatively, you can register your commands as Silex services and add their
identifiers to the `console.commands.ids` parameter. This parameter
contains a list of command service ids indexed by command name
(the value returned by `Command::getName`):

```php
use Knp\Console\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UnderpantsProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['underpants.command.collect'] = function ($container) {
            return new CollectCommand($container['logger']);
        };
        $container['underpants.command.profit'] = function ($container) {
            return new ProfitCommand();
        };
        $commands = $container['console.command.ids'];
        $commands['collect:underpants'] = 'underpants.command.collect';
        $commands['profit:underpants'] = 'underpants.command.profit';

        $container['console.command.ids'] = $commands;
    }
}
```

[Console Commands]: http://symfony.com/doc/current/console.html "Console Commands"
