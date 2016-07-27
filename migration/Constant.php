<?php
class Constant
{
    public static function getDirection()
    {
        return array(
            1 => 'Bắc',
            2 => 'Đông Bắc',
            3 => 'Đông',
            4 => 'Đông Nam',
            5 => 'Nam',
            6 => 'Tây Nam',
            7 => 'Tây',
            8 => 'Tây Bắc',
        );
    }

    public static function getProjectPropertyType()
    {
        return array(
            1 => 'Apartment',
            2 => 'Corner Apartments',
            3 => 'Officetel',
            4 => 'Duplex',
            5 => 'Penthouse'
        );
    }

    public static function getProjectPropertyView()
    {
        return array(
            1 => 'Apartment',
            2 => 'Corner Apartments',
            3 => 'Officetel',
            4 => 'Duplex',
            5 => 'Penthouse'
        );
    }

    public static function getProjectPropertyUtility()
    {
        return array(
            1 => "Spa",
            2 => "Gym",
            3 => "Swimming pool",
            4 => "Park",
            5 => "Water Park",
            6 => "Playground",
            7 => "Amusement Park",
            8 => "Restaurant",
            9 => "Supermarket",
            10 => "Mall",
            11 => "Pet area",
            12 => "Golf Course",
            13 => "Tennis Court",
            14 => "Walking Path",
            15 => "Cleaning service",
            16 => "Maintenance service",
            17 => "BBQ",
            18 => "Unfurnished Homes",
            19 => "Partly Funished Homes",
            20 => "Fully Furnished Homes",
            21 => "University",
            22 => "Air Port",
            23 => "Cable Car",
            24 => "Easy Transportation",
            25 => "Event Origanisation",
            26 => "Hospital",
            27 => "Kid Kindergaten"
        );
    }

    public static function getBlockPropertyType()
    {
        return array(
            1 => 'Apartment',
            2 => 'Corner Apartments',
            3 => 'Officetel',
            4 => 'Duplex',
            5 => 'Penthouse'
        );
    }

    public static function getBlockPropertyView()
    {
        return array(
            1 => "Sea view",
            2 => "River view",
            3 => "Lake view",
            4 => "Pool view",
            5 => "Park view",
            6 => "Garden view",
            7 => "Golf view",
            8 => "City view",
            9 => "Sunrise view",
            10 => "Sunset view"
        );
    }

    public static function getBlockPropertyUtility()
    {
        return array(
            1 => "Spa",
            2 => "Gym",
            3 => "Swimming pool",
            4 => "Park",
            5 => "Water Park",
            6 => "Playground",
            7 => "Amusement Park",
            8 => "Restaurant",
            9 => "Supermarket",
            10 => "Mall",
            11 => "Pet area",
            12 => "Golf Course",
            13 => "Tennis Court",
            14 => "Walking Path",
            15 => "Cleaning service",
            16 => "Maintenance service",
            17 => "BBQ",
            18 => "Unfurnished Homes",
            19 => "Partly Funished Homes",
            20 => "Fully Furnished Homes",
            21 => "University",
            22 => "Air Port",
            23 => "Cable Car",
            24 => "Easy Transportation",
            25 => "Event Origanisation",
            26 => "Hospital",
            27 => "Kid Kindergaten"
        );
    }

    public static function getApartmentPropertyType()
    {
        return array(
            1  => "Căn hộ",
            2  => "Thông tầng",
            3  => "Căn hộ góc",
            4  => "Penthouse",
            5  => "Studio",
            6  => "Biệt thự",
            7  => "Biệt thự biển",
            8  => "Nhà mặt biển",
            9  => "Nhà biệt lập",
            10 => "Nhà liền kề",
            11 => "Officetel"
        );
    }

    public static function getApartmentPropertyView()
    {
        return array(
            1  =>  "Biển",
            2  =>  "Sông",
            3  =>  "Hồ",
            4  =>  "Hồ bơi",
            5  =>  "Công viên",
            6  =>  "Vườn",
            7  =>  "Golf",
            8  =>  "Thành phố",
            9  =>  "Mặt trời mọc",
            10 => "Mặt trời lặn"
        );
    }

    public static function getApartmentPropertyUtility()
    {
        return array(
            1  => "Spa",
            2  => "Gym",
            3  => "Hồ bơi",
            4  => "Công viên",
            5  => "Công viên nước",
            6  => "Sân chơi",
            7  => "Công viên giải trí",
            8  => "Nhà hàng",
            9  => "Siêu thị",
            10 => "Trung tâm mua sắm",
            11 => "Khu cho thú cưng",
            12 => "Sân golf",
            13 => "Sân tennis",
            14 => "Đường đi bộ",
            15 => "Dịch vụ dọn phòng",
            16 => "Dịch vụ bảo trì",
            17 => "Khu tiệc nướng ngoài trời",
            18 => "Nhà không có nội thất",
            19 => "Nhà có sẵn nội thất cơ bản",
            20 => "Nhà đầy đủ nội thất",
            21 => "Tiện ích khác"
        );
    }

