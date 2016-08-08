<?php
namespace ITECH\Data\Lib;

class Constant
{
    const STATUS_CODE_SUCCESS         = 200;
    const STATUS_CODE_ERROR           = 400;
    const STATUS_CODE_ERROR_NOT_FOUND = 404;
    const STATUS_CODE_UNAUTHORIZED    = 401;

    const USER_TYPE_ADMINISTRATOR = 1;
    const USER_TYPE_AGENT         = 2;
    const USER_TYPE_MEMBER        = 3;

    const USER_MEMBERSHIP_ADMIN_SUPERADMIN = 11;
    const USER_MEMBERSHIP_ADMIN_ADMIN      = 12;
    const USER_MEMBERSHIP_ADMIN_EDITOR     = 12;
    const USER_MEMBERSHIP_ADMIN_SALE       = 13;
    const USER_MEMBERSHIP_ADMIN_SEO        = 14;
    const USER_MEMBERSHIP_ADMIN_MARKETING  = 15;

    const USER_MEMBERSHIP_USER_AGENT = 21;
    const USER_MEMBERSHIP_USER_USER  = 31;

    const USER_STATUS_ACTIVE   = 1;
    const USER_STATUS_INACTIVE = 2;
    const USER_STATUS_REMOVED  = 3;

    const USER_GENDER_MALE      = 1;
    const USER_GENDER_FEMALE    = 2;
    const USER_GENDER_UNDEFINED = 3;

    const USER_IS_VERIFIED_YES = 1;
    const USER_IS_VERIFIED_NOT = 2;

    const USER_AUTHENTICATE_APPLICATION_WEB      = 1;
    const USER_AUTHENTICATE_APPLICATION_ANDROID  = 2;
    const USER_AUTHENTICATE_APPLICATION_IOS      = 3;
    const USER_AUTHENTICATE_APPLICATION_WINPHONE = 4;

    const SESSION_TOKEN_APPLICATION_WEB      = 1;
    const SESSION_TOKEN_APPLICATION_ANDROID  = 2;
    const SESSION_TOKEN_APPLICATION_IOS      = 3;
    const SESSION_TOKEN_APPLICATION_WINPHONE = 4;

    const CATEGORY_STATUS_ACTIVE   = 1;
    const CATEGORY_STATUS_INACTIVE = 2;
    const CATEGORY_STATUS_REMOVED  = 3;

    const CATEGORY_MODULE_ARTICLE  = 1;
    const CATEGORY_MODULE_FENGSHUI = 2;

    const ARTICLE_STATUS_ACTIVE   = 1;
    const ARTICLE_STATUS_INACTIVE = 2;
    const ARTICLE_STATUS_REMOVED  = 3;

    const ARTICLE_TYPE_DEFAULT = 1;
    const ARTICLE_TYPE_FOCUS   = 2;

    const ARTICLE_TYPE_FENGSHUI_BOY  = 10;
    const ARTICLE_TYPE_FENGSHUI_GIRL = 11;

    const ARTICLE_MODULE_POST     = 1;
    const ARTICLE_MODULE_PAGE     = 2;
    const ARTICLE_MODULE_FENGSHUI = 3;
    const ARTICLE_MODULE_SPECIAL  = 4;

    const PROJECT_STATUS_ACTIVE   = 1;
    const PROJECT_STATUS_INACTIVE = 2;
    const PROJECT_STATUS_REMOVED  = 3;

    const PROJECT_ATTRIBUTE_STATUS_ACTIVE   = 1;
    const PROJECT_ATTRIBUTE_STATUS_INACTIVE = 2;
    const PROJECT_ATTRIBUTE_STATUS_REMOVED  = 3;

    const ATTRIBUTE_LANGUAGE_VIETNAMESE = 1;
    const ATTRIBUTE_LANGUAGE_ENGLISH    = 2;

    const ATTRIBUTE_TYPE_TYPE    = 1;
    const ATTRIBUTE_TYPE_VIEW    = 2;
    const ATTRIBUTE_TYPE_UTILITY = 3;

    const ATTRIBUTE_IS_SEARCH_YES = 1;
    const ATTRIBUTE_IS_SEARCH_NOT = 2;

