<?php
$parameter = array();

$parameter = array(
    'db' => array(
        'debug'    => false,
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'jinn_new',
        'password' => 'Lzx8Qa5NERyuHyZe',
        'dbname'   => 'jinn_new',
        'charset'  => 'utf8'
    ),
    'db_slave' => array(
        'debug'    => false,
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'jinn_new',
        'password' => 'Lzx8Qa5NERyuHyZe',
        'dbname'   => 'jinn_new',
        'charset'  => 'utf8'
    ),
    'application' => array(
        'protocol'         => 'http://',
        'base_url'         => 'http://apijinn.codex4u.com/',
        'frontend_url'     => 'http://jinn.codex4u.com/',
        'token'            => '89d5c70f11e35817110eeeb7863e3ef0',
        'secret'           => '88458287415cd6ca24b41bcbe7716e91',
        'cookie_key'       => 'jinn_api',
        'pagination_limit' => 15
    ),
    'volt' => array(
        'debug'              => true,
        'stat'               => true,
        'compiled_separator' => '%'
    ),
    'mailer' => array(
        'delivery' => true,
        'ssl'      => false,
        'host'     => '210.211.116.92',
        'port'     => 26,
        'username' => '',
        'password' => ''
    ),
    'cdn' => array(
        'upload_image_url' => 'http://cdnjinn.codex4u.com/image/upload',
        'delete_image_url' => 'http://cdnjinn.codex4u.com/image/delete',
        'dir_upload'       => 'http://cdnjinn.codex4u.com/uploads/',
        'icon_image_url'   => 'http://cdnjinn.codex4u.com/asset/frontend/upload/icon/'
    ),
    'asset' => array(
        'version'      => '20161024_1502',
        'frontend_url' => 'http://cdnjinn.codex4u.com/asset/frontend/',
        'backend_url'  => 'http://cdnjinn.codex4u.com/asset/backend/',
        'icon_default' => 'http://cdnjinn.codex4u.com/asset/frontend/img/no-ic.png',
    ),
    'cache' => array(
        'lifetime' => 600,
        'prefix'   => '_jinn_api_',
        'type'     => 'apc',
        'memcache' => array(
            'host'       => '127.0.0.1',
            'port'       => 11211,
            'persistent' => false
        ),
        'redis' => array(
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'auth'       => 'redis',
            'persistent' => false
        ),
        'metadata' => array(
            'prefix'   => 'jinn_',
            'lifetime' => 31536000
        )
    )
);
