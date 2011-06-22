<?php
$component_name = 'BugTracker';

/**
 * Connection details
 * Driver options:
 * pdo_mysql: A MySQL driver that uses the pdo_mysql PDO extension.
 * pdo_sqlite: An SQLite driver that uses the pdo_sqlite PDO extension.
 * pdo_pgsql: A PostgreSQL driver that uses the pdo_pgsql PDO extension.
 * pdo_oci: An Oracle driver that uses the pdo_oci PDO extension. Note that this driver caused problems in our tests. Prefer the oci8 driver if possible.
 * pdo_sqlsrv: An MSSQL driver that uses pdo_sqlsrv PDO
 * oci8:` An Oracle driver that uses the oci8 PHP extension.
 */
$connection = array(
  'driver' => 'pdo_mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => 'freedom35',
  'dbname' => 'doctrine_test',
);



/**
 * 
 * It should not be necessary to edit below this line.
 * 
 * This boilerplate Doctrine init code should work for any Cumula MVC Component.
 * 
 * @todo Make this work correctly with Doctrine cli (DCLI will run, but many 
 *   commands will fail because paths get screwy)
 * 
 */

use Doctrine\ORM\EntityManager,
  Doctrine\ORM\Configuration,
  Doctrine\DBAL\Logging;


if (defined('STDIN')) {
  chdir('../../classes');
  require_once 'Doctrine/Common/ClassLoader.php';
} else {
  require_once 'core/classes/Doctrine/Common/ClassLoader.php';
}

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine', realpath('.').'/core/classes/');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('models\\'.$component_name, __DIR__);
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('proxies\\'.$component_name, __DIR__);
$classLoader->register();

$env = (defined('STDIN'))
  ? 'development'
  : Application::getSystemConfig()->getValue('setting_environment', ENV_DEVELOPMENT);

$cache = ($env == 'development')
  ? new \Doctrine\Common\Cache\ArrayCache
  : new \Doctrine\Common\Cache\ApcCache;

$config = new Configuration;

$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

$driverImpl = $config->newDefaultAnnotationDriver('models');
$config->setMetadataDriverImpl($driverImpl);

$config->setProxyDir('proxies');
$config->setProxyNamespace('proxies\\'.$component_name);

$config->setAutoGenerateProxyClasses(($env == 'development'));

//$logger = new Logging\EchoSQLLogger;
$logger = new Logging\DebugStack;
$config->setSQLLogger($logger);
//$config->getSQLLogger();

$evm = new Doctrine\Common\EventManager();
$em = EntityManager::create($connection, $config, $evm);
