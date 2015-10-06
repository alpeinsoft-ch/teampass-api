<?php

namespace Teampass\Api\Repository;

use Doctrine\DBAL\Connection;

class NodeRepository extends AbstractRepository
{
    public function findAllByUser(array $user)
    {
        return $this->buildTree($user);
    }

    public function findById($id, array $user)
    {
        $item = $this->connection->executeQuery(
            sprintf('SELECT nt.id, nt.title, \'FOLDER\' AS type, nt.parent_id, m.valeur as complication FROM %s nt LEFT JOIN %s m ON m.intitule = nt.id AND m.type = \'complex\' WHERE  nt.id = ? AND personal_folder = 0', $this->getTableName('nested_tree'), $this->getTableName('misc')),
            [$id]
        )->fetch();

        if (!$item) {
            return false;
        }

        $access = array_map(function ($value) {
            return $value['type'];
        }, $this->connection->fetchAll(sprintf('SELECT DISTINCT type FROM %s WHERE role_id IN (?) AND folder_id = ?', $this->getTableName('roles_values')), [explode(';', $user['fonction_id']), $item['id']], [Connection::PARAM_INT_ARRAY]));

        $node = [
            'id' => $item['id'],
            'title' => $item['title'],
            'type' => $item['type'],
            'complication' => $item['complication'],
            'access' => implode(', ', $access),
            'descendants' => $this->buildTree($user, $item['id']),
        ];

        foreach ($this->repositoryContainer->get('key')->findAllByNode($node['id']) as $key) {
            $randKeys = $this->connection->executeQuery(sprintf('SELECT * FROM %s WHERE sql_table = ? AND id = ?', $this->getTableName('keys')), ['items', $key['id']])->fetch();
            $key['username'] = $this->repositoryContainer->get('encoder')->encrypt($key['username']);
            $key['password'] = $this->repositoryContainer->get('encoder')->encrypt((string)($this->repositoryContainer->get('platform.encoder')->decrypt($key['password'], $key['iv'])));
	    array_push($node['descendants'], $key);
        }

        return $node;
    }

    public function create(array $data, array $user)
    {
        $complication = $data['complication'];
        unset($data['complication']);

        $node = $this->connection->executeQuery(
            sprintf('SELECT * FROM %s WHERE title = ?', $this->getTableName('nested_tree')),
            [$data['title']]
        )->fetch();

        if (is_array($node) && !empty($node)) {
            return false;
        }

        $this->connection->insert($this->getTableName('nested_tree'), $data);
        $this->repositoryContainer->get('platform.tree')->rebuild();

        $node = $this->connection->executeQuery(
            sprintf('SELECT * FROM %s WHERE title = ?', $this->getTableName('nested_tree')),
            [$data['title']]
        )->fetch();

        $this->connection->insert($this->getTableName('misc'), ['type' => 'complex', 'intitule' => $node['id'], 'valeur' => $complication]);
        foreach (explode(';', $user['fonction_id']) as $role) {
            $this->connection->insert($this->getTableName('roles_values'), ['role_id' => $role, 'folder_id' => $node['id'], 'type' => 'W']);
        }

        return $this->findById($node['id'], $user);
    }

    public function update($id, array $data, array $user)
    {
        $complication = $data['complication'];
        unset($data['complication']);

        $this->connection->update($this->getTableName('nested_tree'), $data, ['id' => $id]);
        $this->connection->update($this->getTableName('misc'), ['valeur' => $complication], ['type' => 'complex', 'intitule' => $id]);

        return $this->findById($id, $user);
    }

    public function delete($id)
    {
        $nodes = $this->repositoryContainer->get('platform.tree')->getDescendants($id, true, false, true);
        foreach ($nodes as $node) {
            $this->connection->delete($this->getTableName('nested_tree'), ['id' => $node]);
            $this->connection->delete($this->getTableName('misc'), ['type' => 'complex', 'intitule' => $node]);
            $this->connection->delete($this->getTableName('roles_values'), ['folder_id' => $node]);
            $this->connection->delete($this->getTableName('items'), ['id_tree' => $node]);
        }
        $this->repositoryContainer->get('platform.tree')->rebuild();

        return true;
    }

    private function buildTree(array $user, $parent = 0, $tree = [])
    {
        $nodes = $this->connection->fetchAll(
            sprintf('SELECT nt.id, nt.title, nt.parent_id, m.valeur as complication FROM %s nt LEFT JOIN %s m ON m.intitule = nt.id AND m.type = \'complex\' WHERE nt.parent_id = ? AND personal_folder = 0 ORDER BY nt.nright DESC, nt.parent_id DESC', $this->getTableName('nested_tree'), $this->getTableName('misc')),
            [$parent]
        );

        if (count($nodes) > 0) {
            foreach ($nodes as $item) {
                $access = array_map(function ($value) {
                    return $value['type'];
                }, $this->connection->fetchAll(sprintf('SELECT DISTINCT type FROM %s WHERE role_id IN (?) AND folder_id = ?', $this->getTableName('roles_values')), [explode(';', $user['fonction_id']), $item['id']], [Connection::PARAM_INT_ARRAY]));

                $node = [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'type' => 'FOLDER',
                    'complication' => $item['complication'],
                    'access' => implode(', ', $access),
                    'descendants' => $this->buildTree($user, $item['id']),
                ];

                foreach ($this->repositoryContainer->get('key')->findAllByNode($node['id']) as $key) {
                    $randKeys = $this->connection->executeQuery(sprintf('SELECT * FROM %s WHERE sql_table = ? AND id = ?', $this->getTableName('keys')), ['items', $key['id']])->fetch();
                    $key['username'] = $this->repositoryContainer->get('encoder')->encrypt($key['username']);
                    $key['password'] = $this->repositoryContainer->get('encoder')->encrypt((string)($this->repositoryContainer->get('platform.encoder')->decrypt($key['password'])));
		    array_push($node['descendants'], $key);
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }
}
