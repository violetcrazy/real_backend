<?php
$router->add('/', array(
    'module'     => 'admin',
    'controller' => 'home',
    'action'     => 'index'
))->setName('home');

$router->add('/user/login{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'login'
))->setName('user_login');

$router->add('/user/logout', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'logout'
))->setName('user_logout');

// --------- User Admin
$router->add('/user/super-admin-list{query:(/.*)*}', [
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'superAdminList'
])->setName('userSuperAdminList');

$router->add('/user/admin-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'adminList'
))->setName('userAdminList');

$router->add('/user/admin-editor-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'adminEditorList'
))->setName('userAdminEditorList');

$router->add('/user/admin-seo-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'adminSeoList'
))->setName('userAdminSeoList');

$router->add('/user/admin-sale-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'adminSaleList'
))->setName('userAdminSaleList');

$router->add('/user/add-admin{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'addAdmin'
))->setName('user_add_admin');

$router->add('/user/edit-admin{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'editAdmin'
))->setName('user_edit_admin');

$router->add('/user/delete-admin{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user_admin',
    'action'     => 'deleteAdmin'
))->setName('user_delete_admin');
// User Admin ---------

// --------- User
$router->add('/user/profile', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'profile'
))->setName('user_profile');

$router->add('/user/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'list'
))->setName('user_list');

$router->add('/user/agent-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'agentList'
))->setName('userAgentList');

$router->add('/user/member-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'memberList'
))->setName('userMemberList');

$router->add('/user/admin-profile{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'adminProfile'
))->setName('user_profile_admin');

$router->add('/user/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'edit'
))->setName('user_edit');

$router->add('/user/edit-agent{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'editAgent'
))->setName('user_edit_agent');

$router->add('/user/edit-member{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'editMember'
))->setName('user_edit_member');

$router->add('/user/add-member{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'addMember'
))->setName('user_add_member');

$router->add('/user/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'delete'
))->setName('user_delete');

$router->add('/user/add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'add'
))->setName('user_add');

$router->add('/user/add-agent{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'addAgent'
))->setName('user_add_agent');

$router->add('/user/add-message{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'addMessage'
))->setName('user_add_message');

$router->add('/user/edit-message{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'editMessage'
))->setName('user_edit_message');

$router->add('/user/add-email{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'addEmail'
))->setName('user_add_email');

$router->add('/user/edit-email{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'editEmail'
))->setName('user_edit_email');

$router->add('/user/delete-avatar{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'user',
    'action'     => 'deleteAvatar'
))->setName('user_delete_avatar');
// User ---------

// --------- Project
$router->add('/project/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'project',
    'action'     => 'index'
))->setName('project_list');

$router->add('/project/add', array(
    'module'     => 'admin',
    'controller' => 'project',
    'action'     => 'add'
))->setName('project_add');

$router->add('/project/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'project',
    'action'     => 'edit'
))->setName('project_edit');

$router->add('/project/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'project',
    'action'     => 'delete'
))->setName('project_delete');

$router->add('/project/post-ajax{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'project',
    'action'     => 'postAjax'
))->setName('project_post_ajax');

$router->add('/project/attribute/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'project',
    'action'     => 'listAttribute'
))->setName('project_list_attribute');

// --------- Project

// --------- Block
$router->add('/block/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'index'
))->setName('block_list');

$router->add('/block/add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'add'
))->setName('block_add');

$router->add('/block/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'edit'
))->setName('block_edit');

$router->add('/block/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'delete'
))->setName('block_delete');

$router->add('/block/attribute/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'listAttribute'
))->setName('block_list_attribute');

$router->add('/block/list/ajax', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'listBlock'
))->setName('block_list_block');

$router->add('/block/map-image/clone{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'block',
    'action'     => 'mapImageClone'
))->setName('block_map_image_clone');
// --------- Block

// --------- Apartment
$router->add('/apartment/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'index'
))->setName('apartment_list');

