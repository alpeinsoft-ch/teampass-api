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
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, pw_iv AS iv, email, url, description, id_tree AS folder FROM %s WHERE label = ? AND id_tree IN (?) AND inactif = ?', $this->getTableName('items')),
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
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, pw_iv AS iv, email, url, description, id_tree AS folder FROM %s WHERE id = ? AND id_tree IN (?) AND inactif = ?', $this->getTableName('items')),
            [$id, $nodes, 0],
            [\PDO::PARAM_INT, Connection::PARAM_INT_ARRAY, \PDO::PARAM_INT]
        )->fetch();

        if (!$key) {
            return false;
        }

        $key['username'] = $this->repositoryContainer->get('encoder')->encrypt($key['username']);
        $key['password'] = $this->repositoryContainer->get('encoder')->encrypt($this->repositoryContainer->get('platform.encoder')->decrypt($key['password'], $key['iv']));
        unset($key['iv']);

        return $key;
    }

    public function findAllByNode($node = 0)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, pw_iv AS iv, email, url, description FROM %s WHERE id_tree = ? AND inactif = ?', $this->getTableName('items')),
            [$node, 0]
        );
    }

    public function create(array $data, array $user)
    {
        $encrypt = $this->repositoryContainer->get('platform.encoder')->encrypt($data['pw']);
        $data['pw'] = $encrypt['string'];
        $data['pw_iv'] = $encrypt['iv'];
        $this->connection->insert($this->getTableName('items'), $data);
        $key = $this->connection->executeQuery(
            sprintf('SELECT id, label AS title, \'PASSWORD\' AS type, login AS username, pw AS password, pw_iv AS iv, email, url, description, id_tree AS folder FROM %s WHERE label = ?', $this->getTableName('items')),
            [$data['label']],
            [\PDO::PARAM_STR]
        )->fetch();
        $date = new \DateTime('now');
        $this->connection->insert($this->getTableName('log_items'), ['id_item' => (int) $key['id'], 'date' => $date->getTimestamp(), 'id_user' => $user['id'], 'action' => 'at_creation']);

        return  $this->findById($key['id'], $user);
    }

    public function update($id, array $data, array $user)
    {
        $encrypt = $this->repositoryContainer->get('platform.encoder')->encrypt($data['pw']);
        $data['pw'] = $encrypt['string'];
        $data['pw_iv'] = $encrypt['iv'];
        $this->connection->update($this->getTableName('items'), $data, ['id' => $id]);

        return $this->findById($id, $user);
    }

    public function delete($id)
    {
        $this->connection->delete($this->getTableName('items'), ['id' => $id]);

        return true;
    }
}
