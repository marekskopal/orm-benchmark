<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->initDatabaseMapFromDumps(array (
  'default' => 
  array (
    'tablesByName' => 
    array (
      'addresses' => '\\MarekSkopal\\ORMBenchmark\\Propel\\generated\\Map\\AddressTableMap',
      'users' => '\\MarekSkopal\\ORMBenchmark\\Propel\\generated\\Map\\UserTableMap',
    ),
    'tablesByPhpName' => 
    array (
      '\\Address' => '\\MarekSkopal\\ORMBenchmark\\Propel\\generated\\Map\\AddressTableMap',
      '\\User' => '\\MarekSkopal\\ORMBenchmark\\Propel\\generated\\Map\\UserTableMap',
    ),
  ),
));
