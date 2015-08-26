<?php

namespace Teampass\Api\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Teampass\Api\Service\Encoder\EncoderFactory;

class ApiServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['api.encoder'] = $app->share(function ($app) {
            return EncoderFactory::getEncoder($app['config']['encoder'], $app['config']['secret']);
        });
    }

    public function boot(Application $app)
    {
    }
}