$router->add('/apartment/list-by-project{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'index'
))->setName('apartment_list_by_project');

$router->add('/apartment/list-by-block{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'listByBlock'
))->setName('apartment_list_by_block');

$router->add('/apartment/add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'add'
))->setName('apartment_add');

$router->add('/apartment/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'edit'
))->setName('apartment_edit');

$router->add('/apartment/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'delete'
))->setName('apartment_delete');

$router->add('/apartment/attribute/list', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'listAttribute'
))->setName('apartment_list_attribute');

$router->add('/apartment/request/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'requestList'
))->setName('apartment_request_list');

$router->add('/apartment/request/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'requestEdit'
))->setName('apartment_request_edit');

$router->add('/apartment/request/approve{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'requestApprove'
))->setName('apartment_request_approve');

$router->add('/apartment/request/reject{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'requestReject'
))->setName('apartment_request_reject');

$router->add('/apartment/list/ajax', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'listApartment'
))->setName('apartment_list_apartment');

$router->add('/apartment/furniture/list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'listFurniture'
))->setName('apartment_furniture_list');

$router->add('/apartment/furniture/add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'addFurniture'
))->setName('apartment_furniture_add');

$router->add('/apartment/furniture/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'editFurniture'
))->setName('apartment_furniture_edit');

$router->add('/apartment/furniture/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'apartment',
    'action'     => 'deleteFurniture'
))->setName('apartment_furniture_delete');
// --------- Apartment

// --------- System
$router->add('/system', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'index'
))->setName('system');

$router->add('/system/seo', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'index'
))->setName('system_seo');

$router->add('/system/email', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'email'
))->setName('system_email');

$router->add('/system/email', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'email'
))->setName('system_email');

$router->add('/system/option', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'option'
))->setName('system_option');

$router->add('/system/data', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'data'
))->setName('system_data');

$router->add('/system/data/import-project', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'postImportProject'
))->setName('system_data_import_project');

$router->add('/system/data/import-block', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'postImportBlock'
))->setName('system_data_import_block');

$router->add('/system/data/import-apartment', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'postImportApartment'
))->setName('system_data_import_apartment');

$router->add('/system/data/import-update-apartment', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'postImportUpdateApartment'
))->setName('system_data_import_update_apartment');

$router->add('/system/export-apartment', array(
    'module'     => 'admin',
    'controller' => 'system',
    'action'     => 'exportApartment'
))->setName('system_export_apartment');
// --------- System

// --------- Article
$router->add('/article/list', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'index'
))->setName('article');

$router->add('/article/page-list', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'page'
))->setName('article_list_page');

$router->add('/article/add', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'add'
))->setName('article_add');

$router->add('/article/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'edit'
))->setName('article_edit');

$router->add('/article/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'delete'
))->setName('article_delete');

$router->add('/article/page-add', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'pageAdd'
))->setName('article_add_page');

$router->add('/article/page-edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'pageEdit'
))->setName('article_edit_page');

$router->add('/article/page-delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'pageDelete'
))->setName('article_delete_page');

$router->add('/article/fengshui-list', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'fengshui'
))->setName('article_list_fengshui');

$router->add('/article/fengshui-add', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'fengshuiAdd'
))->setName('article_add_fengshui');

$router->add('/article/fengshui-edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'fengshuiEdit'
))->setName('article_edit_fengshui');

$router->add('/article/special-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'specialList'
))->setName('article_special_list');

$router->add('/article/special-add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'specialAdd'
))->setName('article_special_add');

$router->add('/article/special-edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'specialEdit'
))->setName('article_special_edit');

$router->add('/article/special-delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'article',
    'action'     => 'specialDelete'
))->setName('article_special_delete');
// --------- Article

// --------- Analytic
$router->add('/analytic/list', array(
    'module'     => 'admin',
    'controller' => 'analytic',
    'action'     => 'index'
))->setName('analytic');
// --------- Analytic