    const ATTRIBUTE_STATUS_ACTIVE   = 1;
    const ATTRIBUTE_STATUS_INACTIVE = 2;
    const ATTRIBUTE_STATUS_REMOVED  = 3;

    const BLOCK_STATUS_ACTIVE   = 1;
    const BLOCK_STATUS_INACTIVE = 2;
    const BLOCK_STATUS_REMOVED  = 3;

    const BLOCK_ATTRIBUTE_TYPE_TYPE    = 1;
    const BLOCK_ATTRIBUTE_TYPE_VIEW    = 2;
    const BLOCK_ATTRIBUTE_TYPE_UTILITY = 3;

    const APARTMENT_STATUS_ACTIVE   = 1;
    const APARTMENT_STATUS_INACTIVE = 2;
    const APARTMENT_STATUS_REMOVED  = 3;

    const APARTMENT_CONDITION_AVAILABLE = 1;
    const APARTMENT_CONDITION_HOLD      = 2;
    const APARTMENT_CONDITION_SOLD      = 3;
    const APARTMENT_CONDITION_OTHER     = 4;

    const APARTMENT_TYPE_BUY  = 1;
    const APARTMENT_TYPE_RENT = 2;

    const APARTMENT_ATTRIBUTE_TYPE_TYPE                        = 1;
    const APARTMENT_ATTRIBUTE_TYPE_VIEW                        = 2;
    const APARTMENT_ATTRIBUTE_TYPE_UTILITY                     = 3;
    const APARTMENT_ATTRIBUTE_TYPE_ENTERTAINING_CONTROL_SYSTEM = 4;
    const APARTMENT_ATTRIBUTE_TYPE_ENERGY_CONTROL_SYSTEM       = 5;
    const APARTMENT_ATTRIBUTE_TYPE_ENVIRONMENT_CONTROL_SYSTEM  = 6;
    const APARTMENT_ATTRIBUTE_TYPE_SECURITY_CONTROL_SYSTEM     = 7;
    const APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE                   = 8;
    const APARTMENT_ATTRIBUTE_TYPE_BEST_FOR                    = 9;
    const APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR                = 10;

    const CERITERIAL_STATUS_ACTIVE   = 1;
    const CERITERIAL_STATUS_INACTIVE = 2;
    const CERITERIAL_STATUS_REMOVED  = 3;

    const CERITERIAL_TYPE_BUY          = 1;
    const CERITERIAL_TYPE_RENT         = 2;
    const CERITERIAL_TYPE_SMART_SEARCH = 3;

    const CERITERIAL_APARTMENT_IS_NEW_YES  = 1;
    const CERITERIAL_APARTMENT_IS_NEW_NOT  = 2;
    const CERITERIAL_APARTMENT_IS_HOME_YES = 1;
    const CERITERIAL_APARTMENT_IS_HOME_NOT = 2;

    const MAP_IMAGE_TYPE_IMAGE_VIEW = 1;
    const MAP_IMAGE_TYPE_MAP_VIEW   = 2;

    const TYPE_UPLOAD_IMAGE_PROJECT_DEFAULT     = 1;
    const TYPE_UPLOAD_IMAGE_PROJECT_GALLERY     = 2;
    const TYPE_UPLOAD_IMAGE_PROJECT_MAP_IMAGE   = 3;
    const TYPE_UPLOAD_IMAGE_PROJECT_ATTRIBUTE   = 4;
    const TYPE_UPLOAD_IMAGE_BLOCK_DEFAULT       = 5;
    const TYPE_UPLOAD_IMAGE_BLOCK_GALLERY       = 6;
    const TYPE_UPLOAD_IMAGE_BLOCK_MAP_IMAGE     = 7;
    const TYPE_UPLOAD_IMAGE_BLOCK_ATTRIBUTE     = 8;
    const TYPE_UPLOAD_IMAGE_APARTMENT_DEFAULT   = 9;
    const TYPE_UPLOAD_IMAGE_APARTMENT_GALLERY   = 10;
    const TYPE_UPLOAD_IMAGE_APARTMENT_ATTRIBUTE = 11;
    const TYPE_UPLOAD_IMAGE_CATEGORY            = 12;
    const TYPE_UPLOAD_IMAGE_ARTICLE_DEFAULT     = 13;
    const TYPE_UPLOAD_IMAGE_ARTICLE_GALLERY     = 14;
    const TYPE_UPLOAD_IMAGE_BANNER              = 15;
    const TYPE_UPLOAD_IMAGE_CATEGORY_DEFAULT    = 16;

