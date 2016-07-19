<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/17
 * Time: 16:53
 */
class PoolTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        date_default_timezone_set("Asia/Shanghai");
        require_once '../vendor/autoload.php';
        $loader = new \Wwtg99\ClassLoader\Loader(__DIR__ . DIRECTORY_SEPARATOR . '..', [['Wwtg99\\DataPool', 'src', true]]);
        $loader->autoload();
        require_once 'TestMapper.php';
        require_once 'MultiMapper.php';
    }

    public function test1()
    {
        $pool = new \Wwtg99\DataPool\Common\DefaultDataPool('../example_conf.json');
        $this->assertNotNull($pool->getConnection('main'));
        $conn = $pool->getConnection('main');
        $this->assertEquals('main', $conn->getName());
        $re = $conn->query(['select * from panels where panel_id = :id', ['id'=>1]]);
        $this->assertEquals('HealthWise', $re[0]['name']);
        $mapper = $conn->getMapper('test\TestMapper');
        $this->assertNotNull($mapper);
        $re = $mapper->select('*', ['name'=>'BabyWise']);
        $this->assertEquals(5, $re[0]['panel_id']);
        $re = $mapper->get(1, ['panel_id', 'name']);
        $this->assertEquals('HealthWise', $re['name']);
        $re = $mapper->has(['name'=>'BabyWise']);
        $this->assertTrue($re);
        $re = $mapper->count(null, ['name'=>'BabyWise']);
        $this->assertEquals(1, $re);
        $re = $mapper->insert(['panel_id'=>100, 'name'=>'test1', 'descr'=>'test descr']);
        $this->assertEquals(1, $re);
        $re = $mapper->update(['descr'=>'aaa'], ['panel_id'=>100]);
        $this->assertEquals(1, $re);
        $re = $mapper->delete(100);
        $this->assertEquals(1, $re);
        $re = $mapper->insert(['panel_id'=>200, 'name'=>'test2']);
        $this->assertEquals(1, $re);
        $re = $mapper->delete(null, ['name'=>'test2']);
        $this->assertEquals(1, $re);
        $re = $mapper->search('H', null, ['name']);
        $this->assertTrue($re !== false);
        $mapper2 = $conn->getMapper('MultiMapper');
        $re = $mapper2->select(['phenotype_id', 'rule_version', 'name'], ['phenotype_id'=>'1101']);
        $this->assertEquals('1101', $re[0]['phenotype_id']);
        $re = $mapper2->search('2', ['phenotype_id', 'rule_version', 'name']);
        $this->assertTrue($re !== false);
        $mapper2->setContext(['page'=>2, 'page_size'=>10]);
        $re = $mapper2->search('1', ['phenotype_id', 'rule_version', 'name']);
        $this->assertTrue($re !== false);
    }
}
