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

    public function addAdminAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $userSession = $this->session->get('USER');

        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q      = $this->request->getQuery('q', array('striptags', 'trim'), '');

        if (
            $userSession['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
            && $filter == 'super_admin_list'
        ) {
            throw new \Exception('Bạn không có quyền tạo tài khoản Super Admin');
        }

        $membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN;

        switch ($filter) {
            default:
            case 'super_admin_list':
                $membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN;
                break;

            case 'admin_list':
                $membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN;
                break;

            case 'admin_editor_list':
                $membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR;
                break;

            case 'admin_seo_list':
                $membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO;
                break;

            case 'admin_sale_list':
                $membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SALE;
                break;
        }

        $user             = new \ITECH\Data\Model\UserModel;
        $user->membership = $membership;
        $user->status     = \ITECH\Data\Lib\Constant::USER_STATUS_ACTIVE;

        $form = new \ITECH\Admin\Form\AdminForm($user);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $has = 0;

                if ($this->request->getPost('email') != '') {
                    $has = \ITECH\Data\Model\UserModel::count(array(
                        'conditions' => 'email = :email:',
                        'bind' => array('email' => $this->request->getPost('email'))
                    ));
                }

                if ($has > 0) {
                    $this->flashSession->error('Email này đã được sử dụng.');
                } else {
                    $user->password    = \ITECH\Data\Lib\Util::hashPassword($user->password);
                    $user->name        = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                    $user->display     = $user->name;
                    $user->slug        = \ITECH\Data\Lib\Util::slug($user->name);
                    $user->gender      = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
                    $user->type        = \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR;
                    $user->membership  = $membership;
                    $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                    $user->created_at  = date('Y-m-d H:i:s');
                    $user->updated_at  = date('Y-m-d H:i:s');

                    if (!$user->create()) {
                        $messages = $user->getMessages();

                        if (isset($messages[0])) {
                            $user->password = $this->request->getPost('password');
                            $this->flashSession->error($messages[0]->getMessage());
                        }
                    } else {
                        $request_uri = $this->config->application->protocol . $this->request->getHttpHost() . $this->request->getServer('REQUEST_URI');

                        if ($request_uri != $this->url->get(array('for' => 'home')) && $request_uri != $this->url->get(array('for' => 'user_login'))) {
                            $referralUrl = $request_uri;
                        } else {
                            $referralUrl = $this->url->get(array('for' => 'home'));
                        }

                        $userLogModel               = new \ITECH\Data\Model\UserLogModel();
                        $userLogModel->user_id      = $userSession['id'];
                        $userLogModel->action       = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_ADD_USER;
                        $userLogModel->referral_url = $referralUrl;
                        $userLogModel->user_agent   = $this->request->getUserAgent();
                        $userLogModel->ip           = $this->request->getClientAddress();

                        $post = array(
                            'id'           => $user->id,
                            'username'     => $user->username,
                            'referral_url' => $referralUrl,
                            'user_agent'   => $this->request->getUserAgent(),
                            'ip'           => $this->request->getClientAddress(),
                            'logined_at'   => $user->logined_at
                        );

                        $userLogModel->log_data   = json_encode(array('[UserController][addAdminAction]' => $post), JSON_UNESCAPED_UNICODE);
                        $userLogModel->created_at = date('Y-m-d H:i:s');

                        if (!$userLogModel->create()) {
                            $messages = $userLogModel->getMessages();

                            if (isset($messages[0])) {
                                $this->logger->log('[UserController][addAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                            }
                        }

                        $this->flashSession->success('Thêm thành công.');

                        return $this->response->redirect(array('for' => 'user_edit_admin', 'query' => '?' . http_build_query(array('id' => $user->id, 'filter' => $filter))));
                    }
                }
            }
        }

        $for      = 'userSuperAdminList';
        $title    = 'Danh sách Super Admin';
        $addTitle = 'Thêm Super Admin';

        switch ($filter) {
            default:
            case 'super_admin_list':
                $for      = 'userSuperAdminList';
                $title    = 'Danh sách Super Admin';
                $addTitle = 'Thêm Super Admin';
                break;

            case 'admin_list':
                $for      = 'userAdminList';
                $title    = 'Danh sách Admin';
                $addTitle = 'Thêm Admin';
                break;

            case 'admin_editor_list':
                $for      = 'userAdminEditorList';
                $title    = 'Danh sách Admin Editor';
                $addTitle = 'Thêm Admin Editor';
                break;

            case 'admin_seo_list':
                $for      = 'userAdminSeoList';
                $title    = 'Danh sách Admin SEO';
                $addTitle = 'Thêm Admin SEO';
                break;

            case 'admin_sale_list':
                $for      = 'userAdminSaleList';
                $title    = 'Danh sách Admin Sale';
                $addTitle = 'Thêm Admin Sale';
                break;
        }

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'   => $title,
                'url'     => $this->url->get(['for' => $for]),
                'active'  => false
            ],
            [
                'title'  => $addTitle,
                'url'    => $this->url->get(['for' => 'user_add_admin', 'query' => '?' . http_build_query(['filter' => $filter])]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'filter'      => $filter,
            'q'           => $q,
            'form'        => $form,
            'addTitle'    => $addTitle,
            'urlFor'      => $for
        ));
        $this->view->pick(parent::$theme . '/user_admin/add_admin');
    }

    public function editAdminAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $filter      = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q           = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $userSession = $this->session->get('USER');
        $id          = $this->request->getQuery('id', array('int'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => '
                id       = :id: 
                AND type = :type:
            ',
            'bind' => array(
                'id'   => $id,
                'type' => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR
            )
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        if ($user->id == $userSession['id']) {
            return $this->response->redirect(array('for' => 'user_profile_admin', 'query' => '?' . http_build_query(array('q' => $q))));
        }

        if (
            $userSession['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
            && $user->membership == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
        ) {
            throw new \Exception('Bạn không có quyền chỉnh sửa tài khoản Super Admin');
        }

        $projectIds = [];
        $projects   = \ITECH\Data\Model\UserProjectModel::find([
            'conditions' => 'userId = :userId:',
            'bind'       => ['userId' => $user->id]
        ]);

        if (count($projects)) {
            foreach ($projects as $item) {
                $projectIds[] = $item->projectId;
            }
        }

        $form = new \ITECH\Admin\Form\AdminForm($user, array(
            'edit'        => true,
            'userSession' => $userSession,
            'user'        => $user
        ));

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $has = 0;

                if ($this->request->getPost('email') != '') {
                    $has = \ITECH\Data\Model\UserModel::count(array(
                        'conditions' => '
                            email  = :email: 
                            AND id <> :user_id:
                        ',
                        'bind' => array(
                            'email'   => $this->request->getPost('email'),
                            'user_id' => $user->id
                        )
                    ));
                }

                if ($has > 0) {
                    $this->flashSession->error('Email này đã được sử dụng.');
                } else {
                    if ($this->request->hasFiles() == true) {
                        $file = $this->request->getUploadedFiles('file_avatar');

                        if (isset($file[0]) && $file[0]->getName() != '') {
                            parent::deleteImageFromCdn('avatar', $user->avatar);

                            $user->avatar = null;
                            $user->update();

                            $resource = array(
                                'name'     => $file[0]->getName(),
                                'type'     => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error'    => $file[0]->getError(),
                                'size'     => $file[0]->getSize()
                            );

                            $response = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', 200, $resource);

                            if (isset($response['status']) && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                                $user->avatar = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $response['result'];
                                parent::uploadImageToCdn(ROOT . '/web/admin/asset/upload/', 'avatar', $response['result']);
                                parent::deleteImageFromLocal(ROOT . '/web/admin/asset/upload/', $response['result']);
                            }
                        }
                    }

                    if ($user->birthday != '') {
                        if (strtotime($user->birthday)) {
                            $user->birthday = date('Y-m-d', strtotime($user->birthday));
                        } else {
                            $user->birthday = null;
                        }
                    } else {
                        $user->birthday = null;
                    }

                    $user->name        = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                    $user->display     = $user->name;
                    $user->slug        = \ITECH\Data\Lib\Util::slug($user->name);
                    $user->type        = \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR;
                    $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                    $user->updated_at  = date('Y-m-d H:i:s');

                    if ($user->status == \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED) {
                        $user->display  = md5(uniqid() . $user->username);
                        $user->username = md5(uniqid() . $user->username);
                    }

                    if ($this->request->getPost('new_password') != '') {
                        $user->password     = \ITECH\Data\Lib\Util::hashPassword($this->request->getPost('new_password'));
                        $user->password_raw = $this->request->getPost('new_password');
                    }

                    if (
                        $userSession['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
                        ||
                        (
                            $userSession['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                            && $user->membership != \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
                            && $user->membership != \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
                        )
                    ) {
                        $query = 'DELETE FROM `land_user_project` WHERE `userId` = ' . $user->id;

                        $userProject = new \ITECH\Data\Model\UserProjectModel;
                        $userProject->getWriteConnection()->query($query);
                    }

                    if ($user->membership != \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN) {
                        if ($this->request->getPost('projectIds')) {
                            $projectIds = $this->request->getPost('projectIds');
                            $projectIds = array_unique(array_filter($projectIds));

                            if (count($projectIds)) {
                                foreach ($projectIds as $item) {
                                    $userProject = new \ITECH\Data\Model\UserProjectModel;
                                    $userProject->userId = $user->id;
                                    $userProject->projectId = $item;
                                    $userProject->save();
                                }
                            }
                        }
                    }

                    if (!$user->update()) {
                        $messages = $user->getMessages();

                        if (isset($messages[0])) {
                            $this->flashSession->error($messages[0]->getMessage());
                        }
                    } else {
                        $request_uri = $this->config->application->protocol . $this->request->getHttpHost() . $this->request->getServer('REQUEST_URI');
                        if ($request_uri != $this->url->get(array('for' => 'home')) && $request_uri != $this->url->get(array('for' => 'user_login'))) {
                            $referralUrl = $request_uri;
                        } else {
                            $referralUrl = $this->url->get(array('for' => 'home'));
                        }

                        $userLogModel               = new \ITECH\Data\Model\UserLogModel();
                        $userLogModel->user_id      = $userSession['id'];
                        $userLogModel->action       = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_EDIT_USER;
                        $userLogModel->referral_url = $referralUrl;
                        $userLogModel->user_agent   = $this->request->getUserAgent();
                        $userLogModel->ip           = $this->request->getClientAddress();

                        $post = array(
                            'id'           => $user->id,
                            'username'     => $user->username,
                            'referral_url' => $referralUrl,
                            'user_agent'   => $this->request->getUserAgent(),
                            'ip'           => $this->request->getClientAddress(),
                            'logined_at'   => $user->logined_at
                        );

                        $userLogModel->log_data   = json_encode(array('[UserController][editAdminAction]' => $post), JSON_UNESCAPED_UNICODE);
                        $userLogModel->created_at = date('Y-m-d H:i:s');

                        if (!$userLogModel->create()) {
                            $messages = $userLogModel->getMessages();

                            if (isset($messages[0])) {
                                $this->logger->log('[UserController][editAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                            }
                        }

                        $this->flashSession->success('Cập nhật thành công.');

                        return $this->response->redirect(array('for' => 'user_edit_admin', 'query' => '?' . http_build_query(array('id' => $id, 'q' => $q, 'filter' => $filter))));
                    }
                }
            }
        }

        $for       = 'userSuperAdminList';
        $title     = 'Danh sách Super Admin';
        $editTitle = 'Chỉnh sửa Super Admin';

        switch ($filter) {
            default:
            case 'super_admin_list':
                $for      = 'userSuperAdminList';
                $title    = 'Danh sách Super Admin';
                $editTitle = 'Chỉnh sửa Super Admin';
                break;

            case 'admin_list':
                $for      = 'userAdminList';
                $title    = 'Danh sách Admin';
                $editTitle = 'Chỉnh sửa Admin';
                break;

            case 'admin_editor_list':
                $for      = 'userAdminEditorList';
                $title    = 'Danh sách Admin Editor';
                $editTitle = 'Chỉnh sửa Admin Editor';
                break;

            case 'admin_seo_list':
                $for      = 'userAdminSeoList';
                $title    = 'Danh sách Admin SEO';
                $editTitle = 'Chỉnh sửa Admin SEO';
                break;

            case 'admin_sale_list':
                $for      = 'userAdminSaleList';
                $title    = 'Danh sách Admin Sale';
                $editTitle = 'Chỉnh sửa Admin Sale';
                break;
        }

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => $title,
                'url'    => $this->url->get(['for' => $for]),
                'active' => false
            ],
            [
                'title' => $editTitle,
                'url'   => $this->url->get([
                    'for'   => 'user_edit_admin',
                    'query' => '?' . http_build_query(['id' => $user->id, 'filter' => $filter])
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'q'           => $q,
            'filter'      => $filter,
            'user'        => $user,
            'form'        => $form,
            'userSession' => $userSession,
            'urlFor'      => $for,
            'editTitle'   => $editTitle,
            'projectIds'  => $projectIds
        ));
        $this->view->pick(parent::$theme . '/user_admin/edit_admin');
    }

    public function deleteAdminAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $userSession = $this->session->get('USER');

        $id     = $this->request->getQuery('id', array('int'), '');
        $q      = $this->request->getQuery('q', array('striptags', 'trim', 'lower'), '');
        $filter = $this->request->getQuery('filter', array('striptags', 'trim', 'lower'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind'       => array('id' => $id)
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        if ($user->id == $userSession['id']) {
            throw new \Phalcon\Exception('Bạn không thể xoá tài khoản này.');
        }

        if (
            $userSession['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
            && $user->membership == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
        ) {
            throw new \Exception('Bạn không có quyền xoá tài khoản này.');
        }

        $user->status     = \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED;
        $user->updated_at = date('Y-m-d H:i:s');

        if (!$user->update()) {
            $messages = $user->getMessages();

            if (isset($messages[0])) {
                $this->flashSession->error($messages[0]->getMessage());
            }
        } else {
            $request_uri = $this->config->application->protocol . $this->request->getHttpHost() . $this->request->getServer('REQUEST_URI');

            if ($request_uri != $this->url->get(array('for' => 'home')) && $request_uri != $this->url->get(array('for' => 'user_login'))) {
                $referralUrl = $request_uri;
            } else {
                $referralUrl = $this->url->get(array('for' => 'home'));
            }

            $userLogModel               = new \ITECH\Data\Model\UserLogModel();
            $userLogModel->user_id      = $userSession['id'];
            $userLogModel->action       = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_REMOVE_USER;
            $userLogModel->referral_url = $referralUrl;
            $userLogModel->user_agent   = $this->request->getUserAgent();
            $userLogModel->ip           = $this->request->getClientAddress();

            $post = array(
                'id'           => $user->id,
                'username'     => $user->username,
                'referral_url' => $referralUrl,
                'user_agent'   => $this->request->getUserAgent(),
                'ip'           => $this->request->getClientAddress(),
                'logined_at'   => $user->logined_at
            );

            $userLogModel->log_data = json_encode(array('[UserController][deleteAdminAction]' => $post), JSON_UNESCAPED_UNICODE);
            $userLogModel->created_at = date('Y-m-d H:i:s');

            if (!$userLogModel->create()) {
                $messages = $userLogModel->getMessages();

                if (isset($messages[0])) {
                    $this->logger->log('[UserController][deleteAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                }
            }

            $this->flashSession->success('Xoá thành công.');
        }

        $for = 'userSuperAdminList';

        switch ($filter) {
            default:
            case 'super_admin_list':
                $for = 'userSuperAdminList';
                break;

            case 'admin_list':
                $for = 'userAdminList';
                break;

            case 'admin_editor_list':
                $for = 'userAdminEditorList';
                break;

            case 'admin_seo_list':
                $for = 'userAdminSeoList';
                break;

            case 'admin_sale_list':
                $for = 'userAdminSaleList';
                break;
        }

        return $this->response->redirect(array('for' => $for, 'query' => '?' . http_build_query(array('q' => $q))));
    }
}