// --------- Interaction
$router->add('/interaction/list', array(
    'module'     => 'admin',
    'controller' => 'interaction',
    'action'     => 'index'
))->setName('interaction');

$router->add('/interaction/list-email', array(
    'module'     => 'admin',
    'controller' => 'interaction',
    'action'     => 'emailList'
))->setName('interaction_list_email');

$router->add('/interaction/add', array(
    'module'     => 'admin',
    'controller' => 'interaction',
    'action'     => 'add'
))->setName('interaction_add');

$router->add('/interaction/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'interaction',
    'action'     => 'edit'
))->setName('interaction_edit');

$router->add('/interaction/add-email', array(
    'module'     => 'admin',
    'controller' => 'interaction',
    'action'     => 'emailAdd'
))->setName('interaction_add_email');

$router->add('/interaction/edit-email{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'interaction',
    'action'     => 'emailEdit'
))->setName('interaction_edit_email');
// --------- Interactive

// --------- Banner
$router->add('/banner/list', array(
    'module'     => 'admin',
    'controller' => 'banner',
    'action'     => 'index'
))->setName('banner');

$router->add('/banner/add', array(
    'module'     => 'admin',
    'controller' => 'banner',
    'action'     => 'add'
))->setName('banner_add');

$router->add('/banner/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'banner',
    'action'     => 'edit'
))->setName('banner_edit');
// --------- Banner

// --------- Ceriterial
$router->add('/ceriterial/list', array(
    'module'     => 'admin',
    'controller' => 'ceriterial',
    'action'     => 'index'
))->setName('ceriterial');

$router->add('/ceriterial/buy-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'ceriterial',
    'action'     => 'buyList'
))->setName('ceriterial_buy_list');

$router->add('/ceriterial/rent-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'ceriterial',
    'action'     => 'rentList'
))->setName('ceriterial_rent_list');

$router->add('/ceriterial/smart-search-list{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'ceriterial',
    'action'     => 'smartSearchList'
))->setName('ceriterial_smart_search_list');

$router->add('/ceriterial/add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'ceriterial',
    'action'     => 'add'
))->setName('ceriterial_add');

$router->add('/ceriterial/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'ceriterial',
    'action'     => 'edit'
))->setName('ceriterial_edit');
// --------- Ceriterial

// --------- Category
$router->add('/category/list', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'index'
))->setName('category');

$router->add('/category/add', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'add'
))->setName('category_add');

$router->add('/category/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'edit'
))->setName('category_edit');

$router->add('/category/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'delete'
))->setName('category_delete');

$router->add('/category/group-list', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'groupList'
))->setName('category_list_group');

$router->add('/category/group-add', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'groupAdd'
))->setName('category_add_group');

$router->add('/category/group-edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'groupEdit'
))->setName('category_edit_group');

$router->add('/category/link-list', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'linkList'
))->setName('category_list_link');

$router->add('/category/link-add', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'linkAdd'
))->setName('category_add_link');

$router->add('/category/link-edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'linkEdit'
))->setName('category_edit_link');

$router->add('/category/link-delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'linkDelete'
))->setName('category_delete_link');

$router->add('/category/fengshui{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'fengShui'
))->setName('category_fengshui');

$router->add('/category/fengshui-add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'fengshuiAdd'
))->setName('category_add_fengshui');

$router->add('/category/fengshui-edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'category',
    'action'     => 'fengshuiEdit'
))->setName('category_edit_fengshui');
// --------- Category

// --------- Load
$router->add('/load/district-ajax', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'districtAjax'
))->setName('load_district_ajax');

$router->add('/load/upload-image/ajax{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'uploadImageAjax'
))->setName('load_upload_image_ajax');

$router->add('/load/upload-image-user/ajax{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'uploadImageUserAjax'
))->setName('load_upload_image_user_ajax');