    const MAP_IMAGE_MODULE_PROJECT   = 1;
    const MAP_IMAGE_MODULE_BLOCK     = 2;
    const MAP_IMAGE_MODULE_APARTMENT = 3;

    const MAP_IMAGE_POSITION_IMAGE = 1;
    const MAP_IMAGE_POSITION_MAP   = 2;

    const MAP_IMAGE_TYPE_THUMBNAIL = 1;
    const MAP_IMAGE_TYPE_GALLERY   = 2;
    const MAP_IMAGE_TYPE_FLOOR     = 3;
    const MAP_IMAGE_TYPE_3D        = 4;

    const ATTRIBUTE_MODULE_PROJECT   = 1;
    const ATTRIBUTE_MODULE_BLOCK     = 2;
    const ATTRIBUTE_MODULE_APARTMENT = 3;

    const ATTRIBUTE_TYPE_INPUT_SELECT = 'select';
    const ATTRIBUTE_TYPE_INPUT_TEXT   = 'text';

    const ATTRIBUTE_TYPE_INT       = 'int';
    const ATTRIBUTE_TYPE_VARCHAR   = 'varchar';
    const ATTRIBUTE_TYPE_TIMESTAMP = 'timestamp';

    const USER_LOG_ADMINISTRATOR_LOGIN            = 11;
    const USER_LOG_ADMINISTRATOR_CREATED_PROJECT  = 12;
    const USER_LOG_ADMINISTRATOR_UPDATED_PROJECT  = 13;

    const USER_LOG_ADMINISTRATOR_UPLOAD_IMAGE     = 14;
    const USER_LOG_ADMINISTRATOR_DELETE_IMAGE     = 15;

    const USER_LOG_ADMINISTRATOR_CREATED_BLOCK    = 16;
    const USER_LOG_ADMINISTRATOR_UPDATED_BLOCK    = 17;

    const USER_LOG_ADMINISTRATOR_CREATED_MAP_VIEW = 18;
    const USER_LOG_ADMINISTRATOR_UPDATED_MAP_VIEW = 19;

    const USER_LOG_TYPE_EDIT_USER                 = 16;
    const USER_LOG_TYPE_REMOVE_USER               = 17;
    const USER_LOG_AGENT_LOGIN                    = 21;
    const USER_LOG_MEMBER_LOGIN                   = 31;
    const USER_LOG_TYPE_ADD_USER                  = 13;

    const GROUP_TYPE_LINK   = 1;
    const GROUP_TYPE_BANNER = 2;

    const GROUP_STATUS_ACTIVE   = 1;
    const GROUP_STATUS_INACTIVE = 2;
    const GROUP_STATUS_REMOVED  = 3;

    const MESSAGE_STATUS_UNREAD = 1;
    const MESSAGE_STATUS_READ   = 2;

    const MESSAGE_STATUS_ACTIVE   = 1;
    const MESSAGE_STATUS_INACTIVE = 2;
    const MESSAGE_STATUS_REMOVED  = 3;

    const BANNER_STATUS_ACTIVE   = 1;
    const BANNER_STATUS_INACTIVE = 2;
    const BANNER_STATUS_REMOVED  = 3;

    const USER_SAVE_HOME   = 1;
    const USER_SAVE_SEARCH = 2;

    const GROUP_TYPE_LINK_TARGET = 1;
    const GROUP_TYPE_LINK_BLANK  = 2;

    const MESSAGE_INBOX_TYPE_SYSTEM_AGENT = 1;
    const MESSAGE_INBOX_TYPE_USER_SEND    = 2;
    const MESSAGE_INBOX_TYPE_SYSTEM_USER  = 3;
    const MESSAGE_INBOX_TYPE_SYSTEM_ALL   = 4;
    const MESSAGE_INBOX_TYPE_SYSTEM_EMAIL = 5;

