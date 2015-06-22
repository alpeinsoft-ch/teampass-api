<?php

namespace Teampass\Api\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Teampass\Api\Service\Encoder\TeampassEncoder;

class EncoderServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['teampass.encoder'] = $app->share(function ($app) {
            return new TeampassEncoder($app['config']['teampass_salt']);
        });

        $app['api.encoder'] = $app->share(function ($app) {
            $encoder = $app['config']['encoder'];

            return new $encoder();
        });
    }

    public function boot(Application $app)
    {
    }
}