$router->add('/load/delete-image/ajax', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'deleteImageAjax'
))->setName('load_delete_image_ajax');

$router->add('/load/upload-image-editor/ajax', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'uploadImageEditorAjax'
))->setName('load_upload_image_editor_ajax');

$router->add('/load/link/sort-ajax', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'linkSortAjax'
))->setName('load_sort_link_ajax');

$router->add('/load/block-ajax', array(
    'module'     => 'admin',
    'controller' => 'load',
    'action'     => 'blockAll'
))->setName('block_ajax');

// --------- Load

// Upload ---
$router->add('/load-iframe', array(
    'module'     => 'admin',
    'controller' => 'image',
    'action'     => 'index'
))->setName('load_iframe');

$router->add('/upload-multiple', array(
    'module'     => 'admin',
    'controller' => 'image',
    'action'     => 'multiple'
))->setName('upload_multiple');

$router->add('/quan-ly-hinh-anh', array(
    'module'     => 'admin',
    'controller' => 'image',
    'action'     => 'manager'
))->setName('manager_image');

$router->add('/upload', array(
    'module'     => 'admin',
    'controller' => 'upload',
    'action'     => 'index'
))->setName('upload_home');

$router->add('/change-folder-upload', array(
    'module'     => 'admin',
    'controller' => 'upload',
    'action'     => 'changeFolderUpload'
))->setName('change_folder_upload');

//Media
$router->add('/add-media', array(
    'module'     => 'admin',
    'controller' => 'media',
    'action'     => 'add'
))->setName('add_media');

$router->add('/delete-media', array(
    'module'     => 'admin',
    'controller' => 'home',
    'action'     => 'deleteMedia'
))->setName('delete_media');

//Folder
$router->add('/add-folder', array(
    'module'     => 'admin',
    'controller' => 'media',
    'action'     => 'addFolder'
))->setName('add_folder');

$router->add('/folder-delete', array(
    'module'     => 'admin',
    'controller' => 'media',
    'action'     => 'deleteFolder'
))->setName('folder_delete');

// --- Upload
$router->add('/error', array(
    'module'     => 'admin',
    'controller' => 'error',
    'action'     => 'access'
))->setName('access');

// --- Attribute
$router->add('/attribute/list/ajax/{module_attr:[a-z0-9-]+}{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'attribute',
    'action'     => 'listAttributeAjax'
))->setName('load_attribute_list_ajax');

$router->add('/attribute/add/ajax{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'attribute',
    'action'     => 'addAttributeAjax'
))->setName('load_attribute_add_ajax');

$router->add('/attribute/edit/ajax/{id:[a-z0-9-]+}{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'attribute',
    'action'     => 'editAttributeAjax'
))->setName('load_attribute_edit_ajax');

$router->add('/attribute/save/ajax{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'attribute',
    'action'     => 'saveAttrAjax'
))->setName('load_attribute_save_ajax');

$router->add('/attribute/delete/ajax{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'attribute',
    'action'     => 'deleteAttribute'
))->setName('load_attribute_delete');

// --- Create Map Image
$router->add('/map-image{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'map_image',
    'action'     => 'index'
))->setName('map_image_index');

$router->add('/map-image/add{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'map_image',
    'action'     => 'addAjax'
))->setName('map_image_add');

$router->add('/map-image/edit{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'map_image',
    'action'     => 'updateAjax'
))->setName('map_image_update');

$router->add('/map-image/delete{query:(/.*)*}', array(
    'module'     => 'admin',
    'controller' => 'map_image',
    'action'     => 'deleteAjax'
))->setName('map_image_delete');

$router->add('/map-image/update-image{query:(/.*)*}', array(
    'module' => 'admin',
    'controller' => 'map_image',
    'action' => 'updateLinkMapImage'
))->setName('map_link_update_link');

$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->notFound(array(
    'module'     => 'admin',
    'controller' => 'error',
    'action'     => 'error404'
));
