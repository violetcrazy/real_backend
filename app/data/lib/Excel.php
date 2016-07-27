<?php
namespace ITECH\Data\Lib;

require_once ROOT . '/vendor/excel/PHPExcel.php';
require_once ROOT . '/vendor/excel/PHPExcel/IOFactory.php';

class Excel
{
    public static function importData($file, $type)
    {
        $objPHPExcel = \PHPExcel_IOFactory::load($file);

        $keyMapProject = array(
            'name',
            'name_eng',
            'address',
            'address_eng',
            'province_id',
            'district_id',
            'description',
            'description_eng',
            'default_image',
            'images',
            'property_type',
            'property_view',
            'property_utility',
            'direction',
            'area', //Tổng diện tích
            'space', //Diện tích cây xanh
            'block_count',
            'apartment_count',
        );

        $keyMapBlock = array(
            'name',
            'shortname',
            'floor_count',
            'apartment_count',
            'price',
            'description',
            'direction',
            'area',
            'space',
            'property_type',
            'property_view',
            'property_utility',
            'policy',
            'name_eng',
            'price_eng',
            'description_eng',
            'property_type_eng',
            'property_view_eng',
            'property_utility_eng',
            'policy_eng'
        );

        $keyMapApartment = array(
            'id',
            'user_id',
            'name',
            'name_eng',
            'ordering',
            'floor_count',
            'price',
            'price_sale_off',
            'rose',
            'area',
            'space',
            'description',
            'bedroom_count',
            'bathroom_count',
            'type',
            'adults',
            'children',
            'furniture_name',
            'furniture_address',
            'furniture_note',
            'furniture_name_eng',
            'furniture_address_eng',
            'furniture_note_eng',
            'furniture_email',
            'default_image',
            'direction',
            'condition',
            'property_type',
            'property_type_eng',
            'property_view',
            'property_view_eng',
            'property_utility',
            'property_utility_eng',
            'entertaining_control_system',
            'entertaining_control_system_eng',
            'security_control_system',
            'security_control_system_eng',
            'environment_control_system',
            'environment_control_system_eng',
            'energy_control_system',
            'energy_control_system_eng',
            'room_type',
            'room_type_eng',
            'best_for',
            'best_for_eng',
            'suitable_for',
            'suitable_for_eng'
        );

        $post = array();

        switch ($type) {
            case 'project':
                $keyMap = $keyMapProject;
                break;

            case 'block':
                $keyMap = $keyMapBlock;
                break;

            case 'apartment':
                $keyMap = $keyMapApartment;
                break;

            default:
                goto END_FUNCTION;
        }

        $index = 0;

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            if ($index != 0) {
                break;
            }
            $index ++;

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $i = 0;
                if ($row->getRowIndex() >= 2){
                    foreach ($cellIterator as $cell) {
                        if ($i >= count($keyMap)) {
                            break;
                        }

                        if ($i == 0 && is_null($cell->getCalculatedValue())) {
                            break;
                        }

                        if (!is_null($cell)) {
                            $value = $cell->getCalculatedValue();
                            if ($keyMap[$i] == 'images') {
                                $post[$row->getRowIndex()][$keyMap[$i]] = explode(',', $value);
                            } else {
                                $post[$row->getRowIndex()][$keyMap[$i]] = $value;
                            }
                            $i ++;
                        }
                    }
                }
            }
        }

        END_FUNCTION:
            return $post;
    }

    public static function exportDataApartment($data, $fileName = 'data_export')
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Jinn")
            ->setLastModifiedBy(date('m-d-y H:i:s'))
            ->setTitle("Data Export")
            ->setSubject("Data Export from JINN")
            ->setDescription("Data Export from JINN")
            ->setKeywords("")
            ->setCategory("");

        $keyCell = array(   'A','B','C','D','E','F','G','H','I','J',
                            'K','L','M','N','O','P','Q','R','S','T',
                            'U','V','W','X','Y','Z','AA','AB','AC','AD',
                            'AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN',
                            'AO','AP','AQ','AR','AS','AT','AU','AV');
        $header = array(
            'ID',
            '[VI] Tên',
            '[EN] Tên',
            '[VI] Giá',
            '[EN] Giá',
            '[VI] Giá khuyến mãi',
            '[EN] Giá khuyến mãi',
            'Tình trạng',
            'Tầng',
            'Thứ tự sản phẩm',
            'Người quản lý',
            'Hoa Hồng',
            'Diện tích',
            'Diện tích vườn',
            'Hình vị trí',
            '[VI] Mô tả vị trí',
            '[EN] Mô tả vị trí',
            '[VI] Mô tả',
            '[EN] Mô tả',
            'Hình đại diện',
            'Hinh ảnh',
            'Số phòng ngủ',
            'Số phòng tắm',
            'Loại',
            'Hướng nhìn',
            'Người lớn',
            'Trẻ em',
            'Kiểu sản phẩm',
            'Môi trường sống',
            'Dịch vụ - tiện ích',
            '[VI] Seo tiêu đề',
            '[EN] Seo tiêu đề',
            '[VI] Seo từ khóa',
            '[EN] Seo từ khóa',
            '[VI] Seo mô tả',
            '[EN] Seo mô tả',
        );

        $k = 0;
        foreach ($header as $item) {
            $cellName = $keyCell[$k] . '1';
            $objPHPExcel
                ->setActiveSheetIndex(0)
                ->setCellValue($cellName, $item);
            $objPHPExcel->getActiveSheet()->getStyle($cellName)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($cellName)->getFill()->getStartColor()->setRGB('FFFF00');
            $k++;
        }

        if (count($data) > 0) {
            $i = 1;
            foreach ($data as $apartment) {
                $i ++;
                $objPHPExcel
                    ->setActiveSheetIndex(0)
                    ->setCellValue($keyCell[0] . $i, $apartment->id)
                    ->setCellValue($keyCell[1] . $i, $apartment->name)
                    ->setCellValue($keyCell[2] . $i, $apartment->name_eng)
                    ->setCellValue($keyCell[3] . $i, $apartment->price)
                    ->setCellValue($keyCell[4] . $i, $apartment->price_eng)
                    ->setCellValue($keyCell[5] . $i, $apartment->price_sale_off)
                    ->setCellValue($keyCell[6] . $i, $apartment->price_sale_off_eng)
                    ->setCellValue($keyCell[7] . $i, $apartment->condition)
                    ->setCellValue($keyCell[8] . $i, $apartment->floor)
                    ->setCellValue($keyCell[9] . $i, $apartment->room_count)
                    ->setCellValue($keyCell[10] . $i, $apartment->user_id)
                    ->setCellValue($keyCell[11] . $i, $apartment->rose)
                    ->setCellValue($keyCell[12] . $i, $apartment->total_area)
                    ->setCellValue($keyCell[13] . $i, $apartment->green_area)
                    ->setCellValue($keyCell[14] . $i, $apartment->image_position)
                    ->setCellValue($keyCell[15] . $i, $apartment->position_vi)
                    ->setCellValue($keyCell[16] . $i, $apartment->position_en)
                    ->setCellValue($keyCell[17] . $i, $apartment->description)
                    ->setCellValue($keyCell[18] . $i, $apartment->description_eng)
                    ->setCellValue($keyCell[19] . $i, $apartment->default_image)
                    ->setCellValue($keyCell[20] . $i, $apartment->images)
                    ->setCellValue($keyCell[21] . $i, $apartment->bedroom_count)
                    ->setCellValue($keyCell[22] . $i, $apartment->bathroom_count)
                    ->setCellValue($keyCell[23] . $i, $apartment->type)
                    ->setCellValue($keyCell[24] . $i, $apartment->direction)
                    ->setCellValue($keyCell[25] . $i, $apartment->adults_count)
                    ->setCellValue($keyCell[26] . $i, $apartment->children_count)
                    ->setCellValue($keyCell[27] . $i, $apartment->property_type)
                    ->setCellValue($keyCell[28] . $i, $apartment->property_view)
                    ->setCellValue($keyCell[29] . $i, $apartment->property_utility)
                    ->setCellValue($keyCell[30] . $i, $apartment->meta_title)
                    ->setCellValue($keyCell[31] . $i, $apartment->meta_title_eng)
                    ->setCellValue($keyCell[32] . $i, $apartment->meta_keywords)
                    ->setCellValue($keyCell[33] . $i, $apartment->meta_keywords_eng)
                    ->setCellValue($keyCell[34] . $i, $apartment->meta_description)
                    ->setCellValue($keyCell[35] . $i, $apartment->meta_description_eng);
            }

            $objPHPExcel->getActiveSheet()->setTitle('Sản phẩm');
            $objPHPExcel->setActiveSheetIndex(0);

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save(ROOT . '/web/admin/asset/download/'  . $fileName);
            return true;
        } else {
            return false;
        }
    }
}
