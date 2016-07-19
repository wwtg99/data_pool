<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/20
 * Time: 12:14
 */
class MultiMapper extends \Wwtg99\DataPool\Mappers\ArrayMapper
{

    protected $name = 'phenotype_all';

    protected $key = ['phenotype_id', 'rule_version'];

    /**
     * @field phenotype_id text primary key
     * @field rule_version text primary key
     * @field name text
     */
}