    const MESSAGE_INBOX_STATUS_ACTIVE   = 1;
    const MESSAGE_INBOX_STATUS_INACTIVE = 2;
    const MESSAGE_INBOX_STATUS_REMOVED  = 3;

    const SAVE_SEARCH_NOTIFY_DAILY   = 1;
    const SAVE_SEARCH_NOTIFY_MONTHLY = 2;

    const APARTMENT_REQUEST_STATUS_APPROVED = 1;
    const APARTMENT_REQUEST_STATUS_WAITING  = 2;
    const APARTMENT_REQUEST_STATUS_REJECTED = 3;

    const APARTMENT_REQUEST_PAY_METHOD_FULL     = 1;
    const APARTMENT_REQUEST_PAY_METHOD_PROGRESS = 2;
    const APARTMENT_REQUEST_PAY_LOAN            = 3;

    const FURNITURE_STATUS_ACTIVE   = 1;
    const FURNITURE_STATUS_INACTIVE = 2;
    const FURNITURE_STATUS_REMOVED  = 3;

    const SYSTEM_LOG_ITEM_TYPE_PROJECT   = 1;
    const SYSTEM_LOG_ITEM_TYPE_BLOCK     = 2;
    const SYSTEM_LOG_ITEM_TYPE_APARTMENT = 3;
    const SYSTEM_LOG_ITEM_TYPE_ARTICLE   = 4;
    const SYSTEM_LOG_ITEM_TYPE_REQUEST   = 5;

    const SYSTEM_LOG_ACTION_CREATE   = 1;
    const SYSTEM_LOG_ACTION_EDIT     = 2;
    const SYSTEM_LOG_ACTION_DELETE   = 3;
    const SYSTEM_LOG_ACTION_ACTIVE   = 4;
    const SYSTEM_LOG_ACTION_INACTIVE = 5;