    public static function getApartmentTrend()
    {
        return array(
            1 => 'Bắc',
            2 => 'Đông Bắc',
            3 => 'Đông',
            4 => 'Đông Nam',
            5 => 'Nam',
            6 => 'Tây Nam',
            7 => 'Tây',
            8 => 'Tây Bắc',
        );
    }

    public static function getApartmentSecuritySystem()
    {
        return array(
            1 => "Camera an ninh tự động",
            2 => "Hệ thống báo rò rỉ khí Gas",
            3 => "Cảm biến nhiệt",
            4 => "Cảm biến khói",
            5 => "Thiết bị báo cháy nổ",
            6 => "Dịch vụ đi kèm",
            7 => "Hệ thống chăm sóc vật nuôi"
        );
    }

    public static function getApartmentEnvironmentControlSystem()
    {
        return array(
            1 => "Hệ thống tưới nước sân vườn",
            2 => "Hệ thống đèn chiếu sáng",
            3 => "Điều chỉnh nhiệt độ tự động"
        );
    }

    public static function getApartmentEntertainingControlSystem()
    {
        return array(
            1 => "Điều khiển TV",
            2 => "Điều khiển loa"
        );
    }

    public static function getApartmentSmartHome()
    {
        return array(
            1 => "Hệ thống điều khiển tiết kiệm điện",
            2 => "Khóa cửa bằng dấu vân tay",
            3 => "Màn cửa tự động và cửa tự động",
            4 => "Lò sưởi hai mặt"
        );
    }

    public static function getApartmentRoomType()
    {
        return array(
            1  => "Working Room",
            2  => "Wine Cellar",
            3  => "Ware House",
            4  => "Security Room",
            5  => "Parking Room",
            6  => "Pantry Room",
            7  => "Old People Bed Room",
            8  => "Living Room",
            9  => "Maid Room",
            10 => "Kitchen",
            11 => "Hollow Room",
            12 => "Gym Room",
            13 => "Common Room",
            14 => "Garden",
            15 => "Library (Reading Room)",
            16 => "Eating Room",
            17 => "Bed Room",
            18 => "Bath Room",
            19 => "Closet Room"
        );
    }

    public static function getApartmentSuitableFor()
    {
        return array(
            1  => "Thuê",
            2  => "Ở",
            3  => "Bán"
        );
    }

    public static function getApartmentBestFor()
    {
        return array(
            1  => "Thuê",
            2  => "Ở",
            3  => "Gia đình trẻ"
        );
    }

    public static function getApartmentAttribute()
    {
        return array(
            1 => array(
                'name' => 'Hướng căn hộ',
                'type' => 'int',
                'type_input' => 'select',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_trend',
                'search' => 'apartment_trend'
            ),
            2 => array(
                'name' => 'Hệ thống giải trí âm nhạc',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_entertaining_control_system',
                'search' => 'apartment_entertaining_control_system'
            ),
            3 => array(
                'name' => 'Hệ thống an ninh',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_security_control_system',
                'search' => 'apartment_security_control_system'
            ),
            4 => array(
                'name' => 'Hệ thống kiểm soát môi trường',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_environment_control_system',
                'search' => 'apartment_environment_control_system'
            ),
            5 => array(
                'name' => 'Hệ thống điều khiển tiết kiệm điện',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_entertaining_control_system',
                'search' => 'apartment_entertaining_control_system'
            ),
            6 => array(
                'name' => 'Loại',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_property_type',
                'search' => 'apartment_property_type'
            ),
            7 => array(
                'name' => 'Tiện ích',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_property_utility',
                'search' => 'apartment_property_utility'
            ),
            8 => array(
                'name' => 'Hướng nhìn',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_property_view',
                'search' => 'apartment_property_view'
            ),
            9 => array(
                'name' => 'Phòng',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_property_room_type',
                'search' => 'apartment_property_room_type'
            ),
            10 => array(
                'name' => 'Tốt nhất cho',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_property_best_for',
                'search' => 'apartment_property_best_for'
            ),
            11 => array(
                'name' => 'Phù hợp',
                'type' => 'int',
                'type_input' => 'checkbox',
                'is_require' => '2',
                'status' => '1',
                'data' => 'apartment_property_suitable_for',
                'search' => 'apartment_property_suitable_for'
            )
        );
    }
}