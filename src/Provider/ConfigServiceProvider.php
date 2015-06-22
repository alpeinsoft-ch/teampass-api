<?php

namespace Teampass\Api\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Parser;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['config'] = $app->share(function ($app) {
            $config = [];
            $yaml = new Parser();
            $data = $yaml->parse(file_get_contents($app['root_dir'].'/app/config.yml'));
            foreach ($data['parameters'] as $parameter => $value) {
                if (false === strpos($parameter, 'database_')) {
                    $config[$parameter] = $value;
                } else {
                    $config['database']['db.options'][str_replace('database_', '', $parameter)] = $value;
                }
            }

            return $config;
        });
    }

    public function boot(Application $app)
    {
    }
}
