<?php

namespace Teampass\Api\Controller;

use Silex\Application as SilexApplication;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Teampass\Api\Application;

abstract class AbstractController implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    protected $container;

    public function __construct(Application $app)
    {
        $this->container = $app;
    }

    public function connect(SilexApplication $app)
    {
        $controllers = $app['controllers_factory'];
        $this->addRoutes($controllers);

        return $controllers;
    }

    abstract protected function addRoutes(ControllerCollection $controllers);

    /**
     * @param $object
     *
     * @return array
     */
    protected function validate($object)
    {
        return $this->container['api.validator']->validate($object);
    }
}
