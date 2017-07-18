
# ConsoleServiceProvider

Provides a [`Symfony\Component\Console`](http://symfony.com/doc/current/components/console.html) based console for Silex 2.x.

## Installation

Add `knplabs/console-service-provider` to your `composer.json` and register the service provider:

```
composer require knplabs/console-service-provider
```

```php
use Knp\Provider\ConsoleServiceProvider;

$app->register(new ConsoleServiceProvider());
```

You can now copy the `console` executable from the `bin` folder to whatever
place you see fit, and tweak it to your needs.

You will need a way to fetch your silex application, the most common way is
to return it from your bootstrap file:

```php
use Knp\Provider\ConsoleServiceProvider;
use Silex\Application;

$app = new Application();

$app->register(new ConsoleServiceProvider());
$app->register(new SomeOtherServiceProvider());

return $app;
```

For the rest of this documentation, we will assume you do have a `bin`
directory, so the `console` executable will be located at `bin/console`.

## Usage

Use the console just like any `Symfony\Component\Console` based console:

```
$ bin/console my:command
```

or on Windows:

```
$ php bin/console my:command
```

## Configuration parameters

| Parameter                            | Default                 | Description  |
|-----------------------------------|----------------------|-------------|
| `console.name` (string)              | Silex console           | Name of your console application |
| `console.version` (string)           | UNKNOWN                 | Version of your console application |
| `console.project_directory` (string) | (auto-detected)         | Your project's directory path. The default value should work, assuming the provider is installed in `vendor/knplabs/console-service-provider` |
| `console.class` (string)             | Knp\Console\Application | Class name of the console service |
| `console.boot_in_constructor` (bool) | false                   | Whether the console should boot Silex when loaded (set it to `true` if you depend on [a bug that was fixed in 2.1](CHANGELOG.md#booting-silex-from-the-console-constructor)) |
| `console.command.ids` (array)        | array()                 | Console commands registered as services |

## Default commands

The service provider will register the following commands if the corresponding
Symfony components are installed:

- From `symfony/twig-bridge`, the `lint:twig` and `debug:twig` commands
- From `symfony/yaml`, the `lint:yaml` command

### Web-server-bundle support

The `WebServerServiceProvider` will register the commands provided by `symfony/web-server-bundle`.

```php
$app = new Silex\Application();

$app->register(new Knp\Provider\ConsoleServiceProvider());
$app->register(new Knp\Provider\WebServerServiceProvider(), array(
    // Folder that contains your front controller/public files
    'web_server.document_root' => __DIR__.'/../public',
));
```

The server commands expect your front controller to be located in your
document root and be called `app_dev.php`, `app.php`, `index_dev.php` or `index.php`.

For more information, please consult the [Symfony documentation](https://symfony.com/doc/current/setup/built_in_web_server.html).

## Recipes

- [Writing and registering commands](doc/adding-commands.md)
- [Testing your commands](doc/testing-commands.md)
- [Listening to console events](doc/console-events.md)
