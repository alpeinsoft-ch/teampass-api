<?php

namespace Teampass\Api\Repository;

class RepositoryContainer
{
    /**
     * @var \Pimple
     */
    private $container;

    /**
     * @var array
     */
    private $repositoryMap;

    public function __construct(\Pimple $container, array $repositoryMap)
    {
        $this->container = $container;
        $this->repositoryMap = $repositoryMap;
    }

    /**
     * @param $key
     *
     * @return AbstractRepository
     *
     * @throws \Exception
     */
    public function get($key)
    {
        if (!isset($this->repositoryMap[$key])) {
            throw new \Exception(sprintf('Unknown repository name %s', $key));
        }
        $service = $this->repositoryMap[$key];

        return $this->container[$service];
    }
}
