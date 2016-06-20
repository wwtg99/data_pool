<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/20
 * Time: 10:58
 */

namespace test;


use DataPool\Mappers\ArrayMapper;

class TestMapper extends ArrayMapper
{

    protected $name = 'panels';

    protected $key = 'panel_id';

    /**
     * @field panel_id int primary key
     * @field name text
     * @field descr text
     */
}