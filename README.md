Pheanstalk Service Provider
---------------------------
[![Build Status](https://travis-ci.org/sergiors/pheanstalk-service-provider.svg)](https://travis-ci.org/sergiors/pheanstalk-service-provider)

To see the complete documentation, check out [LeezyPheanstalkBundle](https://github.com/armetiz/LeezyPheanstalkBundle)

Install
-------
```bash
composer require sergiors/pheanstalk-service-provider "dev-master"
```

How to use
----------
```php
use Sergiors\Silex\PheanstalkServiceProvider;

$app->register(new PheanstalkServiceProvider(), [
    'pheanstalk.options' => [
        'server' => '',
        'port' => ''
    ]
]);

$app['pheanstalk']
    ->useTube('test')
    ->put(json_encode([
        'name' => 'Kirk'
    ]));
```

If you install [Console Service Provider](https://github.com/sergiors/console-service-provider), you can enjoy more features.

License
-------
MIT
