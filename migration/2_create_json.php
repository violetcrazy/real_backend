<?php
define('ROOT', realpath(dirname(dirname(__FILE__))));
require_once 'Lib.php';
require_once 'Constant.php';
require_once 'Util.php';

$before = microtime(true);

$lib = Lib::getInstance();
$db2 = $lib->db2Connect();

$result = Constant::getApartmentTrend();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_trend.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentSecuritySystem();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_security_control_system.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentEnvironmentControlSystem();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_environment_control_system.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentEntertainingControlSystem();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_entertaining_control_system.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentSmartHome();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_smart_home.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentPropertyType();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_property_type.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentPropertyView();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_property_view.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentPropertyUtility();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_property_utility.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentRoomType();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_property_room_type.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentBestFor();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_property_best_for.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$result = Constant::getApartmentSuitableFor();
$contentJson = array();
foreach($result as $key => $item) {
    $contentJson[] = array(
        'id' => $key,
        'name' => $item
    );
}

$fp = fopen(ROOT . '/cache/data/json/apartment_property_suitable_for.json', 'w') or die('Error, opening output file.');
fwrite($fp, json_encode($contentJson));
fclose($fp);

$apartmentAttribute = Constant::getApartmentAttribute();

foreach ($apartmentAttribute as $key => $item) {
    $sq = 'UPDATE `land_attribute`
        SET `name` = "' . $item['name'] . 
            '", `slug` = "' . Util::slug($item['name']) .
            '", `type` = "' . $item['type'] . 
            '", `type_input` = "' . $item['type_input'] .
            '", `is_require` = "' . $item['is_require'] .
            '", `status` = "' . $item['status'] .
            '", `data` = "' . $item['data'] .
            '", `search` = "' . $item['search'] . '" 
        WHERE `id` = "' . $key . '"';
    $db2->query($sq);
}

$after = microtime(true);
echo ($after - $before) . " sec\n";