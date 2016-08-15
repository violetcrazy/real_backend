<?php
$parameters = array();

$parameters = array(
    'db' => array(
        'debug'    => false,
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => '',
        'dbname'   => 'web_land',
        'charset'  => 'utf8'
    ),
    'db_slave' => array(
        'debug'    => false,
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => '',
        'dbname'   => 'web_land',
        'charset'  => 'utf8'
    ),
    'application' => array(
        'protocol'                 => 'http://',
        'base_url'                 => 'http://localhost.admin.land.com/',
        'frontend_url'             => 'http://localhost.land.com/',
        'api_url'                  => 'http://localhost.api.land.com/',
        'token'                    => '89d5c70f11e35817110eeeb7863e3ef0',
        'secret'                   => '88458287415cd6ca24b41bcbe7716e91',
        'cookie_key'               => 'land_admin',
        'pagination_limit'         => 15,
        'template_excel_project'   => 'http://localhost.admin.land.com/asset/download/new_template_project.xlsx',
        'template_excel_block'     => 'http://localhost.admin.land.com/asset/download/new_template_block.xlsx',
        'template_excel_apartment' => 'http://localhost.admin.land.com/asset/download/new_template_apartment.xlsx',
        'session_domain' => '.land.com',
        'session_name' => 'land'
    ),
    'volt' => array(
        'debug'              => true,
        'stat'               => true,
        'compiled_separator' => '%'
    ),
    'mailer' => array(
        'delivery' => true,
        'ssl'      => true,
        'host'     => 'smtp.gmail.com',
        'port'     => '465',
        'username' => 'noreplyestatevn@gmail.com',
        'password' => 'zxcvbnm,./@#'
    ),
    'cdn' => array(
        'upload_media_url' => 'http://localhost.cdn.land.com/upload',
        'upload_image_url' => 'http://localhost.cdn.land.com/image/upload',
        'delete_image_url' => 'http://localhost.cdn.land.com/image/delete',
        'dir_upload'       => 'http://localhost.cdn.land.com/uploads/',
        'file_upload'      => 'http://localhost.cdn.land.com/',
        'file_url'         => 'http://localhost.cdn.land.com/uploads/'
    ),
    'asset' => array(
        'download'       => 'http://localhost.admin.land.com/asset/download/',
        'version'        => '20160813_0024',
        'mobile_version' => '20160813_0024',
        'frontend_url'   => 'http://localhost.cdn.land.com/asset/frontend/',
        'backend_url'    => 'http://localhost.cdn.land.com/asset/backend/'
    ),
    'cache' => array(
        'lifetime' => 300,
        'prefix'   => '_land_admin_',
        'type'     => 'apc',
        'memcache' => array(
            'host'       => '127.0.0.1',
            'port'       => '11211',
            'persistent' => false
        ),
        'redis' => array(
            'host'       => '127.0.0.1',
            'port'       => '6379',
            'auth'       => 'redis',
            'persistent' => false
        ),
        'metadata' => array(
            'prefix'   => 'land_',
            'lifetime' => 31536000
        )
    )
);
