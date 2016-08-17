<?php
function get_current_url() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . strtok($_SERVER["REQUEST_URI"], '?');
    }
    return $pageURL;
}

function CreateUserFolder($new_folder) {
    if (!file_exists($new_folder)) {
        $old = umask(0);
        if (!mkdir($new_folder, 0777)) {
            //Nothing yet
        }
        umask($old);
    }
}

function is_image_extenstion($extension) {
    return in_array($extension, array('gif', 'jpg', 'jpeg', 'png', 'jpe'));
}

function get_file_icon_path($extension) {
    $icon = 'bootstrap/img/file-icons/file_extension_' . $extension . '.png';

    if (!is_file($icon)) {
        $icon = 'bootstrap/img/file-icons/document_black.png';
    }

    return $icon;
}

$user = CurrentUserFolder();
define('LIBRARY_FOLDER_PATH', FOLDER_PATH . (strlen($user) > 0 ? $user . "/" : ""));
define('LIBRARY_FOLDER_URL', FOLDER_URL . (strlen($user) > 0 ? $user . "/" : ""));
CreateUserFolder(LIBRARY_FOLDER_PATH);

function Dirtree($path, $name = "Upload to: Home", $prefix = "") {
    if (isset($_SESSION["tinymce_upload_directory"]) AND $_SESSION["tinymce_upload_directory"] == $path) {
        $list = '<option value="' . $path . '" selected="selected">' . $prefix . '' . $name . '</option>';
    } else {
        $list = '<option value="' . $path . '">' . $prefix . '' . $name . '</option>';
    }

    $dircont = scandir($path);
    if (count($dircont) > 0) {
        foreach ($dircont as $file) {
            if (is_file($path . $file)) {
                //do nothing
            } elseif ($file !== '.' && $file !== '..') {
                $list .= Dirtree($path . $file . '/', $file, $prefix . '&hellip; ');
            }
        }
    }
    return $list;
}

function startsWith($haystack, $needle, $case = true) {
    if ($case) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
}

