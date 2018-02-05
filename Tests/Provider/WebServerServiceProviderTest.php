<?php

namespace Knp\Tests\Provider;

use Knp\Provider\ConsoleServiceProvider;
use Knp\Provider\WebServerServiceProvider;
use Silex\Application;
use Symfony\Bundle\WebServerBundle\Command\ServerRunCommand;

class WebServerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (!class_exists(ServerRunCommand::class)) {
            self::markTestSkipped('The web-server-bundle component is not installed');
        }
    }

    public function testCommandsAreRegistered()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());
        $app->register(new WebServerServiceProvider(), [
            'web_server.document_root' => __DIR__,
        ]);
        /** @var \Knp\Console\Application $console */
        $console = $app['console'];

        $this->assertTrue($console->has('server:run'));
        $this->assertTrue($console->has('server:start'));
        $this->assertTrue($console->has('server:stop'));
        $this->assertTrue($console->has('server:status'));
        $this->assertTrue($console->has('server:log'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You must register the ConsoleServiceProvider to use the WebServerServiceProvider.
     */
    public function testRegistrationFailsIfNoConsoleProvider()
    {
        $app = new Application();
        $app->register(new WebServerServiceProvider(), [
            'web_server.document_root' => __DIR__,
        ]);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You must set the web_server.document_root parameter to use the development web server.
     */
    public function testCannotLoadRunCommandIfNoDocumentRootSet()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());
        $app->register(new WebServerServiceProvider());

        echo $app['web_server.command.server_run'];
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You must set the web_server.document_root parameter to use the development web server.
     */
    public function testCannotLoadStartCommandIfNoDocumentRootSet()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());
        $app->register(new WebServerServiceProvider());

        echo $app['web_server.command.server_start'];
    }
}
