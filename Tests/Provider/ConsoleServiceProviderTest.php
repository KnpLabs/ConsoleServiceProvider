<?php

namespace Tests\Provider;

use Knp\Console\Application as ConsoleApplication;
use Knp\Provider\ConsoleServiceProvider;
use Knp\Tests\Provider\Fixtures\TestCommand;
use Silex\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Tester\ApplicationTester;

class ConsoleServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfiguration()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());

        $console = $app['console'];
        $console->setAutoExit(false);
        $console->add(new TestCommand());

        $this->assertInstanceOf(ConsoleApplication::class, $console);

        $tester = new ApplicationTester($console);
        $tester->run(['command' => 'test:test']);

        $this->assertContains('Test command', $tester->getDisplay());
    }

    public function testApplicationParametersAreInjected()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider(), [
            'console.name' => 'Test application',
            'console.version' => '1.42',
            'console.project_directory' => __DIR__,
        ]);
        /** @var ConsoleApplication $console */
        $console = $app['console'];

        $this->assertSame('Test application', $console->getName());
        $this->assertSame('1.42', $console->getVersion());
        $this->assertSame(__DIR__, $console->getProjectDirectory());
    }

    public function testConsoleEventsAreDispatched()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());

        /** @var ConsoleApplication $console */
        $console = $app['console'];
        $console->setAutoExit(false);
        $console->add(new TestCommand());

        $listenerCalled = false;
        $app['dispatcher']->addListener(ConsoleEvents::COMMAND, function () use (&$listenerCalled) {
            $listenerCalled = true;
        });
        $tester = new ApplicationTester($console);
        $tester->run(['command' => 'test:test']);

        $this->assertTrue($listenerCalled);
    }

    /**
     * @group legacy
     * @expectedDeprecation Listening to the Knp\Console\ConsoleEvents::INIT event is deprecated %s
     */
    public function testKnpConsoleInitEventIsDispatched()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());

        $app['dispatcher']->addListener(\Knp\Console\ConsoleEvents::INIT, function (\Knp\Console\ConsoleEvent $event) {
            $event->getApplication()->add(new TestCommand());
        });

        /** @var ConsoleApplication $console */
        $console = $app['console'];

        $this->assertTrue($console->has('test:test'));
    }
}
