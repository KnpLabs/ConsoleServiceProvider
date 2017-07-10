
# Testing commands

You can use the Symfony [CommandTester](http://symfony.com/doc/current/console.html#testing-commands)
to test your console commands:

```php
use Knp\Console\Application as ConsoleApplication;
use PHPUnit\Framework\TestCase;
use Silex\Application;

class MyCommandTest extends TestCase
{
    public function getConsole()
    {
        $silex = new Application();

        return new ConsoleApplication($silex, __DIR__);
    }

    public function testMyCommand()
    {
        $console = $this->getConsole();
        $console->add(new MyCommand());

        $tester = new CommandTester($console->find('my:command'));
        $tester->execute([
            'command' => 'my:command',
            '--option-name' => 'option-value',
        ]);
        $output = $tester->getDisplay();

        $this->assertContains('Everything is going extremely well.', $output);
    }
}
```
