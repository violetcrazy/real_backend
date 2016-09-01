<?php
namespace ITECH\Admin\Controller;

class UserAdminController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();
    }

    public function superAdminListAction()
    {
        parent::authenticateUser();
        parent::allowRole([\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN]);

        $q     = $this->request->getQuery('q', ['striptags', 'trim'], '');
        $page  = $this->request->getQuery('page', ['int'], 1);
        $limit = $this->config->application->pagination_limit;

        $params = [
            'conditions' => [
                'q'          => $q,
                'type'       => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
            ],
            'page'  => $page,
            'limit' => $limit
        ];

        $userRepo = new \ITECH\Data\Repo\UserRepo;
        $result   = $userRepo->getPaginationList($params);

        $query         = [];
        $query['page'] = $page;

        $url = $this->url->get(['for' => 'userSuperAdminList']);

        $options = [
            'url'           => $url,
            'query'         => $query,
            'total_pages'   => isset($result->total_pages) ? $result->total_pages : 0,
            'page'          => $page,
            'pages_display' => 3
        ];

        $layoutComponent  = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách Super Admin',
                'url'    => $this->url->get(['for' => 'userSuperAdminList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q

        ]);
        $this->view->pick(parent::$theme . '/user_admin/super_admin_list');
    }

    public function adminListAction()
    {
        parent::authenticateUser();

        $q     = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page  = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'          => $q,
                'type'       => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result   = $userRepo->getPaginationList($params);

        $query         = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userAdminList'));

        $options = array(
            'url'           => $url,
            'query'         => $query,
            'total_pages'   => isset($result->total_pages) ? $result->total_pages : 0,
            'page'          => $page,
            'pages_display' => 3
        );

        $layoutComponent  = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách Admin',
                'url'    => $this->url->get(['for' => 'userAdminList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q

        ]);
        $this->view->pick(parent::$theme . '/user_admin/admin_list');
    }

    public function adminEditorListAction()
    {
        parent::authenticateUser();

        $q     = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page  = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'          => $q,
                'type'       => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result   = $userRepo->getPaginationList($params);

        $query         = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userAdminEditorList'));

        $options = array(
            'url'           => $url,
            'query'         => $query,
            'total_pages'   => isset($result->total_pages) ? $result->total_pages : 0,
            'page'          => $page,
            'pages_display' => 3
        );

        $layoutComponent  = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách Admin Editor',
                'url'    => $this->url->get(['for' => 'userAdminEditorList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q

        ]);
        $this->view->pick(parent::$theme . '/user_admin/admin_editor_list');
    }

    public function adminSeoListAction()
    {
        parent::authenticateUser();

        $q     = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page  = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'          => $q,
                'type'       => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result   = $userRepo->getPaginationList($params);

        $query         = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userAdminSeoList'));

        $options = array(
            'url'           => $url,
            'query'         => $query,
            'total_pages'   => isset($result->total_pages) ? $result->total_pages : 0,
            'page'          => $page,
            'pages_display' => 3
        );

        $layoutComponent  = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách Admin SEO',
                'url'    => $this->url->get(['for' => 'userAdminSeoList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q

        ]);
        $this->view->pick(parent::$theme . '/user_admin/admin_seo_list');
    }

    public function adminSaleListAction()
    {
        parent::authenticateUser();

        $q     = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page  = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'          => $q,
                'type'       => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SALE
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result   = $userRepo->getPaginationList($params);

        $query         = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userAdminSaleList'));

        $options = array(
            'url'           => $url,
            'query'         => $query,
            'total_pages'   => isset($result->total_pages) ? $result->total_pages : 0,
            'page'          => $page,
            'pages_display' => 3
        );

        $layoutComponent  = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách Admin SEO',
                'url'    => $this->url->get(['for' => 'userAdminSaleList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q

        ]);
        $this->view->pick(parent::$theme . '/user_admin/admin_sale_list');
    }
}
