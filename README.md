# Data Pool

### A collection of data connections for data mappers.

### Usage

- Configure conf file
```
{
  "log_dir": "log",
  "debug": true,
  "connections": [
    {
      "name": "main",
      "class": "DataPool\\Connections\\DatabaseConnection",
      "mapper_path": "",
      "database": {
        "driver": "pgsql",
        "dbname": "test2",
        "host": "192.168.0.21",
        "username": "genobase",
        "password": "genobase",
        "port": 5432
      },
      "logger": {
        "level": "DEBUG",
        "title": "main.log",
        "max_logfile": 5
      }
    }
  ]
}
```
- Use conf file to create data pool
```
$pool = new DefaultDataPool('example_conf.json');
```
- Get connection
```
$conn = $pool->getConnection('main');
```
- Get data mapper for query
```
$mapper = $conn->getMapper('test\TestMapper'); //mapper should implement IDataMapper
$re = $mapper->select('*', ['panel__id'=>'1']);
var_dump($re);
```
- Use pagination
```
$mapper->setContext(['page'=>2, 'page_size'=>10]);
$re = $mapper->search('1', ['name']);
var_dump($re);
```

### Methods to query
- select($select, $where)
- get($key, $select, $where)
- insert($data)
- update($data, $where)
- delete($key, $where)
- has($where)
- count($select, $where)
- search($term, $select, $fields)
