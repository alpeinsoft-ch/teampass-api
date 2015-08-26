<?php

namespace Teampass\Api\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Teampass\Api\Service\Platform\Encoder;
use Teampass\Api\Service\Platform\Tree;

class PlatformServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['platform.encoder'] = $app->share(function ($app) {
            return new Encoder($app['config']['teampass_salt']);
        });

        $app['platform.tree'] = $app->share(function ($app) {
            return new Tree($app['db']);
        });
    }

    public function boot(Application $app)
    {
    }
}
