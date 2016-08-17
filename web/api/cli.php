<?php
date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', true);
error_reporting(E_ALL);

try {
    define('ROOT', realpath(dirname(dirname(dirname(__FILE__)))));

    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        ROOT . '/app/api/task/'
    ));
    $loader->register();

    $loader->registerNamespaces(array(
        'MBN\Data\Lib' => ROOT . '/app/data/lib/',
        'MBN\Data\Model' => ROOT . '/app/data/model/'
    ))->register();

    require_once ROOT . '/app/api/config/parameter.php';
    $config = new \Phalcon\Config($parameter);

    $di = new \Phalcon\DI\FactoryDefault\CLI();
    $di->setShared('config', $config);

    $di->setShared('db', function () use ($config) {
        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            'host' => $config->db->host,
            'port' => $config->db->port,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'dbname' => $config->db->dbname,
            'charset' => $config->db->charset
        ));

        if ($config->db->debug) {
            $e = new \Phalcon\Events\Manager();
            $logger = new \Phalcon\Logger\Adapter\File(ROOT . '/log/api_db_master.log');

            $e->attach('db', function ($event, $connection) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $sql = $connection->getSQLVariables();

                    if (count($sql)) {
                      $logger->log($connection->getSQLStatement() . ' ' . join(', ', $sql), \Phalcon\Logger::INFO);
                    } else {
                      $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
                    }
                }
            });
            $connection->setEventsManager($e);
        }

        return $connection;
    });

    $di->setShared('db_slave', function () use ($config) {
        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            'host' => $config->db_slave->host,
            'port' => $config->db->port,
            'username' => $config->db_slave->username,
            'password' => $config->db_slave->password,
            'dbname' => $config->db_slave->dbname,
            'charset' => $config->db_slave->charset
        ));

        if ($config->db_slave->debug) {
            $e = new \Phalcon\Events\Manager();
            $logger = new \Phalcon\Logger\Adapter\File(ROOT . '/log/api_db_slave.log');

            $e->attach('db', function ($event, $connection) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $sql = $connection->getSQLVariables();

                    if (count($sql)) {
                      $logger->log($connection->getSQLStatement() . ' ' . join(', ', $sql), \Phalcon\Logger::INFO);
                    } else {
                      $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
                    }
                }
            });
            $connection->setEventsManager($e);
        }

        return $connection;
    });

    if ($config->cache->type == 'memcache') {
        $di->setShared('cache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Memcache($data_cache, array(
                'host' => $config->cache->memcache->host,
                'port' => $config->cache->memcache->port,
                'persistent' => $config->cache->memcache->persistent
            ));
            return $cache;
        });
    } elseif ($config->cache->type == 'redis') {
        $di->setShared('cache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Redis($data_cache, array(
                'host' => $config->cache->redis->host,
                'port' => $config->cache->redis->port,
                'auth' => $config->cache->redis->auth,
                'persistent' => $config->cache->redis->persistent
            ));
            return $cache;
        });
    } else {
        $di->setShared('cache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Apc($data_cache, array(
            ));
            return $cache;
        });
    }

    $di->setShared('modelsMetadata', function() use ($config) {
        $meta_data = new \Phalcon\Mvc\Model\MetaData\Files(array(
            'metaDataDir' => ROOT . '/cache/data/metadata/',
            'prefix' => $config->cache->metadata->prefix,
            'lifetime' => $config->cache->metadata->lifetime
        ));
        return $meta_data;
    });

    $di->setShared('logger', function() {
        $logger = new \Phalcon\Logger\Adapter\File(ROOT . '/log/api_debug.log');
        return $logger;
    });

    $console = new \Phalcon\CLI\Console();
    $console->setDI($di);

    $arguments = array();
    $params = array();

    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
           $params[] = $arg;
        }
    }

    $arguments['params'] = $params;
    $console->handle($arguments);
} catch (\Exception $e) {
    echo $e->getMessage();
} catch (\PDOException $e) {
    echo $e->getMessage();
}