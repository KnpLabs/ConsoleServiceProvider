# ConsoleServiceProvider

This project is now maintained by [Crimson Labs](https://github.com/CrimsonLabs) .

Provides a `Symfony\Component\Console` based console for Silex.

Use Version v1.0 for Silex 1.* compatibility, Version 2.0 for Silex 2.* compatibility

## Install

Add `knplabs/console-service-provider` to your `composer.json` and register the service:

```php
<?php

use Knp\Provider\ConsoleServiceProvider;

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'MyApplication',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
));

?>
```

You can now copy the `console` executable in whatever place you see fit, and tweak it to your needs. You will need a way to fetch your silex application, the most common way is to return it from your bootstrap:

```php
<?php

$app = new Silex\Application();

// your beautiful silex bootstrap

return $app;

?>
```

For the rest of this document, we will assume you do have an `app` directory, so the `console` executable will be located at `app/console`.

## Usage

Use the console just like any `Symfony\Component` based console:

```
$ app/console my:command
```

## Write commands

Your commands should extend `Knp\Command\Command` to have access to the 2 useful following commands:

* `getSilexApplication`, which returns the silex application
* `getProjectDirectory`, which returns your project's root directory (as configured earlier)

I know, it's a lot to learn, but it's worth the pain.

## Register commands

There are two ways of registering commands to the console application.

### Directly access the console application from the `console` executable

Open up `app/console`, and stuff your commands directly into the console application:

```php
#!/usr/bin/env php
<?php

set_time_limit(0);

$app = require_once __DIR__.'/bootstrap.php';

use My\Command\MyCommand;

$application = $app['console'];
$application->add(new MyCommand());
$application->run();

?>
```

### Extend the `console` service

This way is intended for use by provider developers and exposes an unobstrusive way to register commands.

```php
<?php

une Knp\Console\Application;
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
?>
```

### Use the Event Dispatcher (deprecated)

If you used an old version of the console provider and still listen to the
`Knp\Console\ConsoleEvents::INIT` event to register commands, you should
modify your code and extend the `console` service instead.

**Before:**
```php
<?php

use My\Command\MyCommand;
use Knp\Console\ConsoleEvents;
use Knp\Console\ConsoleEvent;

$app['dispatcher']->addListener(ConsoleEvents::INIT, function(ConsoleEvent $event) {
    $app = $event->getApplication();
    $app->add(new MyCommand());            
});
```

**After:**
```php
<?php

use My\Command\MyCommand;
une Knp\Console\Application;

$app->extend('console', function (Application $console) {
    $console->add(new MyCommand());

    return $console;
});
```

## Listen to console events

You can listen to the `Symfony\Component\Console\ConsoleEvents` events
by adding listeners to Silex:

```php
<?php

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;

$app->on(ConsoleEvents::EXCEPTION, function (ConsoleExceptionEvent $event) use ($app) {
    // Log console errors
    $app['logger']->error($event->getException()->getMessage());
});
```
