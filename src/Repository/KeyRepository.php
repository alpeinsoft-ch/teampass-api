<?php

namespace Teampass\Api\Repository;

use Doctrine\DBAL\Connection;

class KeyRepository extends AbstractRepository
{
    public function findByLabel($label, array $user)
    {
        $nodes = $this->connection->executeQuery(
            sprintf('SELECT DISTINCT folder_id FROM %s WHERE role_id IN (?)', $this->getTableName('roles_values')),
            [explode(';', $user['fonction_id'])],
            [Connection::PARAM_INT_ARRAY]
        )->fetchAll(\PDO::FETCH_COLUMN);

        return $this->connection->executeQuery(
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, email, url, description, id_tree AS folder FROM %s WHERE label = ? AND id_tree IN (?) AND inactif = ?', $this->getTableName('items')),
            [$label, $nodes, 0],
            [\PDO::PARAM_STR, Connection::PARAM_INT_ARRAY, \PDO::PARAM_INT]
        )->fetch();
    }

    public function findById($id, array $user)
    {
        $nodes = $this->connection->executeQuery(
            sprintf('SELECT DISTINCT folder_id FROM %s WHERE role_id IN (?)', $this->getTableName('roles_values')),
            [explode(';', $user['fonction_id'])],
            [Connection::PARAM_INT_ARRAY]
        )->fetchAll(\PDO::FETCH_COLUMN);

        $key = $this->connection->executeQuery(
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, email, url, description, id_tree AS folder FROM %s WHERE id = ? AND id_tree IN (?) AND inactif = ?', $this->getTableName('items')),
            [$id, $nodes, 0],
            [\PDO::PARAM_INT, Connection::PARAM_INT_ARRAY, \PDO::PARAM_INT]
        )->fetch();

        if (!$key) {
            return false;
        }

        $randKeys = $this->connection->executeQuery(
            sprintf('SELECT * FROM %s WHERE sql_table = ? AND id = ?', $this->getTableName('keys')),
            ['items', $id]
        )->fetch();
        $key['username'] = $this->repositoryContainer->get('encoder')->encrypt($key['username']);
        $key['password'] = $this->repositoryContainer->get('encoder')->encrypt((string) substr($this->repositoryContainer->get('teampass.encoder')->decrypt($key['password']), strlen($randKeys['rand_key'])));

        return $key;
    }

    public function findAllByNode($node = 0)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, email, url, description FROM %s WHERE id_tree = ? AND inactif = ?', $this->getTableName('items')),
            [$node, 0]
        );
    }

    public function create(array $data, array $user)
    {
        $randomKey = substr(md5(rand().rand()), 0, 15);
        $data['pw'] = $this->repositoryContainer->get('teampass.encoder')->encrypt($randomKey.$data['pw']);
        $this->connection->insert($this->getTableName('items'), $data);
        $key = $this->connection->executeQuery(
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, email, url, description, id_tree AS folder FROM %s WHERE label = ?', $this->getTableName('items')),
            [$data['label']],
            [\PDO::PARAM_STR]
        )->fetch();
        $date = new \DateTime('now');
        $this->connection->insert($this->getTableName('log_items'), ['id_item' => (int) $key['id'], 'date' => $date->getTimestamp(), 'id_user' => $user['id'], 'action' => 'at_creation']);
        $this->connection->insert($this->getTableName('keys'), ['sql_table' => 'items', 'id' => (int) $key['id'], 'rand_key' => $randomKey]);
        $key['password'] = (string) substr($this->repositoryContainer->get('teampass.encoder')->decrypt($key['password']), strlen($randomKey));

        return $key;
    }

    public function update($id, array $data, array $user)
    {
        if (array_key_exists('pw', $data)) {
            $randKeys = $this->connection->executeQuery(
                sprintf('SELECT * FROM %s WHERE sql_table = ? AND id = ?', $this->getTableName('keys')),
                ['items', $id]
            )->fetch();
            $data['pw'] = $this->repositoryContainer->get('teampass.encoder')->encrypt($randKeys['rand_key'].$data['pw']);
        }

        $this->connection->update($this->getTableName('items'), $data, ['id' => $id]);

        return $this->findById($id, $user);
    }

    public function delete($id)
    {
        $this->connection->delete($this->getTableName('items'), ['id' => $id]);
        $this->connection->delete($this->getTableName('keys'), ['sql_table' => 'items', 'id' => $id]);

        return true;
    }
}
