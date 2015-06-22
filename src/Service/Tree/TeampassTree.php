<?php

namespace Teampass\Api\Service\Tree;

use Doctrine\DBAL\Connection;

class TeampassTree
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->table = 'teampass_nested_tree';
        $this->fields = ['id' => 'id', 'parent' => 'parent_id', 'sort' => 'title'];
    }

    public function getFields()
    {
        return [$this->fields['id'], $this->fields['parent'], $this->fields['sort'], 'nleft', 'nright', 'nlevel', 'personal_folder'];
    }

    public function build()
    {
        $idField = $this->fields['id'];
        $parentField = $this->fields['parent'];
        $result = $this->connection->executeQuery(sprintf('SELECT %s FROM %s ORDER BY %s', implode(', ', $this->getFields()), $this->table, $this->fields['sort']));

        $root = new \stdClass();
        $root->$idField = 0;
        $root->children = [];

        $arr[$root->$idField] = $root;

        foreach ($result->fetchAll(\PDO::FETCH_OBJ) as $row) {
            $arr[$row->$idField] = $row;
            $arr[$row->$idField]->children = array();
        }

        foreach ($arr as $id => $row) {
            if (isset($row->$parentField)) {
                $arr[$row->$parentField]->children[$id] = $id;
            }
        }

        return $arr;
    }

    public function rebuild()
    {
        $data = $this->build();
        $n = 0;
        $this->generate($data, 0, 0, $n);

        foreach ($data as $id => $row) {
            if ($id == 0) {
                continue;
            }

            $this->connection->executeUpdate(sprintf('UPDATE %s SET nlevel = %d, nleft = %d, nright = %d where %s = %d',
                $this->table,
                $row->nlevel,
                $row->nleft,
                $row->nright,
                $this->fields['id'],
                $id
            ));
        }
    }

    public function generate(&$arr, $id, $level, &$n)
    {
        $arr[$id]->nlevel = $level;
        $arr[$id]->nleft = $n++;

        foreach ($arr[$id]->children as $child_id) {
            $this->generate($arr, $child_id, $level + 1, $n);
        }
        $arr[$id]->nright = $n++;
    }

    public function getDescendants($id = 0, $includeSelf = false, $childrenOnly = false, $unique_id_list = false)
    {
        $idField = $this->fields['id'];
        $node = $this->getNode($id);

        $nleft = 0;
        $nright = 0;
        $parent_id = 0;
        $personal_folder = 0;

        if ($node) {
            $nleft = $node->nleft;
            $nright = $node->nright;
            $parent_id = $node->$idField;
            $personal_folder = $node->personal_folder;
        }

        if ($childrenOnly) {
            if ($includeSelf) {
                $query = sprintf('SELECT %s FROM %s WHERE %s = %d OR %s = %d ORDER BY nleft',
                    implode(',', $this->getFields()),
                    $this->table,
                    $this->fields['id'],
                    $parent_id,
                    $this->fields['parent'],
                    $parent_id
                );
            } else {
                $query = sprintf('SELECT %s FROM %s WHERE %s = %d ORDER BY nleft',
                    implode(',', $this->getFields()),
                    $this->table,
                    $this->fields['parent'],
                    $parent_id
                );
            }
        } else {
            if ($nleft > 0 && $includeSelf) {
                $query = sprintf(
                    'SELECT %s FROM %s WHERE nleft >= %d AND nright <= %d ORDER BY nleft',
                    implode(',', $this->getFields()),
                    $this->table,
                    $nleft,
                    $nright
                );
            } elseif ($nleft > 0) {
                $query = sprintf(
                    'SELECT %s FROM %s WHERE nleft > %d AND nright < %d ORDER BY nleft',
                    implode(',', $this->getFields()),
                    $this->table,
                    $nleft,
                    $nright
                );
            } else {
                $query = sprintf(
                    'SELECT %s FROM %s ORDER BY nleft',
                    implode(',', $this->getFields()),
                    $this->table
                );
            }
        }
        $result = $this->connection->executeQuery($query);

        $arr = [];
        foreach ($result->fetchAll(\PDO::FETCH_OBJ) as $row) {
            if ($unique_id_list == false) {
                $arr[$row->$idField] = $row;
            } else {
                array_push($arr, $row->$idField);
            }
        }

        return $arr;
    }

    public function getNode($id)
    {
        $query = sprintf('SELECT %s FROM %s WHERE %s = %d', implode(',', $this->getFields()), $this->table, $this->fields['id'], $id);

        return $this->connection->executeQuery($query)->fetch(\PDO::FETCH_OBJ);
    }
}
