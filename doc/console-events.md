
# Listening to console events

You can listen to the [console events](http://symfony.com/doc/current/components/console/events.html)
by adding listeners to Silex:

```php
<?php

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

$app->on(ConsoleEvents::EXCEPTION, function (ConsoleExceptionEvent $event) use ($app) {
    // Log console errors
    $app['logger']->error($event->getException()->getMessage());
});

$app->on(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) use ($app) {
    $app['logger']->info(sprintf(
        'Command %s terminated with exit code %d.',
        $event->getCommand()->getName(),
        $event->getExitCode()
    ));
});
```
