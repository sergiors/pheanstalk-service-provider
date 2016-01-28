<?php

namespace Sergiors\Silex\Tests\Provider;

use Silex\Application;
use Silex\WebTestCase;
use Pheanstalk\PheanstalkInterface;
use Sergiors\Silex\Provider\ConsoleServiceProvider;
use Sergiors\Silex\Provider\PheanstalkServiceProvider;

class PheanstalkServiceProviderTest extends WebTestCase
{
    /**
     * @test
     */
    public function register()
    {
        $app = $this->createApplication();
        $app->register(new PheanstalkServiceProvider(), [
            'pheanstalk.options' => [
                'timeout' => 120,
            ],
        ]);

        $this->assertInstanceOf(PheanstalkInterface::class, $app['pheanstalk']);
        $this->assertEquals($app['pheanstalk'], $app['pheanstalks']->getPheanstalk('default'));
    }

    /**
     * @test
     */
    public function multipleConnections()
    {
        $app = $this->createApplication();
        $app->register(new PheanstalkServiceProvider(), [
            'pheanstalks.options' => [
                'conn1' => [
                    'server' => 'localhost',
                ],
                'conn2' => [
                    'server' => 'localhost',
                ],
            ],
        ]);

        $this->assertCount(2, $app['pheanstalks']->getPheanstalks());
        $this->assertEquals($app['pheanstalks']->getDefaultPheanstalk(), $app['pheanstalk']);
        $this->assertEquals($app['pheanstalks']->getPheanstalk('conn1'), $app['pheanstalk']);
    }

    /**
     * @test
     */
    public function shouldReturnTheCommands()
    {
        $app = $this->createApplication();
        $app->register(new ConsoleServiceProvider());
        $app->register(new PheanstalkServiceProvider());

        $this->assertCount(13, $app['console']->all('leezy:pheanstalk'));
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['exception_handler']->disable();

        return $app;
    }
}
