<?php
$router->add('/authenticate{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'authen',
    'action' => 'createAuthorizedToken'
))->setName('authenticate');

// Home ---------
$router->add('/home/file-json{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'fileJson'
))->setName('home_file_json');

$router->add('/location/list', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'locationList'
))->setName('location_list');

$router->add('/home/category-list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'categoryList'
))->setName('home_category_list');

$router->add('/home/category-detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'categoryDetail'
))->setName('home_category_detail');

$router->add('/home/attribute-list', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'attributeList'
))->setName('home_attribute_list');

$router->add('/home/apartment-value', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'apartmentValue'
))->setName('home_apartment_value');

$router->add('/category/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'categoryDetail'
))->setName('category_detail');
// ---------Home

// --------- User
$router->add('/user/register', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'register'
))->setName('user_register');

$router->add('/user/login', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'login'
))->setName('user_login');

$router->add('/user/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'detail'
))->setName('user_detail');

$router->add('/user/profile{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'profile'
))->setName('user_profile');

$router->add('/user/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'list'
))->setName('user_list');

$router->add('/user/add{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'add'
))->setName('user_add');

$router->add('/user/forgot-password{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'forgotPassword'
))->setName('user_forgot_password');

$router->add('/user/upload-image', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'uploadImage'
))->setName('user_upload_image');

$router->add('/user/delete-image', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'deleteImage'
))->setName('user_delete_image');

$router->add('/user/setting{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'setting'
))->setName('user_setting');

$router->add('/user/upload-avatar{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'uploadAvatar'
))->setName('user_upload_avatar');

$router->add('/user/save-bookmark{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'saveBookmark'
))->setName('user_save_bookmark');

$router->add('/user/save-search{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'saveSearch'
))->setName('user_save_search');

$router->add('/user/list-save-search{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'listSaveSearch'
))->setName('user_list_save_search');

$router->add('/user/delete-bookmark{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'deleteBookmark'
))->setName('user_delete_bookmark');

$router->add('/user/save-home{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'saveHome'
))->setName('user_save_home');

$router->add('/user/add-contact{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'addContact'
))->setName('user_add_contact');

$router->add('/user/delete-contact{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'deleteContact'
))->setName('user_delete_contact');

$router->add('/user/list-contact{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'user',
    'action' => 'listContactByUser'
))->setName('user_list_contact');
// User ---------

// --------- Project
$router->add('/project/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'project',
    'action' => 'list'
))->setName('project');

$router->add('/project/all{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'project',
    'action' => 'all'
))->setName('project_all');

$router->add('/project/add', array(
    'module' => 'api',
    'controller' => 'project',
    'action' => 'add'
))->setName('project_add');

$router->add('/project/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'project',
    'action' => 'detail'
))->setName('project_detail');

$router->add('/project/full{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'project',
    'action' => 'full'
))->setName('project_full');

$router->add('/project/other-project{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'project',
    'action' => 'otherProject'
))->setName('project_other');
// Project ---------

// --------- Block
$router->add('/block/all{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'all'
))->setName('block_all');

$router->add('/block/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'list'
))->setName('block');

$router->add('/block/list-by-project{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'listByProject'
))->setName('block_list_by_project');

$router->add('/block/add', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'add'
))->setName('block_add');

$router->add('/block/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'detail'
))->setName('block_detail');

$router->add('/block/full{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'full'
))->setName('block_full');

$router->add('/block/full-ajax{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'block',
    'action' => 'fullAjax'
))->setName('block_full_ajax');
// Block ---------

// --------- Apartment
$router->add('/apartment/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'list'
))->setName('apartment');

$router->add('/apartment/list-by-block{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'listByBlock'
))->setName('apartment_list_by_block');

$router->add('/apartment/add', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'add'
))->setName('apartment_add');

$router->add('/apartment/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'detail'
))->setName('apartment_detail');

$router->add('/apartment/full{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'full'
))->setName('apartment_full');

$router->add('/apartment/ceriterial{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'ceriterial'
))->setName('apartment_ceriterial');

$router->add('/apartment/request{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment',
    'action' => 'request'
))->setName('apartment_request');
// Apartment ---------

// --------- Article
$router->add('/article/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'article',
    'action' => 'list'
))->setName('article');

$router->add('/article/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'article',
    'action' => 'detail'
))->setName('article_detail');
// --------- Article

