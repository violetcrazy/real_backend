<?php
define('ROOT', realpath(dirname(dirname(__FILE__))));
require_once 'Lib.php';
require_once 'Constant.php';

$before = microtime(true);

$lib = Lib::getInstance();
$db1 = $lib->db1Connect();
$db2 = $lib->db2Connect();

$q = 'TRUNCATE TABLE `land_location`';
$db2->query($q);

$q = 'SELECT `p1`.* FROM `province` AS `p1` ORDER BY `p1`.`id` ASC';
foreach ($db1->query($q) as $row) {
    $sq = 'INSERT INTO
        `land_location`(
            `id`,
            `parent_id`,
            `name`,
            `slug`,
            `ordering`,
            `project_count`
        )
        VALUES(
            "",
            "' . (int)0 . '",
            "' . addslashes($row['name']) . '",
            "' . $row['slug'] . '",
            "' . $row['id'] . '",
            "' . (int)0 . '"
        )';
    $db2->query($sq);

    $q1 = 'SELECT `d1`.*
        FROM `district` AS `d1`
        WHERE `d1`.`province_id` = "' . $row['id'] . '"
        ORDER BY `d1`.`id` ASC';
    foreach ($db1->query($q1) as $item) {
        $sq1 = 'INSERT INTO
            `land_location`(
                `id`,
                `parent_id`,
                `name`,
                `slug`,
                `ordering`,
                `project_count`
            )
            VALUES(
                "",
                "' . $item['province_id'] . '",
                "' . addslashes($item['name']) . '",
                "' . $item['slug'] . '",
                "' . $item['ordering'] . '",
                "' . (int)0 . '"
            )';
        $db2->query($sq1);
    }
}

$after = microtime(true);
echo ($after - $before) . " sec\n";