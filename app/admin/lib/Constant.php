<?php
namespace ITECH\Admin\Lib;

class Constant
{
    public static function getSidebarMenu()
    {
        return array(
            'home' => array(
                'icon_class' => 'clip-home',
                'title'      => 'Dashboard',
                'controller' => 'home',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                )
            ),
            'project_list' => array(
                'icon_class' => 'clip-database',
                'title'      => 'Quản lý dự án',
                'controller' => 'project',
                'action'     => 'index',
                'roles'      => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                ),
                'sub_menu' => array(
                    'project_list' => array(
                        'title'      => 'Danh sách dự án',
                        'controller' => 'project',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                        )
                    ),
                    'project_list_attribute' => array(
                        'title'      => 'Danh sách thuộc tính',
                        'controller' => 'project',
                        'action'     => 'listAttribute',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            'block_list' => array(
                'icon_class' => 'clip-cube-2 ',
                'title'      => 'Quản lý Block/Khu',
                'controller' => 'block',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                ),
                'sub_menu' => array(
                    'block_list' => array(
                        'title'      => 'Danh sách Block/Khu',
                        'controller' => 'block',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                        )
                    ),
                    'block_list_attribute' => array(
                        'title'      => 'Danh sách thuộc tính',
                        'controller' => 'block',
                        'action'     => 'listAttribute',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            'apartment_list' => array(
                'icon_class' => 'clip-location',
                'title'      => 'Quản lý sản phẩm',
                'controller' => 'apartment',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                ),
                'sub_menu' => array(
                    'apartment_list' => array(
                        'title'      => 'Danh sách sản phẩm',
                        'controller' => 'apartment',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
                        )
                    ),
                    'apartment_list_attribute' => array(
                        'title'      => 'Danh sách thuộc tính',
                        'controller' => 'apartment',
                        'action'     => 'listAttribute',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    ),
                    'apartment_request_list' => array(
                        'title'      => 'Danh sách yêu cầu',
                        'controller' => 'apartment',
                        'action'     => 'requestList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'apartment_furniture_list' => array(
                        'title'      => 'DS nhà cung cấp nội thất',
                        'controller' => 'apartment',
                        'action'     => 'furnitureList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            'ceriterial' => array(
                'icon_class' => 'clip-cube-2 ',
                'title'      => 'Bán, Thuê, Search',
                'controller' => 'ceriterial',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                ),
                'sub_menu' => array(
                    'ceriterial_buy_list' => array(
                        'title'      => 'Sản phẩm cần bán',
                        'controller' => 'ceriterial',
                        'action'     => 'buyList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    ),
                    'ceriterial_rent_list' => array(
                        'title'      => 'Sản phẩm cho thuê',
                        'controller' => 'ceriterial',
                        'action'     => 'rentList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    ),
                    'ceriterial_smart_search_list' => array(
                        'title'      => 'Smart search',
                        'controller' => 'ceriterial',
                        'action'     => 'smartSearchList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            'category' => array(
                'icon_class' => 'clip-banknote',
                'title'      => 'Danh mục/Nhóm',
                'controller' => 'category',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                ),
                'sub_menu' => array(
                    'category' => array(
                        'title'      => 'Danh mục',
                        'controller' => 'category',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'category_list_group' => array(
                        'title'      => 'Nhóm',
                        'controller' => 'category',
                        'action'     => 'groupList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'category_list_link' => array(
                        'title'      => 'Liên kết',
                        'controller' => 'category',
                        'action'     => 'linkList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'category_fengshui' => array(
                        'title'      => 'Phong thủy',
                        'controller' => 'category',
                        'action'     => 'fengShui',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            'article' => array(
                'icon_class' => 'clip-file-3',
                'title'      => 'Quản lý bài viết',
                'controller' => 'article',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                ),
                'sub_menu' => array(
                    'article' => array(
                        'title'      => 'DS bài viết',
                        'controller' => 'article',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    ),
                    'article_list_page' => array(
                        'title'      => 'DS trang tĩnh',
                        'controller' => 'article',
                        'action'     => 'page',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    ),
                    'article_list_fengshui' => array(
                        'title'      => 'DS phong thủy',
                        'controller' => 'article',
                        'action'     => 'fengshui',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    ),
                    'article_special_list' => array(
                        'title'      => 'DS tin special',
                        'controller' => 'article',
                        'action'     => 'specialList',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            'manager_image' => array(
                'icon_class' => 'clip-images-3',
                'title'      => 'Quản lý hình ảnh',
                'controller' => 'image',
                'action'     => 'manager',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                )
            ),
            'banner' => array(
                'icon_class' => 'clip-banknote',
                'title'      => 'Quản lý banner',
                'controller' => 'banner',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                ),
                'sub_menu' => array(
                    'banner' => array(
                        'title'      => 'Danh sách banner',
                        'controller' => 'banner',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
                        )
                    )
                )
            ),
            /*
            'interaction' => array(
                'icon_class' => 'clip-paperplane',
                'title'      => 'Quản lý tương tác',
                'controller' => 'interaction',
                'action'     => 'index',
                'roles' => array(\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN),
                'sub_menu' => array(
                    'interaction' => array(
                        'title'      => 'Thông báo hệ thống',
                        'controller' => 'interaction',
                        'action'     => 'index',
                        'roles' => array(\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN)
                    ),
                    'interaction_list_email' => array(
                        'title' => 'Thông báo email',
                        'controller' => 'interaction',
                        'action' => 'emailList',
                        'roles' => array(\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN)
                    )
                )
            ),
            'analytic' => array(
                'icon_class' => 'clip-health',
                'title'      => 'Quản lý thống kê',
                'controller' => 'analytic',
                'action'     => 'index',
                'roles' => array(\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN)
            ),
            */
            'system_seo' => array(
                'icon_class' => 'clip-settings',
                'title'      => 'Quản lý hệ thống',
                'controller' => 'system',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                ),
                'sub_menu' => array(
                    'system_seo' => array(
                        'title'      => 'Cấu hình SEO',
                        'controller' => 'system',
                        'action'     => 'index',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'system_email' => array(
                        'title'      => 'Cấu hình Email',
                        'controller' => 'system',
                        'action'     => 'email',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'system_option' => array(
                        'title'      => 'Tùy chỉnh',
                        'controller' => 'system',
                        'action'     => 'option',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ),
                    'system_data' => array(
                        'title'      => 'Nhập/Xuất Excel',
                        'controller' => 'system',
                        'action'     => 'data',
                        'roles' => array(
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    )
                )
            ),
            'user' => array(
                'icon_class' => 'clip-user-2',
                'title'      => 'Quản lý thành viên',
                'controller' => 'user',
                'action'     => 'index',
                'roles' => array(
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                    \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                ),
                'sub_menu' => array(
                    'userSuperAdminList' => [
                        'title'      => 'DS Super Admin',
                        'controller' => 'user',
                        'action'     => 'superAdminList',
                        'roles'      => [\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN]
                    ],
                    'userAdminList' => array(
                        'title'      => 'Danh sách Admin',
                        'controller' => 'user',
                        'action'     => 'adminList',
                        'roles'      => [
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        ]
                    ),
                    'userAdminEditorList' => array(
                        'title'      => 'DS Admin Editor',
                        'controller' => 'user',
                        'action'     => 'adminEditorList',
                        'roles'      => [
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        ]
                    ),
                    'userAdminSeoList' => array(
                        'title'      => 'DS Admin SEO',
                        'controller' => 'user',
                        'action'     => 'adminSeoList',
                        'roles'      => [
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        ]
                    ),
                    'userAdminMarketingList' => array(
                        'title'      => 'DS Admin Marketing',
                        'controller' => 'user',
                        'action'     => 'adminMarketingList',
                        'roles'      => [
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        ]
                    ),
                    'userMemberList' => array(
                        'title'      => 'Danh sách thành viên',
                        'controller' => 'user',
                        'action'     => 'memberList',
                        'roles'      => [
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        ]
                    ),
                    'userAgentList' => array(
                        'title'      => 'Danh sách đại lý',
                        'controller' => 'user',
                        'action'     => 'agentList',
                        'roles'      => [
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
                            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        ]
                    )
                )
            )
        );
    }
}
