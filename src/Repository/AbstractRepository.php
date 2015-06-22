<?php

namespace Teampass\Api\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var RepositoryContainer
     */
    protected $repositoryContainer;

    public function __construct(Connection $connection, RepositoryContainer $repositoryContainer)
    {
        $this->connection = $connection;
        $this->repositoryContainer = $repositoryContainer;
    }

    protected function getTableName($name)
    {
        return sprintf('%s_%s', $this->connection->getParams()['prefix'], $name);
    }

    /**
     * Can be called in finishHydrateObject to normalize a date to a DateTime object.
     *
     * @param $propertyName
     * @param $object
     */
    protected function normalizeDateTimeProperty($propertyName, $object)
    {
        $object->$propertyName = \DateTime::createFromFormat('Y-m-d H:i:s', $object->$propertyName);
    }
}
