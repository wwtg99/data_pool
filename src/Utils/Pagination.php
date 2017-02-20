<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/20
 * Time: 13:06
 */

namespace Wwtg99\DataPool\Utils;


class Pagination
{

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $pageSize = 100;

    /**
     * @param int $page
     * @param int $pageSize
     * @param int $toPage
     * @return Pagination
     */
    public static function createFromPage($page, $pageSize = 100, $toPage = null)
    {
        if ($toPage) {
            $limit = $toPage * $pageSize;
        } else {
            $limit = $pageSize;
        }
        $offset = ($page - 1) * $pageSize;
        $p = new Pagination($limit, $offset);
        $p->pageSize = $pageSize;
        return $p;
    }

    /**
     * Pagination constructor.
     * @param int $limit
     * @param int $offset
     */
    public function __construct($limit, $offset)
    {
        $this->setLimit($limit);
        $this->setOffset($offset);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        if ($limit < 0) {
            $limit = 0;
        }
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        if ($offset < 0) {
            $offset = 0;
        }
        $this->offset = $offset;
    }

    /**
     * @return Pagination
     */
    public function nextPage()
    {
        $this->offset += $this->pageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->offset + $this->limit;
    }

    /**
     * @param int $count
     * @return bool
     */
    public function exceed($count)
    {
        if ($this->offset >= $count) {
            return true;
        }
        return false;
    }
}