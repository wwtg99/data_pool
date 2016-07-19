<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 19:40
 */

namespace Wwtg99\DataPool\Engines;


class MedooPlus extends \medoo
{
    /**
     * @var array
     */
    protected $lastError = [];

    /**
     * @var string
     */
    protected $lastSql = '';

    /**
     * MedooPlus constructor.
     * @param array $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * @param string $table
     * @param array $join
     * @param string|array $columns
     * @param array $where
     * @return array|bool
     *
     * Or,
     * @param string $table
     * @param string|array $columns
     * @param array $where
     * @return array|bool
     */
    public function select($table, $join, $columns = null, $where = null)
    {
        $re = parent::select($table, $join, $columns, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        return $re;
    }

    /**
     * @param string $table
     * @param array $datas
     * @return bool|int
     */
    public function insert($table, $datas)
    {
        $n = 0;
        if (isset($datas[0])) {
            foreach ($datas as $data) {
                parent::insert($table, $data);
                $this->lastSql = $this->last_query();
                $this->lastError = $this->error();
                if ($this->lastError[0] == '00000') {
                    $n += 1;
                }
            }
        } else {
            parent::insert($table, $datas);
            $this->lastSql = $this->last_query();
            $this->lastError = $this->error();
            if ($this->lastError[0] == '00000') {
                $n = 1;
            }
        }
        return $n;
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool|int
     */
    public function update($table, $data, $where = null)
    {
        $re = parent::update($table, $data, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        return $re;
    }

    /**
     * @param string $table
     * @param array $where
     * @return bool|int
     */
    public function delete($table, $where)
    {
        $re = parent::delete($table, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        return $re;
    }

    /**
     * @param string $table
     * @param array $join
     * @param string|array $column
     * @param array $where
     * @return bool|array
     *
     * Or,
     * @param string $table
     * @param string|array $column
     * @param array $where
     * @return bool|array
     */
    public function get($table, $join = null, $column = null, $where = null)
    {
        $re = parent::get($table, $join, $column, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        return $re;
    }

    /**
     * @param string $table
     * @param array $join
     * @param array $where
     * @return bool
     *
     * Or,
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function has($table, $join, $where = null)
    {
        $column = null;
        $re = $this->query('SELECT EXISTS(' . $this->select_context($table, $join, $column, $where, 1) . ')');
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re) {
            return $re->fetchColumn();
        } else {
            return false;
        }
    }

    /**
     * @param string $table
     * @param array $join
     * @param string|array $column
     * @param array $where
     * @return bool|int
     *
     * Or,
     * @param string $table
     * @param array $where
     * @return bool|int
     */
    public function count($table, $join = null, $column = null, $where = null)
    {
        $re = parent::count($table, $join, $column, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        return $re;
    }

    /**
     * @return bool
     */
    public function begin()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * @param string $query
     * @param array $data
     * @return array|bool
     */
    public function queryAll($query, $data = [])
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executeStatement($stmt, $data);
        if ($stmt) {
            return $stmt->fetchAll();
        }
        return false;
    }

    /**
     * @param string $query
     * @param array $data
     * @return array|bool
     */
    public function queryOne($query, $data = [])
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executeStatement($stmt, $data);
        if ($stmt) {
            return $stmt->fetch();
        }
        return false;
    }

    /**
     * @param string $query
     * @param array $data
     * @return int|bool
     */
    public function execute($query, $data = [])
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executeStatement($stmt, $data);
        if ($stmt) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * @param string $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        $query = str_replace(';', ' ', $query);
        $statement = $this->pdo->prepare($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        return $statement;
    }

    /**
     * @param \PDOStatement $statement
     * @param array $data
     * @return bool|\PDOStatement
     * @throws \Exception
     */
    public function executeStatement(\PDOStatement $statement, array $data = array())
    {
        $this->bindValue($statement, $data);
        $re = $statement->execute();
        $this->lastSql = $statement->queryString;
        $this->lastError = $statement->errorInfo();
        if (!$re) {
            return false;
        }
        return $statement;
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @return string
     */
    public function getLastSql()
    {
        return $this->lastSql;
    }

    /**
     * @param \PDOStatement $statement
     * @param array $data  [key1=>[value1, type1], key1=>value2, ...]
     */
    private function bindValue(\PDOStatement $statement, array $data)
    {
        if ($statement) {
            foreach ($data as $k => $v) {
                if (is_array($v) && count($v) > 1) {
                    $statement->bindValue($k, $v[0], $v[1]);
                } else {
                    $statement->bindValue($k, $v);
                }
            }
        }
    }
}