function lc_delete($targ) {
    if (is_dir($targ)) {
        $files = glob($targ . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
        foreach ($files as $file) {
            lc_delete($file);
        }
        rmdir($targ);
    } else {
        unlink($targ);
    }
}

function hide_thumbnail($file) {
    if (THUMBNAIL_HIDE == false) {
        return true;
    }

    $str = THUMBNAIL_POSTFIX . "." . GetExtension($file);

    $pos = strrpos($file, $str);
    if ($pos === false) {
        return true;
    } else {
        return false;
    }
}

function scandirSorted($path) {
    $sortedData = array();
    $dircont = scandir($path);
    if (count($dircont) > 0) {
        foreach ($dircont as $file) {
            if (is_file($path . $file)) {
                if (ValidFileExtension($file) AND hide_thumbnail($file)) {
                    array_push($sortedData, array('is_file' => true, 'name' => $file, 'path' => PathToUrl($path) . $file, 'p' => $path . $file, 's' => filesize($path . $file), 'x' => $path));
                }
            } elseif ($file !== '.' && $file !== '..') {
                $count = count(scandirSorted($path . $file . '/'));
                array_unshift($sortedData, array('is_file' => false, 'name' => $file, 'path' => $path . $file . '/', 'i' => $count));
            }
        }
    }
    return $sortedData;
}

function SearchFiles($path) {
    $sortedData = array();
    $dircont = scandir($path);
    if (count($dircont) > 0) {
        foreach ($dircont as $file) {
            if (is_file($path . $file)) {
                if (ValidFileExtension($file) AND hide_thumbnail($file)) {
                    $sortedData[] = array(0 => PathToUrl($path) . $file, 1 => $file, urlencode($path . $file), urlencode($path));
                }
            } elseif ($file !== '.' && $file !== '..') {
                array_merge($sortedData, SearchFiles($path . $file . '/'));
            }
        }
    }
    return $sortedData;
}

function PathToUrl($path) {
    if ($path == LIBRARY_FOLDER_PATH) {
        return LIBRARY_FOLDER_URL;
    } else {
        $url = str_replace(LIBRARY_FOLDER_PATH, "", $path);
        //array_shift($url);// Remove root of lib

        if ($url != "") {
            return LIBRARY_FOLDER_URL . $url;
        } else {
            return LIBRARY_FOLDER_URL;
        }
    }
}

function ValidFileExtension($name) {
    $allowed_extensions = explode(',', ALLOWED_IMG_EXTENSIONS);
    $extension = strtolower(GetExtension($name));
    if (in_array($extension, $allowed_extensions, TRUE)) {
        return true;
    } else {
        return false;
    }
}

if (!function_exists('random_file_name')) {

    function random_file_name($name) {
        return uniqid() . '_' . time() . "." . GetExtension($name);
    }

}

if (!function_exists('generateRandomNumber')) {
    function generateRandomNumber($len) {
        $al = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $date = date("Hs");
        $password = "$date";
        for ($index = 1; $index <= $len; $index++) {
            $randomNumber = rand(1, strlen($al));
            $password .= substr($al, $randomNumber - 1, 1);
        }
        return $password;
    }

}

function GetExtension($filename) {
    $x = explode('.', $filename);
    return end($x);
}

function clean($str) {
    if (is_array($str)) {
        $return = array();

        foreach ($str as $k => $v) {
            $return[clean($k)] = clean($v);
        }

        return $return;
    } else {
        $str = trim($str);
        return $str;
    }
}

function set_filename($path, $filename) {
    $filename = clean_file_name($filename);
    $file_ext = GetExtension($filename);
    if (!file_exists($path . $filename)) {
        return $filename;
    }
    $new_filename = str_replace('.' . $file_ext, '', $filename);
    for ($i = 1; $i < 300; $i++) {
        if (!file_exists($path . $new_filename . '_' . $i . '.' . $file_ext)) {
            $new_filename .= '_' . $i . '.' . $file_ext;
            break;
        }
    }
    return $new_filename;
}

function clean_file_name($filename) {
    $invalid = array("<!--", "-->", "'", "<", ">", '"', '&', '$', '=', ';', '?', '/', "%20", "%22", "%3c", "%253c", "%3e", "%0e", "%28", "%29", "%2528", "%26", "%24", "%3f", "%3b", "%3d");
    $filename = str_replace($invalid, '', $filename);
    $filename = preg_replace("/\s+/", "_", $filename);
    return stripslashes($filename);
}

function MBToBytes($number) {
    return $number * pow(1024, 2);
}

function DoUpload($field = 'userfile') {
    $output = array();
    $output["success"] = true;
    $output["is_pdf"] = 0;

    if (isset($_SESSION["tinymce_upload_directory"]) AND $_SESSION["tinymce_upload_directory"] != "") {
        $current_folder = $_SESSION["tinymce_upload_directory"];
    } else {
        $current_folder = LIBRARY_FOLDER_PATH;
    }

    if (!CanAcessUploadForm()) {
        $output["reason"] = "No permission to upload.";
        $output["success"] = false;
        return $output;
    }

    if (!isset($_FILES[$field])) {
        $output["reason"] = "File not selected.";
        $output["success"] = false;
        return $output;
    }

    if (!is_uploaded_file($_FILES[$field]['tmp_name'])) {
        $error = (!isset($_FILES[$field]['error'])) ? 4 : $_FILES[$field]['error'];
        $output["success"] = false;
        switch ($error) {
            case 1: // UPLOAD_ERR_INI_SIZE
                $output["reason"] = "File exceeds limit size.";
                break;
            case 2: // UPLOAD_ERR_FORM_SIZE
                $output["reason"] = "File exceeds limit size.";
                break;
            case 3: // UPLOAD_ERR_PARTIAL
                $output["reason"] = "File uploaded partially.";
                break;
            case 4: // UPLOAD_ERR_NO_FILE
                $output["reason"] = "File not selected.";
                break;
            case 6: // UPLOAD_ERR_NO_TMP_DIR
                $output["reason"] = "No temp directory.";
                break;
            case 7: // UPLOAD_ERR_CANT_WRITE
                $output["reason"] = "Unable to write the file.";
                break;
            case 8: // UPLOAD_ERR_EXTENSION
                $output["reason"] = "Invalid extension.";
                break;
            default :
                $output["reason"] = "File not selected.";
                break;
        }

        return $output;
    }

    if (!ValidFileExtension($_FILES[$field]['name'])) {
        $output["reason"] = "Invalid extension.";
        $output["success"] = false;
        return $output;
    }

    if (RENAME_UPLOADED_FILES == true) {
        $file_name = random_file_name($_FILES[$field]['name']);
        $file_name = set_filename($current_folder, $file_name);
    } else {
        $file_name = set_filename($current_folder, $_FILES[$field]['name']);
    }

    if (!copy($_FILES[$field]['tmp_name'], $current_folder . $file_name)) {
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $current_folder . $file_name)) {
            $output["reason"] = "Could not move file.";
            $output["success"] = false;
            return $output;
        }
    }

    if (!isset($_SESSION['SimpleImageManager'])) {
        $_SESSION['SimpleImageManager'] = array();
    }
    $_SESSION['SimpleImageManager'][] = PathToUrl($current_folder) . $file_name;
    $output["file"] = PathToUrl($current_folder) . $file_name;
    $output["file_cdn"] = LIBRARY_FOLDER_CDN_URL . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $file_name;
    $output["file_url"] = PathToUrl($current_folder);
    $output["file_name_new"] = $file_name;
    $output["file_name_old"] = $_FILES[$field]['name'];
    $output["resource"] = $_FILES[$field];

    if (is_image_extenstion(GetExtension($file_name))) {
        $rw = isset($_SESSION['RESIZE_WIDTH']) ? $_SESSION['RESIZE_WIDTH'] : RESIZE_WIDTH;
        $rh = isset($_SESSION['RESIZE_HEIGHT']) ? $_SESSION['RESIZE_HEIGHT'] : RESIZE_HEIGHT;

        Resizing($current_folder, $file_name, $rw, $rh);
    } else {
        $output["is_pdf"] = 1;
        $output["icon"] = get_file_icon_path(GetExtension($file_name));
    }

    return $output;
}

