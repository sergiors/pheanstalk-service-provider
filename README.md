Pheanstalk Service Provider
---------------------------

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
```

License
-------
MIT