    public static function getProjectStatus()
    {
        return array(
            self::PROJECT_STATUS_ACTIVE   => 'Active',
            self::PROJECT_STATUS_INACTIVE => 'Inactive',
            self::PROJECT_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getCeriterialStatus()
    {
        return array(
            self::CERITERIAL_STATUS_ACTIVE   => 'Active',
            self::CERITERIAL_STATUS_INACTIVE => 'Inactive',
            self::CERITERIAL_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getCeriterialType()
    {
        return array(
            self::CERITERIAL_TYPE_BUY  => 'Cần bán',
            self::CERITERIAL_TYPE_RENT => 'Cho thuê'
        );
    }

    public static function getCeriterialIsNew()
    {
        return array(
            self::CERITERIAL_APARTMENT_IS_NEW_NOT => 'Không',
            self::CERITERIAL_APARTMENT_IS_NEW_YES => 'Có'
        );
    }

    public static function getCeriterialIsHome()
    {
        return array(
            self::CERITERIAL_APARTMENT_IS_HOME_NOT => 'Không',
            self::CERITERIAL_APARTMENT_IS_HOME_YES => 'Có'
        );
    }

    public static function getCeriterialTemplate()
    {
        return array(
            '1_big_4_small' => '1 lớn 4 nhỏ',
            '2_big_4_small' => '2 lớn 4 nhỏ',
            '1_big_5_small' => '1 lớn 5 nhỏ',
            '1_big_6_small' => '1 lớn 6 nhỏ'
        );
    }

    public static function getCeriterialTemplateValue()
    {
        return array(
            '1_big_4_small' => 5,
            '2_big_4_small' => 6,
            '1_big_5_small' => 7,
            '1_big_6_small' => 7
        );
    }

    public static function getMessageType()
    {
        return array(
            self::MESSAGE_INBOX_TYPE_SYSTEM_AGENT => 'Đại lý',
            self::MESSAGE_INBOX_TYPE_SYSTEM_USER  => 'Thành viên',
            self::MESSAGE_INBOX_TYPE_SYSTEM_ALL   => 'Tất cả',
            self::MESSAGE_INBOX_TYPE_SYSTEM_EMAIL => 'Email'
        );
    }

    public static function getMessageStatus()
    {
        return array(
            self::MESSAGE_INBOX_STATUS_ACTIVE   => 'Active',
            self::MESSAGE_INBOX_STATUS_INACTIVE => 'Inactive',
            self::MESSAGE_INBOX_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getCategoryStatus()
    {
        return array(
            self::CATEGORY_STATUS_ACTIVE   => 'Active',
            self::CATEGORY_STATUS_INACTIVE => 'Inactive',
            self::CATEGORY_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getGroupStatus()
    {
        return array(
            self::GROUP_STATUS_ACTIVE   => 'Active',
            self::GROUP_STATUS_INACTIVE => 'Inactive',
            self::GROUP_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getBannerStatus()
    {
        return array(
            self::BANNER_STATUS_ACTIVE   => 'Active',
            self::BANNER_STATUS_INACTIVE => 'Inactive',
            self::BANNER_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getGroupType()
    {
        return array(
            self::GROUP_TYPE_LINK   => 'Link',
            self::GROUP_TYPE_BANNER => 'Banner'
        );
    }

    public static function getGroupLinkTarget()
    {
        return array(
            self::GROUP_TYPE_LINK_TARGET => 'Liên kết vào',
            self::GROUP_TYPE_LINK_BLANK  => 'Mở tab mới'
        );
    }

    public static function getArticleStatus()
    {
        return array(
            self::ARTICLE_STATUS_ACTIVE   => 'Active',
            self::ARTICLE_STATUS_INACTIVE => 'Inactive',
            self::ARTICLE_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getArticleType()
    {
        return array(
            self::ARTICLE_TYPE_DEFAULT => 'Default',
            self::ARTICLE_TYPE_FOCUS   => 'Focus'
        );
    }

    public static function getArticleFengShuiType()
    {
        return array(
            self::ARTICLE_TYPE_FENGSHUI_BOY  => 'Nam',
            self::ARTICLE_TYPE_FENGSHUI_GIRL => 'Nữ'
        );
    }

    public static function getProjectAttributeType()
    {
        return array(
            self::ATTRIBUTE_TYPE_TYPE    => 'Kiểu',
            self::ATTRIBUTE_TYPE_VIEW    => 'Hướng nhìn',
            self::ATTRIBUTE_TYPE_UTILITY => 'Tiện ích'
        );
    }

    public static function getBlockAttributeType()
    {
        return array(
            self::BLOCK_ATTRIBUTE_TYPE_TYPE    => 'Kiểu',
            self::BLOCK_ATTRIBUTE_TYPE_VIEW    => 'Hướng nhìn',
            self::BLOCK_ATTRIBUTE_TYPE_UTILITY => 'Tiện ích'
        );
    }

    public static function getApartmentAttributeType()
    {
        return array(
            self::APARTMENT_ATTRIBUTE_TYPE_TYPE    => 'Kiểu căn hộ',
            self::APARTMENT_ATTRIBUTE_TYPE_VIEW    => 'Môi trường sống',
            self::APARTMENT_ATTRIBUTE_TYPE_UTILITY => 'Dịch vụ - Tiện ích'

            /*
            self::APARTMENT_ATTRIBUTE_TYPE_ENTERTAINING_CONTROL_SYSTEM => 'Hệ thống giải trí âm nhạc',
            self::APARTMENT_ATTRIBUTE_TYPE_ENERGY_CONTROL_SYSTEM       => 'Hệ thống điều khiển tiết kiệm điện',
            self::APARTMENT_ATTRIBUTE_TYPE_ENVIRONMENT_CONTROL_SYSTEM  => 'Hệ thống kiểm soát môi trường',
            self::APARTMENT_ATTRIBUTE_TYPE_SECURITY_CONTROL_SYSTEM     => 'Hệ thống an ninh',
            self::APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE                   => 'Phòng',
            self::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR                    => 'Tốt nhất cho',
            self::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR                => 'Phù hợp'
            */
        );
    }

    public static function getAttributeSearch()
    {
        return array(
            self::ATTRIBUTE_IS_SEARCH_YES => 'Cho phép tìm kiếm',
            self::ATTRIBUTE_IS_SEARCH_NOT => 'Không cho phép tìm kiếm'
        );
    }

    public static function getAttributeStatus()
    {
        return array(
            self::ATTRIBUTE_STATUS_ACTIVE   => 'Active',
            self::ATTRIBUTE_STATUS_INACTIVE => 'Inactive',
            self::ATTRIBUTE_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getAttributeLanguage()
    {
        return array(
            self::ATTRIBUTE_LANGUAGE_VIETNAMESE => 'Tiếng Việt',
            self::ATTRIBUTE_LANGUAGE_ENGLISH    => 'Tiếng Anh'
        );
    }

    public static function getBlockStatus()
    {
        return array(
            self::BLOCK_STATUS_ACTIVE   => 'Active',
            self::BLOCK_STATUS_INACTIVE => 'Inactive',
            self::BLOCK_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getApartmentStatus()
    {
        return array(
            self::BLOCK_STATUS_ACTIVE   => 'Active',
            self::BLOCK_STATUS_INACTIVE => 'Inactive',
            self::BLOCK_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getFurnitureStatus()
    {
        return array(
            self::FURNITURE_STATUS_ACTIVE   => 'Active',
            self::FURNITURE_STATUS_INACTIVE => 'Inactive',
            self::FURNITURE_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getMapView()
    {
        return array(
            self::MAP_IMAGE_TYPE_IMAGE_VIEW => 'Hình ảnh',
            self::MAP_IMAGE_TYPE_MAP_VIEW   => 'Bản đồ'
        );
    }

    public static function getUserGender()
    {
        return array(
            self::USER_GENDER_MALE      => 'Nam',
            self::USER_GENDER_FEMALE    => 'Nữ',
            self::USER_GENDER_UNDEFINED => 'Không xác định'
        );
    }

    public static function getUserStatus()
    {
        return array(
            self::USER_STATUS_ACTIVE   => 'Active',
            self::USER_STATUS_INACTIVE => 'Inactive',
            self::USER_STATUS_REMOVED  => 'Removed'
        );
    }

    public static function getUserMembership()
    {
        return array(
            self::USER_MEMBERSHIP_USER_USER => 'Thành viên'
        );
    }

    public static function getUserMembershipAdministrator()
    {
        return array(
            self::USER_MEMBERSHIP_ADMIN_SUPERADMIN => 'Super Admin',
            self::USER_MEMBERSHIP_ADMIN_ADMIN      => 'Admin',
            self::USER_MEMBERSHIP_ADMIN_SALE       => 'Admin Sale',
            self::USER_MEMBERSHIP_ADMIN_SEO        => 'Admin SEO',
            self::USER_MEMBERSHIP_ADMIN_MARKETING  => 'Admin Marketing'
        );
    }

    public static function getUserMembershipAgent()
    {
        return array(
            self::USER_MEMBERSHIP_USER_AGENT => 'Đại lý'
        );
    }

    public static function floorSelect()
    {
        $level = array('' => 'Chọn số tầng');

        for ($i = 1; $i <= 100; $i++) {
            if ($i > 0) {
                $level[$i] = $i;
            }
        }

        return $level;
    }

    public static function getApartmentCondition()
    {
        return array(
            self::APARTMENT_CONDITION_AVAILABLE => 'Còn trống',
            self::APARTMENT_CONDITION_HOLD      => 'Đang xử lý',
            self::APARTMENT_CONDITION_SOLD      => 'Đã bán',
            self::APARTMENT_CONDITION_OTHER     => 'Khác'
        );
    }

    public static function getApartmentType()
    {
        return array(
            self::APARTMENT_TYPE_BUY  => 'Mua',
            self::APARTMENT_TYPE_RENT => 'Thuê'
        );
    }

    public static function getDirection()
    {
        return array(
            1  => 'Đông',
            2  => 'Đông Nam',
            3  => 'Nam',
            4  => 'Tây Nam',
            5  => 'Tây',
            6  => 'Tây Bắc',
            7  => 'Bắc',
            8  => 'Đông Bắc',
            9  => 'Tây Bắc - Tây Nam',
            10 => 'Đông Bắc - Tây Bắc',
            11 => 'Đông Bắc - Đông Nam',
            12 => 'Đông Nam - Tây Nam',
            13 => 'Đông Tây',
            14 => 'Đông Nam - Tây Bắc',
            15 => 'Đông Bắc - Tây Nam'
        );
    }

    public static function getProjectPropertyType()
    {
        return array(
            1  => "Sản phẩm",
            2  => "Thông tầng",
            3  => "Sản phẩm góc",
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

    public static function getProjectPropertyView()
    {
        return array(
            1  => "Biển",
            2  => "Sông",
            3  => "Hồ",
            4  => "Hồ bơi",
            5  => "Công viên",
            6  => "Vườn",
            7  => "Golf",
            8  => "Thành phố",
            9  => "Mặt trời mọc",
            10 => "Mặt trời lặn"
        );
    }

    public static function getProjectPropertyUtility()
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

    public static function getBlockPropertyType()
    {
        return array(
            1  => "Sản phẩm",
            2  => "Thông tầng",
            3  => "Sản phẩm góc",
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

    public static function getBlockPropertyView()
    {
        return array(
            1  => "Biển",
            2  => "Sông",
            3  => "Hồ",
            4  => "Hồ bơi",
            5  => "Công viên",
            6  => "Vườn",
            7  => "Golf",
            8  => "Thành phố",
            9  => "Mặt trời mọc",
            10 => "Mặt trời lặn"
        );
    }

    public static function getBlockPropertyUtility()
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

    public static function allowOptions()
    {
        return array(
            'footer1_vi',
            'footer2_vi',
            'footer3_vi',
            'footer1_en',
            'footer2_en',
            'footer3_en',
            'facebook',
            'google_plus',
            'twitter',
        );
    }

    public static function getCeriterialPrice()
    {
        return array(
            '' => 'Mức giá',
            1  => 'Nhỏ hơn 400.000.000 VND',
            2  => '400.000.000 VND - 600.000.000 VND',
            3  => '600.000.000 VND - 800.000.000 VND',
            4  => '800.000.000 VND - 1.000.000.000 VND',
            5  => '1.000.000.000 VND - 1.200.000.000 VND',
            6  => '1.200.000.000 VND - 1.400.000.000 VND',
            7  => '1.400.000.000 VND - 1.600.000.000 VND',
            8  => '1.600.000.000 VND - 1.800.000.000 VND',
            9  => '1.800.000.000 VND - 2.000.000.000 VND',
            10 => 'Lớn hơn 2.000.000.000 VND'
        );
    }

    public static function optionNumberOnly()
    {
        return array(
            'range_price',
            'price_score',
            'request_timeout'
        );
    }

    public static function getApartmentRequestMethod()
    {
        return array(
            self::APARTMENT_REQUEST_PAY_METHOD_FULL     => '100%',
            self::APARTMENT_REQUEST_PAY_METHOD_PROGRESS => 'Theo tiến độ',
            self::APARTMENT_REQUEST_PAY_LOAN            => 'Vay vốn'
        );
    }

    public static function getApartmentRequestStatus()
    {
        return array(
            self::APARTMENT_REQUEST_STATUS_APPROVED => 'Đã duyệt',
            self::APARTMENT_REQUEST_STATUS_WAITING  => 'Đang chờ duyệt',
            self::APARTMENT_REQUEST_STATUS_REJECTED => 'Từ chối'
        );
    }

    public static function getMapImagePosition()
    {
        return array(
            self::MAP_IMAGE_POSITION_IMAGE => 'Hình ảnh',
            self::MAP_IMAGE_POSITION_MAP   => 'Bản đồ'
        );
    }

    public static function getMapImageType()
    {
        return array(
            self::MAP_IMAGE_TYPE_GALLERY   => 'Bộ sưu tập',
            self::MAP_IMAGE_TYPE_THUMBNAIL => 'Ảnh đại diện',
            self::MAP_IMAGE_TYPE_FLOOR     => 'Hình sơ đồ mặt bằng tầng - dãy',
            self::MAP_IMAGE_TYPE_3D        => 'Phối cảnh'
        );
    }
}
