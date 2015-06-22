<?php

namespace Teampass\Api\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Teampass\Api\Service\Tree\TeampassTree;

class TeampassTreeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['teampass.tree'] = $app->share(function ($app) {
            return new TeampassTree($app['db']);
        });
    }

    public function boot(Application $app)
    {
    }
}
