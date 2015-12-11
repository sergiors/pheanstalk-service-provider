<?php
namespace Sergiors\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Leezy\PheanstalkBundle\Listener\PheanstalkLogListener;
use Leezy\PheanstalkBundle\Command\ListTubeCommand;
use Leezy\PheanstalkBundle\Command\StatsCommand;

/**
 * @author Sérgio Rafael Siqueira <sergio@gmail.com>
 */
class PheanstalkServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['pheanstalks.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['pheanstalks.options'])) {
                $app['pheanstalks.options'] = [
                    'default' => isset($app['pheanstalk.options']) ? $app['pheanstalk.options'] : []
                ];
            }

            $tmp = $app['pheanstalks.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['pheanstalk.default_options'], $options);

                if (!isset($app['pheanstalks.default'])) {
                    $app['pheanstalks.default'] = $name;
                }
            }
            $app['pheanstalks.options'] = $tmp;
        });

        $app['pheanstalks'] = $app->share(function (Application $app) {
            $app['pheanstalks.options.initializer']();

            $locator = new PheanstalkLocator();
            foreach ($app['pheanstalks.options'] as $name => $options) {
                $pheanstalk = new Pheanstalk(
                    $options['server'],
                    $options['port'],
                    $options['timeout']
                );

                $locator->addPheanstalk($name, $pheanstalk, $app['pheanstalks.default'] === $name);
            }

            return $locator;
        });

        $app['pheanstalk.listener.log'] = $app->share(function (Application $app) {
            $listener = new PheanstalkLogListener();
            $listener->setLogger($app['logger']);
            return $listener;
        });

        // shortcuts for the "first" pheanstalk
        $app['pheanstalk'] = $app->share(function (Application $app) {
            $pheanstalks = $app['pheanstalks'];
            return $pheanstalks->getPheanstalk($app['pheanstalks.default']);
        });

        $app['pheanstalk.default_options'] = [
            'server' => '127.0.0.1',
            'port' => PheanstalkInterface::DEFAULT_PORT,
            'timeout' => PheanstalkInterface::DEFAULT_TTR
        ];

        if (isset($app['console'])) {
            $app['console'] = $app->share(
                $app->extend('console', function (ConsoleApplication $console) use ($app) {
                    $locator = $app['pheanstalks'];

                    $console->add(new ListTubeCommand($locator));
                    $console->add(new StatsCommand($locator));

                    return $console;
                })
            );
        }
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['pheanstalk.listener.log']);
    }
}
