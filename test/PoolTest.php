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
        require_once 'TestMapper.php';
        require_once 'MultiMapper.php';
    }

    public function test1()
    {
        $pool = new \Wwtg99\DataPool\Common\DefaultDataPool('../example_conf.json');
        $this->assertNotNull($pool->getConnection('main'));
        $conn = $pool->getConnection('main');
        $this->assertEquals('main', $conn->getName());
        $mapper = $conn->getMapper('test\TestMapper');
        $this->assertNotNull($mapper);
        $re = $mapper->insert(['panel_id'=>100, 'name'=>'test1', 'descr'=>'test descr']);
        $this->assertEquals(1, $re);
        $re = $mapper->select('*', ['name'=>'test1']);
        $this->assertEquals(100, $re[0]['panel_id']);
        $re = $mapper->get(100, ['panel_id', 'name']);
        $this->assertEquals('test1', $re['name']);
        $re = $mapper->has(['name'=>'test1']);
        $this->assertTrue($re);
        $re = $mapper->has(['name'=>'test2']);
        $this->assertFalse($re);
        $re = $mapper->count(null, ['name'=>'test1']);
        $this->assertEquals(1, $re);
        $re = $mapper->update(['descr'=>'aaa'], ['panel_id'=>100]);
        $this->assertEquals(1, $re);
        $re = $mapper->get(null, 'descr', ['name'=>'test1']);
        $this->assertEquals('aaa', $re);
        $re = $mapper->delete(100);
        $this->assertEquals(1, $re);
        $re = $mapper->insert(['panel_id'=>200, 'name'=>'test2']);
        $this->assertEquals(1, $re);
        $re = $mapper->search('t', null, ['name']);
        $this->assertTrue($re !== false);
        $re = $conn->query(['select * from panels where panel_id = :id', ['id'=>200]]);
        $this->assertEquals('test2', $re[0]['name']);
        $re = $mapper->delete(null, ['name'=>'test2']);
        $this->assertEquals(1, $re);
        # multi primary key
        $mapper2 = $conn->getMapper('MultiMapper');
        $re = $mapper2->insert(['pid'=>'999', 'version'=>'1.0', 'name'=>'test1']);
        $this->assertEquals(1, $re);
        $re = $mapper2->select(['pid', 'version', 'name'], ['pid'=>'999']);
        $this->assertEquals('test1', $re[0]['name']);
        $re = $mapper2->search('t', ['pid', 'version', 'name']);
        $this->assertTrue($re !== false);
    }

    public function test2()
    {
        $pool = new \Wwtg99\DataPool\Common\DefaultDataPool('../example_conf.json');
        $this->assertNotNull($pool->getConnection('main'));
        $conn = $pool->getConnection('main');
        $mapper = $conn->getMapper('test\TestMapper');
        $this->assertNotNull($mapper);
        for ($i = 101; $i < 110; $i++) {
            $re = $mapper->insert(['panel_id'=>$i, 'name'=>'tt' . $i]);
        }
        $mapper->setContext(['page'=>2, 'page_size'=>2]);
        $re = $mapper->select();
        $this->assertEquals(2, count($re));
        $mapper->setContext(['limit'=>5, 'offset'=>2, 'order'=>'<panel_id']);
        $re = $mapper->select();
        $this->assertEquals(5, count($re));
    }

    public function testFormat()
    {
        //datetime
        $dateFields = [
            ['2016-01-10 10:12:15', null, '2016-01-10 10:12:15'],
            ['2016-01-10 10:12:15', 'Y-m-d', '2016-01-10'],
            ['2016-01-10 10:12:15', 'H:i:s', '10:12:15'],
            ['2016-07-05 16:59:53.086167+08', null, '2016-07-05 16:59:53'],
            ['2016-07-05 16:59:53.086167+08', 'Y-m-d', '2016-07-05'],
            ['2016-07-05 16:59:53.086167+08', 'H:i:s', '16:59:53'],
            [null, null, null],
        ];
        foreach ($dateFields as $dateField) {
            if ($dateField[1]) {
                $d = \Wwtg99\DataPool\Utils\FieldFormatter::formatDateTimeField($dateField[0], $dateField[1]);
                $this->assertEquals($dateField[2], $d);
            } else {
                $d = \Wwtg99\DataPool\Utils\FieldFormatter::formatDateTimeField($dateField[0]);
                $this->assertEquals($dateField[2], $d);
            }
        }
        $data = [
            ['f1'=>'aa', 'f2'=>'1999-05-06', 'f3_at'=>'2016-07-01 16:59:53.083112+08', 'created_at'=>'2016-07-25 16:59:00'],
            ['created_at'=>null, 'updated_at'=>'2016-07-25 16:59:00', 'name'=>'aag'],
        ];
        $data_exp = [
            ['f1'=>'aa', 'f2'=>'1999-05-06', 'f3_at'=>'2016-07-01 16:59:53', 'created_at'=>'2016-07-25 16:59:00'],
            ['created_at'=>null, 'updated_at'=>'2016-07-25 16:59:00', 'name'=>'aag'],
        ];
        $res = \Wwtg99\DataPool\Utils\FieldFormatter::formatDateTime($data);
        $this->assertEquals($data_exp, $res);
        //number
        $numFields = [
            ['100', 0, PHP_ROUND_HALF_UP, 100],
            ['100.123', 0, PHP_ROUND_HALF_UP, 100],
            [12.34, 0, PHP_ROUND_HALF_UP, 12],
            [12.34, 1, PHP_ROUND_HALF_UP, 12.3],
            [12.3456, 2, PHP_ROUND_HALF_UP, 12.35],
            ['23.689', 2, PHP_ROUND_HALF_UP, 23.69],
            ['23.685', 2, PHP_ROUND_HALF_DOWN, 23.68],
        ];
        foreach ($numFields as $numField) {
            $re = \Wwtg99\DataPool\Utils\FieldFormatter::formatNumberField($numField[0], $numField[1], $numField[2]);
            $this->assertEquals($numField[3], $re);
        }
        $data = [
            ['num'=>'123.45', 'numa'=>'12345.6', 'nuu'=>'aa'],
            ['num'=>12.3, 'numa'=>12.34, 'nuu'=>'aab'],
        ];
        $data_exp = [
            ['num'=>123, 'numa'=>'12345.6', 'nuu'=>'aa'],
            ['num'=>12, 'numa'=>12.34, 'nuu'=>'aab'],
        ];
        $res = \Wwtg99\DataPool\Utils\FieldFormatter::formatNumber($data);
        $this->assertEquals($data_exp, $res);
        $data1 = [
            ['num'=>'12.345', 'val'=>56.78, 'created_at'=>'2016-12-12 10:12:14', 'name'=>'aa'],
            ['num'=>'12.345', 'val'=>'56.78', 'created_at'=>'2016-12-12 10:12:14.089176+08', 'name'=>'aa'],
        ];
        $setting1 = [
            'format_datetime'=>['format'=>'Y-m-d'],
            'format_number'=>['fields'=>['num', 'val'], 'precision'=>2]
        ];
        $data_exp1 = [
            ['num'=>12.35, 'val'=>56.78, 'created_at'=>'2016-12-12', 'name'=>'aa'],
            ['num'=>12.35, 'val'=>56.78, 'created_at'=>'2016-12-12', 'name'=>'aa'],
        ];
        \Wwtg99\DataPool\Utils\FieldFormatter::formatFields($data1, $setting1);
        $this->assertEquals($data_exp1, $data1);
        $data2 = [
            ['num'=>'12.345', 'val'=>56.78, 'created_at'=>'2016-12-12 10:12:14', 'name'=>'aa'],
            ['num'=>'12.345', 'val'=>'56.78', 'created_at'=>'2016-12-12 10:12:14.089176+08', 'name'=>'aa'],
        ];
        $setting2 = [
            'format_datetime'=>[],
            'format_number'=>[]
        ];
        $data_exp2 = [
            ['num'=>12, 'val'=>56.78, 'created_at'=>'2016-12-12 10:12:14', 'name'=>'aa'],
            ['num'=>12, 'val'=>'56.78', 'created_at'=>'2016-12-12 10:12:14', 'name'=>'aa'],
        ];
        \Wwtg99\DataPool\Utils\FieldFormatter::formatFields($data2, $setting2);
        $this->assertEquals($data_exp2, $data2);
    }
}
