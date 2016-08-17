<?php
session_start();

/** Full path to the folder that images will be used as library and upload. Include trailing slash */
define('FOLDER_PATH', 'uploads/');

/** Full URL to the folder that images will be used as library and upload. Include trailing slash and protocol (i.e. http://) */
define('FOLDER_URL', '/uploads/image/uploads/');

/** Full URL to the folder that images will be used as library and upload. Include trailing slash and protocol (i.e. http://) */
define('DOMAIN', 'http://localhost.cdn.land.com/');

define('MOVE_CDN', 'http://localhost.admin.land.com/load/upload-image-editor/ajax');
define('LIBRARY_FOLDER_CDN_URL', 'http://localhost.cdn.land.com/asset/frontend/upload/article/');
define('LIBRARY_FOLDER_CDN_150_URL', 'http://localhost.cdn.land.com/asset/frontend/upload/article/');
define('LIBRARY_CURL', 'http://localhost.admin.land.com/ajax/file');

/** The extensions for to use in validation */
define('ALLOWED_IMG_EXTENSIONS', 'gif,jpg,jpeg,png');

/** Should the files be renamed to a random name when uploading */
define('RENAME_UPLOADED_FILES', true);

/** Number of folders/images to display per page */
define('ROWS_PER_PAGE', 12);

/** Should Images be resized on upload. You will then need to set at least one of the dimensions sizes below */
define('RESIZE_ON_UPLOAD', true);

/** If resizing, width */
define('RESIZE_WIDTH', 800);
/** If resizing, height */
define('RESIZE_HEIGHT', 600);

/** Should a thumbnail be created? */
define('THUMBNAIL_ON_UPLOAD', false);

/** If thumbnailing, thumbnail postfix */
define('THUMBNAIL_POSTFIX', '_thumb');
/** If thumbnailing, maximum width */
define('THUMBNAIL_WIDTH', 100);
/** If thumbnailing, maximum height */
define('THUMBNAIL_HEIGHT', 100);
/** If thumbnailing, hide thumbnails in listings */
define('THUMBNAIL_HIDE', true);

/**  Use these 9 functions to check cookies and sessions for permission.
Simply write your code and return true or false */

/** If you would like each user to have their own folder and only upload
 * to that folder and get images from there, you can use this funtion to
 * set the folder name base on user ids or usernames. NB: make sure it return
 * a valid folder name. */
function CurrentUserFolder() {
	return '';
}

function CanAcessLibrary() {
	return true;
}

function CanAcessUploadForm() {
	return true;
}

function CanAcessAllRecent() {
	return false;
}

function CanCreateFolders() {
	return false;
}

function CanDeleteFiles() {
	return true;
}

function CanDeleteFolder() {
	return false;
}

function CanRenameFiles() {
	return false;
}

function CanRenameFolder() {
	return false;
}
