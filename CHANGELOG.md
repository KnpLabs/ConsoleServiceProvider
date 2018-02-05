
ConsoleServiceProvider changelog
================================

v2.2.0 (2018-02-05)
-------------------

- Fixed compatibility with Symfony 3.4 and 4.0 (chihiro-adachi, skalpa)

v2.1.0 (2017-07-18)
-------------------

- Allow Silex applications to listen to console events
- Deprecated `Knp\Console\ConsoleEvents::INIT`
- Added the `console.class` parameter
- Added the `console.command.ids` parameter
- Added support for the `lint:twig` and `debug:twig` commands from `symfony/twig-bridge`
- Added support for the `lint:yaml` command from `symfony/yaml`
- Added `symfony/web-server-bundle` support
- **[Minor BC break]** The console constructor does not boot the Silex application anymore. You can set the `console.boot_in_constructor` parameter to true if your code depends on the old behavior.

### `Knp\Console\ConsoleEvents::INIT` deprecation

If you used an old version of the console provider and still listen to the
`Knp\Console\ConsoleEvents::INIT` event to register commands, you should
modify your code and extend the `console` service instead.

**Before:**
```php
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
use My\Command\MyCommand;
use Knp\Console\Application;

$app->extend('console', function (Application $console) {
    $console->add(new MyCommand());

    return $console;
});
```

### Booting Silex from the console constructor

Older versions of the provider used to boot the Silex application when the console was loaded.
This has been considered a bug and has been fixed in this version: the new console application
only boots Silex before command execution.

If your code depends on the old behavior, you can set the `console.boot_in_constructor`
parameter to `true`. Please note that this parameter will be removed in v3 of the service
provider and update your projects accordingly.


v2.0.0 (2016-06-01)
-------------------

- Added Silex 2 compatibility
