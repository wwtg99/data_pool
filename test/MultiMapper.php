<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/20
 * Time: 12:14
 */
class MultiMapper extends \Wwtg99\DataPool\Mappers\ArrayMapper
{

    protected $name = 'phenotypes';

    protected $key = ['pid', 'version'];

    /**
     * @field pid text primary key
     * @field version text primary key
     * @field name text
     */
}