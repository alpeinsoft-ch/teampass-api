<?php

namespace Teampass\Api\Repository;

final class UserRepository extends AbstractRepository
{
    public function findByLogin($login)
    {
        $user = $this->connection->executeQuery(
            sprintf('SELECT * FROM %s WHERE login = ?', $this->getTableName('users')),
            [$login]
        )->fetch();

        return $user;
    }
}
