<?php
require_once('config.php');
require_once('functions.php');

if (!defined('LIBRARY_FOLDER_PATH')) {
    define('LIBRARY_FOLDER_PATH', 'uploads/');
}

if (!defined('LIBRARY_FOLDER_PATH')) {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    if (preg_match("/(.*)\/rename_folder\.php/", $pageURL, $matches)) {
        define('LIBRARY_FOLDER_URL', $matches[1] . '/uploads/');
    }
}

$output = array();

$output["success"] = 1;
$output["msg"] = "";

if (isset($_GET["path"]) AND $_GET["path"] != "") {
    $current_folder = urldecode(clean($_GET["path"]));
} else {
    $current_folder = LIBRARY_FOLDER_PATH;
}

if (!CanRenameFolder()) {
    $output["success"] = 0;
    $output["msg"] = "You don not have permission to rename folders.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (isset($_GET["new_name"]) AND $_GET["new_name"] != "") {
    $new_name = clean($_GET["new_name"]);
} else {
    $output["success"] = 0;
    $output["msg"] = "The new name is required.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (isset($_GET["current_name"]) AND $_GET["current_name"] != "") {
    $current_name = clean($_GET["current_name"]);
    $folder = $current_folder . $current_name;
} else {
    $output["success"] = 0;
    $output["msg"] = "The current name is required.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!startsWith($folder, LIBRARY_FOLDER_PATH)) {
    $output["success"] = 0;
    $output["msg"] = "You can not rename this folder";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!file_exists($folder)) {
    $output["success"] = 0;
    $output["msg"] = "The folder does not exist.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!is_dir($folder)) {
    $output["success"] = 0;
    $output["msg"] = "That is not a folder.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (file_exists(($current_folder . $new_name))) {
    $output["success"] = 0;
    $output["msg"] = "The new name is already in use.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

rename(($current_folder . $current_name), ($current_folder . $new_name));

include 'contents.php';

header("Content-type: text/plain;");
echo json_encode($output);
exit();