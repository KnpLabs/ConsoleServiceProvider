<?php

namespace Tests\Provider;

use Knp\Console\Application as ConsoleApplication;
use Knp\Provider\ConsoleServiceProvider;
use Knp\Tests\Provider\Fixtures\TestBootApplication;
use Knp\Tests\Provider\Fixtures\TestBootCommand;
use Knp\Tests\Provider\Fixtures\TestCommand;
use Knp\Tests\Provider\Fixtures\TestConsoleApplication;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Command\LintCommand as LintYamlCommand;

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

    public function testCanSetCustomApplicationClass()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider(), [
            'console.class' => TestConsoleApplication::class,
        ]);

        $this->assertInstanceOf(TestConsoleApplication::class, $app['console']);
    }

    public function testCommandsAsServiceAreRegistered()
    {
        $app = new Application();
        $app['test_command'] = function () {
            return new TestCommand();
        };
        $app->register(new ConsoleServiceProvider(), [
            'console.command.ids' => ['test_command'],
        ]);

        $this->assertTrue($app['console']->has('test:test'));
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

    public function testDebugTwigCommand()
    {
        if (!class_exists(DebugCommand::class)) {
            $this->markTestSkipped('symfony/twig-bridge is not available.');
        }
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new ConsoleServiceProvider());

        /** @var ConsoleApplication $console */
        $console = $app['console'];

        $this->assertTrue($console->has('debug:twig'));

        $tester = new CommandTester($command = $console->find('debug:twig'));
        $tester->execute([
            'command' => 'debug:twig',
            '--format' => 'json',
        ]);
        $output = json_decode($tester->getDisplay(), JSON_OBJECT_AS_ARRAY);

        $this->assertArrayHasKey('functions', $output);
    }

    public function testLintTwigCommand()
    {
        if (!class_exists(DebugCommand::class)) {
            $this->markTestSkipped('symfony/twig-bridge is not available.');
        }
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new ConsoleServiceProvider());

        /** @var ConsoleApplication $console */
        $console = $app['console'];

        $this->assertTrue($console->has('lint:twig'));

        $tester = new CommandTester($command = $console->find('lint:twig'));
        $tester->execute([
            'command' => 'lint:twig',
            'filename' => [__DIR__.'/../Fixtures/Command/Twig/valid.html.twig'],
        ]);
        $output = $tester->getDisplay();

        $this->assertContains('[OK] All 1 Twig files contain valid syntax.', $output);
    }

    public function testLintYamlCommand()
    {
        if (!class_exists(LintYamlCommand::class)) {
            $this->markTestSkipped('symfony/yaml >= 3.2 is not available.');
        }
        $app = new Application();
        $app->register(new ConsoleServiceProvider());

        /** @var ConsoleApplication $console */
        $console = $app['console'];

        $this->assertTrue($console->has('lint:yaml'));

        $tester = new CommandTester($command = $console->find('lint:yaml'));
        $tester->execute([
            'command' => 'lint:yaml',
            'filename' => __DIR__.'/../Fixtures/Command/Yaml/valid.yml',
        ]);
        $output = $tester->getDisplay();

        $this->assertContains('[OK] All 1 YAML files contain valid syntax.', $output);
    }

    public function testApplicationBootsBeforeCommand()
    {
        $app = new TestBootApplication();
        $app->register(new ConsoleServiceProvider());

        /** @var ConsoleApplication $console */
        $console = $app['console'];

        $console->setAutoExit(false);
        $console->add(new TestBootCommand());

        $tester = new ApplicationTester($console);

        $this->assertFalse($app->isBooted(), 'Loading the console should not boot the Silex application');
        $tester->run(['command' => 'test:boot']);

        $output = $tester->getDisplay();
        $this->assertSame('Booted', $output, 'The Silex application must boot before console commands are executed');
    }

    /**
     * @group legacy
     */
    public function testBootingSilexFromApplicationConstructor()
    {
        $app = new TestBootApplication();
        $app->register(new ConsoleServiceProvider());
        $app['console.boot_in_constructor'] = true;

        $console = $app['console'];
        $this->assertTrue($app->isBooted());
    }
}