// --------- Map image
$router->add('/map-image/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map_image',
    'action' => 'list'
))->setName('map_image');

$router->add('/map-image/full{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map_image',
    'action' => 'full'
))->setName('map_full');

$router->add('/map-image/add{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map_image',
    'action' => 'add'
))->setName('map_image_add');

$router->add('/map-image/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map_image',
    'action' => 'detail'
))->setName('map_image_detail');

$router->add('/map-image/detail-image-view{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map_image',
    'action' => 'detaiImageView'
))->setName('map_detail_image_view');

$router->add('/map-image/detail-plan-view{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map_image',
    'action' => 'detaiPlanView'
))->setName('map_detail_plan_view');
// Map image ---------

// --------- Attribute
$router->add('/attribute/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'attribute',
    'action' => 'list'
))->setName('attribute');

$router->add('/attribute/add{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'attribute',
    'action' => 'add'
))->setName('attribute_add');

$router->add('/attribute/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'attribute',
    'action' => 'detail'
))->setName('attribute_detail');

$router->add('/attribute/list-by-project{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'attribute',
    'action' => 'listByProject'
))->setName('attribute_list_by_project');
// Attribute ---------

// --------- Apartment gallery
$router->add('/apartment/gallery/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment_gallery',
    'action' => 'list'
))->setName('apartment_gallery');

$router->add('/apartment/gallery/add{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment_gallery',
    'action' => 'add'
))->setName('apartment_gallery_add');

$router->add('/apartment/gallery/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'apartment_gallery',
    'action' => 'detail'
))->setName('apartment_gallery_detail');
// Apartment gallery ---------

// --------- Map
$router->add('/map/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map',
    'action' => 'list'
))->setName('map');

$router->add('/map/add{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map',
    'action' => 'add'
))->setName('map_add');

$router->add('/map/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map',
    'action' => 'detail'
))->setName('map_detail');

$router->add('/map/list-by-map-image{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'map',
    'action' => 'listByMapImage'
))->setName('map_list_by_map_image');
// Map ---------

// --------- Check
$router->add('/check-phone-exists{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'checkPhoneExists'
))->setName('check_phone_exists');

$router->add('/check-email-exists{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'home',
    'action' => 'checkEmailExists'
))->setName('check_email_exists');
// Check ---------

// --------- Search
$router->add('/search{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'search',
    'action' => 'index'
))->setName('search');
// Search ---------

// --------- Message
$router->add('/message/send{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'message',
    'action' => 'send'
))->setName('message_send');

$router->add('/message/count-by-id{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'message',
    'action' => 'countMessageById'
))->setName('message_count_by_id');

$router->add('/message/list{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'message',
    'action' => 'list'
))->setName('message_list');

$router->add('/message/detail{query:(/.*)*}', array(
    'module' => 'api',
    'controller' => 'message',
    'action' => 'detail'
))->setName('message_detail');
// Message ---------

// --------- Option
$router->add('/option/add', array(
    'module' => 'api',
    'controller' => 'option',
    'action' => 'add'
))->setName('option_add');

$router->add('/option/list', array(
    'module' => 'api',
    'controller' => 'option',
    'action' => 'list'
))->setName('option_list');

$router->add('/option/detail', array(
    'module' => 'api',
    'controller' => 'option',
    'action' => 'detail'
))->setName('option_detail');
//Option ---------

// Upload ---
$router->add('/media-list', array(
    'module' => 'api',
    'controller' => 'media',
    'action' => 'getListMedia'
))->setName('media_list');

$router->add('/media-detail', array(
    'module' => 'api',
    'controller' => 'media',
    'action' => 'getInfoMedia'
))->setName('media_detail');

//folder
$router->add('/folder-list', array(
    'module' => 'api',
    'controller' => 'media_term',
    'action' => 'getListMediaTerm'
))->setName('mediaterm_list');

$router->add('/folder-detail', array(
    'module' => 'api',
    'controller' => 'media_term',
    'action' => 'getInfoMediaTerm'
))->setName('mediaterm_detail');
// --- Upload


// Category --------
$router->add('/category/list', array(
    'module' => 'api',
    'controller' => 'category',
    'action' => 'categoryList'
))->setName('category_list');
// ------- Category

$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->notFound(array(
    'module' => 'api',
    'controller' => 'error',
    'action' => 'error404'
));
