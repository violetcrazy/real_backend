 <?php
$parameters = array();

$parameters = array(
    'db' => array(
        'debug' => false,
        'host' => 'localhost',
        'port' => 3306,
        'username' => 'jinn_new',
        'password' => 'Lzx8Qa5NERyuHyZe',
        'dbname' => 'jinn_new',
        'charset' => 'utf8'
    ),
    'db_slave' => array(
        'debug' => false,
        'host' => 'localhost',
        'port' => 3306,
        'username' => 'jinn_new',
        'password' => 'Lzx8Qa5NERyuHyZe',
        'dbname' => 'jinn_new',
        'charset' => 'utf8'
    ),
    'application' => array(
        'protocol' => 'http://',
        'base_url' => 'http://adminjinn.codex4u.com/',
        'frontend_url' => 'http://jinn.codex4u.com/',
        'api_url' => 'http://apijinn.codex4u.com/',
        'token' => '89d5c70f11e35817110eeeb7863e3ef0',
        'secret' => '88458287415cd6ca24b41bcbe7716e91',
        'cookie_key' => 'jinn_admin',
        'pagination_limit' => 15,
        'template_excel_project' => 'http://adminjinn.codex4u.com/asset/download/new_template_project.xlsx',
        'template_excel_block' => 'http://adminjinn.codex4u.com/asset/download/new_template_block.xlsx',
        'template_excel_apartment' => 'http://adminjinn.codex4u.com/asset/download/new_template_apartment.xlsx'
    ),
    'volt' => array(
        'debug' => true,
        'stat' => true,
        'compiled_separator' => '%'
    ),
    'mailer' => array(
        'delivery' => true,
        'ssl' => false,
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => ''
    ),
    'cdn' => array(
        'upload_media_url' => 'http://cdnjinn.codex4u.com/upload',
        'upload_image_url' => 'http://cdnjinn.codex4u.com/image/upload',
        'delete_image_url' => 'http://cdnjinn.codex4u.com/image/delete',
        'dir_upload' => 'http://cdnjinn.codex4u.com/asset/frontend/upload/stories/'
    ),
    'asset' => array(
        'download' => 'http://adminjinn.codex4u.com/asset/download/',
        'version' => '201607140950',
        'mobile_version' => '201607140950',
        'frontend_url' => 'http://cdnjinn.codex4u.com/asset/frontend/',
        'backend_url' => 'http://cdnjinn.codex4u.com/asset/backend/'
    ),
    'cache' => array(
        'lifetime' => 600,
        'prefix' => '_jinn_admin_',
        'type' => 'apc',
        'memcache' => array(
            'host' => '127.0.0.1',
            'port' => '11211',
            'persistent' => false
        ),
        'redis' => array(
            'host' => '127.0.0.1',
            'port' => '6379',
            'auth' => 'redis',
            'persistent' => false
        ),
        'metadata' => array(
            'prefix' => 'jinn_',
            'lifetime' => 31536000
        )
    )
);
