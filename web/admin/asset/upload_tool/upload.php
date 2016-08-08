<?php
$json_out = array();
$json_out['status'] = 400;

if (isset($_FILES['file'])) {
    $files = $_FILES['file'];
    $json_out['status'] = 200;

    if ( 0 < $_FILES['file']['error'] ) {
        $json_out['error'] =  @$files['error'];
    }
    else {
        move_uploaded_file($files['tmp_name'], 'uploads/' . $files['name']);
        $link="uploads/".$files['name'];
        $json_out['result'] = array('link'=>$link);
    }
}

header('Content-Type: application/json');
echo json_encode($json_out);