function Resizing($path, $filename, $rw, $rh) {
    //$size = getimagesize($path . $filename);
    if (RESIZE_ON_UPLOAD == true) {
        $image_lib = new Image_lib();

        $config = array();
        $config['source_image'] = $path . $filename;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;

        /*
        if (intval($rw) > 0) {
            $config['width'] = intval($rw);
        } elseif (intval($rh) > 0) {
            //$config['width'] = floor($size[0] * (intval($_SESSION['RESIZE_HEIGHT']) / $size[1]));
        }

        if (intval($rh) > 0) {
            $config['height'] = intval($rh);
        } elseif (intval($rw) > 0) {
            //$config['height'] = floor($size[1] * (intval($_SESSION['RESIZE_WIDTH']) / $size[0]));
        }
        */

        $config['width'] = $rw;
        $config['height'] = $rh;

        $image_lib->initialize($config);
        $image_lib->resize();
    }

    Thumbnail($path, $filename);
}

function Thumbnail($path, $filename) {
    //$size = getimagesize($path.$filename);

    if (THUMBNAIL_ON_UPLOAD == true) {
        $image_lib = new Image_lib();

        $config = array();
        $config['source_image'] = $path . $filename;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['height'] = THUMBNAIL_HEIGHT;
        $config['width'] = THUMBNAIL_WIDTH;
        $config['thumb_marker'] = THUMBNAIL_POSTFIX;

        $image_lib->initialize($config);
        $image_lib->resize();
    }
}

function is_url_exist($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code == 200) {
        $status = true;
    } else {
        $status = false;
    }
    curl_close($ch);
    return $status;
}

function TrimText($input, $length) {
    $input = strip_tags($input);
    if (strlen($input) <= $length) {
        return $input;
    }
    $trimmed_text = substr($input, 0, $length);

    $trimmed_text .= ' &hellip;';

    return $trimmed_text;
}

function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function curl_cdn($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($content);
    return $result;
}

function Paginate($url, $page, $total_pages, $adjacents = 3) {
    //$prevlabel = "&larr;";
    //$nextlabel = "&rarr;";

    $out = '<div class="pagination"><ul>';

    // previous
    if ($page == 1) {
        $out.= '<li class="disabled"><a href="#">&larr;</a></li>';
    } else {
        $out.= '<li><a class="page-link" data-path="' . urlencode($url) . '" data-page="' . ($page - 1) . '" href="">&larr;</a></li>';
    }

    // first
    if ($page > ($adjacents + 1)) {
        $out.= '<li><a class="page-link" data-path="' . urlencode($url) . '" data-page="1" href="">1</a></li>';
    }

    // interval
    if ($page > ($adjacents + 2)) {
        $out.= '<li class="disabled"><a href="#">...</a></li>';
    }

    // pages
    $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
    $pmax = ($page < ($total_pages - $adjacents)) ? ($page + $adjacents) : $total_pages;
    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $page) {
            $out.= '<li class="disabled"><a href="#">' . $i . '</a></li>';
        } else {
            $out.= '<li><a class="page-link" data-path="' . urlencode($url) . '" data-page="' . $i . '" href="">' . $i . '</a></li>';
        }
    }

    // interval
    if ($page < ($total_pages - $adjacents - 1)) {
        $out.= '<li class="disabled"><a href="#">...</a></li>';
    }

    // last
    if ($page < ($total_pages - $adjacents)) {
        $out.= '<li><a class="page-link" data-path="' . urlencode($url) . '" data-page="' . $total_pages . '" href="">' . $total_pages . '</a></li>';
    }

    // next
    if ($page < $total_pages) {
        $out.= '<li><a class="page-link" data-path="' . urlencode($url) . '" data-page="' . ($page + 1) . '" href="">&rarr;</a></li>';
    } else {
        $out.= '<li class="disabled"><a href="#">&rarr;</a></li>';
    }

    $out.= '</ul></div>';

    return $out;
}