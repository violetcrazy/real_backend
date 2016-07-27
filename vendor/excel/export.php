<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="">
</head>
<body>
    <?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    require_once 'PHPExcel.php';

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Jinn")
                                 ->setLastModifiedBy(date('m-d-y H:i:s'))
                                 ->setTitle("Data Export")
                                 ->setSubject("Data Export from JINN")
                                 ->setDescription("Data Export from JINN")
                                 ->setKeywords("")
                                 ->setCategory("");

    $result = json_decode(file_get_contents('https://api.muabannhanh.com/article/list?session_token=cb2663ce82a9f4ba448ba435091e27bb&limit=100'),  true);
    $result = $result['result'];

    $args = array('gallery', 'category', 'province', 'district', 'user');
    $ABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','X','AA');
    $j = 0 ;
    foreach ($result as $item) {
        $j ++ ;
        $i = 0;
        foreach ($item as $key => $value) {
            if (!in_array($key, $args)) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($ABC[$i] . $j, $value);
                $i++;
            }
        }
    }

    $objPHPExcel->getActiveSheet()->setTitle('Dữ liệu mẫu');
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('data_export.xlsx');
    echo 'File được lưu tại: ' , getcwd() , EOL;
?>
</body>
</html>
