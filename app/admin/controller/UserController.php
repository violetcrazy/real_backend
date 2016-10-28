<?php
namespace ITECH\Admin\Controller;

class UserController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function loginAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $referralUrl     = $this->request->getQuery('referral_url', array('striptags', 'trim'), '');

        $form = new \ITECH\Admin\Form\LoginForm();

        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $username  = $this->request->getPost('username');
                $password  = $this->request->getPost('password');
                $userAgent = $this->request->getUserAgent();
                $ip        = $this->request->getClientAddress();

                $url = $this->config->application->api_url . 'user/login?authorized_token=' . $authorizedToken;
                $post = array(
                    'username'     => $username,
                    'password'     => $password,
                    'application'  => 'web',
                    'referral_url' => $this->url->get(array('for' => 'home')),
                    'user_agent'   => $userAgent,
                    'ip'           => $ip,
                    'type'         => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);

                if (
                    isset($r['result'])
                    && count($r['result'])
                    && isset($r['status'])
                    && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS
                ) {
                    if (!parent::setUserSession($r['result'])) {
                        $this->flashSession->error('Không thể tạo session hoặc cookie.');
                    } else {
                        if ($referralUrl != '') {
                            return $this->response->redirect($referralUrl);
                        } else {
                            return $this->response->redirect(array('for' => 'home'));
                        }
                    }
                } else {
                    $this->flashSession->error($r['message']);
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/user/login');
    }

    public function logoutAction()
    {
        $this->session->remove('USER');
        $cookie = $this->cookies->get('USER');
        $cookie->delete();

        return $this->response->redirect(array('for' => 'user_login'));
    }

    public function memberListAction()
    {
        parent::authenticateUser();

        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'    => $q,
                'type' => \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result = $userRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userMemberList'));

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
                'title'  => 'Danh sách thành viên',
                'url'    => $this->url->get(['for' => 'userMemberList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q
        ]);
        $this->view->pick(parent::$theme . '/user/member_list');
    }

    public function memberRemovedListAction()
    {
        parent::authenticateUser();

        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'      => $q,
                'type'   => \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER,
                'status' => \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result = $userRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userMemberRemovedList'));

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
                'title'  => 'Danh sách thành viên đã xóa',
                'url'    => $this->url->get(['for' => 'userMemberList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q
        ]);
        $this->view->pick(parent::$theme . '/user/member_removed_list');
    }

    public function deleteAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $userSession = $this->session->get('USER');

        $id = $this->request->getQuery('id', array('int'), '');
        $q = $this->request->getQuery('q', array('striptags', 'trim', 'lower'), '');
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

        //$user->username = md5(uniqid() . $user->username);
        //$user->display  = md5(uniqid() . $user->username);
        //$user->email    = null;
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

            $userLogModel->log_data = json_encode(array('[UserController][deleteAction]' => $post), JSON_UNESCAPED_UNICODE);
            $userLogModel->created_at = date('Y-m-d H:i:s');

            if (!$userLogModel->create()) {
                $messages = $userLogModel->getMessages();

                if (isset($messages[0])) {
                    $this->logger->log('[UserController][deleteAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
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

            case 'member_list':
                $for = 'userMemberList';
            break;

            case 'agent_list':
                $for = 'userAgentList';
            break;
        }

        return $this->response->redirect(array('for' => $for, 'query' => '?' . http_build_query(array('q' => $q))));
    }

    public function deleteAvatarAction()
    {
        parent::authenticateUser();

        $userId = $this->request->getQuery('user_id', array('int'), '');
        $from = $this->request->getQuery('from', array('striptags', 'trim', 'lower'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $userId)
        ));

        if (!$user) {
            throw new \Exception('Không tồn tại thành viên này.');
        }

        parent::deleteImageFromCdn('avatar', $user->avatar);

        $user->avatar = null;
        $user->updated_at = date('Y-m-d H:i:s');

        if (!$user->save()) {
            $messages = $user->getMessages();
            $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xoá.';

            throw new \Exception($m);
        } else {
            $this->flashSession->success('Xoá thành công.');

            if ($from == 'agent') {
                return $this->response->redirect(array('for' => 'user_edit_agent', 'query' => '?' . http_build_query(array('id' => $userId))));
            }
        }
    }

    public function addAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        //$userSession = $this->session->get('USER');

        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $user = new \ITECH\Data\Model\UserModel();
        $type = \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR;
        $form = new \ITECH\Admin\Form\AdminForm($user);

        switch ($filter) {
            case 'admin' :
                $form = new \ITECH\Admin\Form\AdminForm($user);
                $type = \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR;
            break;

            case 'agent' :
                $form = new \ITECH\Admin\Form\AgentForm($user);
                $type = \ITECH\Data\Lib\Constant::USER_TYPE_AGENT;
            break;

            case 'member' :
                $form = new \ITECH\Admin\Form\MemberForm($user);
                $type = \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER;
            break;
        }

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $url = $this->config->application->api_url . 'user/add?authorized_token=' . $authorizedToken;
                $post = array(
                    'name' => $this->request->getPost('name'),
                    'password' => $this->request->getPost('password'),
                    'email' => $this->request->getPost('email'),
                    'username' => $this->request->getPost('username'),
                    'membership' => $this->request->getPost('membership'),
                    'status' => $this->request->getPost('status'),
                    'type' => $type
                );
                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Thêm thành viên thành công.');
                    $query = array(
                        'filter' => $filter,
                        'id' => $r['result']['id']
                    );
                    return $this->response->redirect(array('for' => 'user_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, thêm thành viên không thành công.');
                    }
                }
            }
        }

        $view             = '/user/add_admin';
        $titleBreadcrumbs = 'Thêm Thành viên';

        switch ($filter) {
            case 'admin' :
                $view = '/user/add_admin';
                $titleBreadcrumbs = 'Thêm Thành viên';
            break;

            case 'agent' :
                $view = '/user/add_agent';
                $titleBreadcrumbs = 'Thêm Đại lí';
            break;

            case 'member' :
                $view = '/user/add_member';
                $titleBreadcrumbs = 'Thêm quản trị viên';
            break;
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách quản trị viên',
                'url' => $this->url->get([
                    'for' => 'userAdminList',
                ]),
                'active' => false
            ],
            [
                'title' => $titleBreadcrumbs,
                'url' => $this->url->get([
                    'for' => 'user_add',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'filter' => $filter,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . $view);
    }

    public function addAgentAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        //$authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');

        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $user = new \ITECH\Data\Model\UserModel();
        $form = new \ITECH\Admin\Form\AgentForm($user);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $has = 0;
                if ($this->request->getPost('email') != '') {
                    $has = \ITECH\Data\Model\UserModel::count(array(
                        'conditions' => 'email = :email:',
                        'bind' => array(
                            'email' => $this->request->getPost('email')
                        )
                    ));
                }
                if ($has > 0) {
                    $this->flashSession->error('Email này đã được sử dụng.');
                } else {
                    $user->password = \ITECH\Data\Lib\Util::hashPassword($user->password);
                    $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                    $user->display = $user->name;
                    $user->slug = \ITECH\Data\Lib\Util::slug($user->name);
                    $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
                    $user->type = \ITECH\Data\Lib\Constant::USER_TYPE_AGENT;
                    $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                    $user->created_at = date('Y-m-d H:i:s');
                    $user->updated_at = date('Y-m-d H:i:s');
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

                        $userLogModel = new \ITECH\Data\Model\UserLogModel();
                        $userLogModel->user_id = $userSession['id'];
                        $userLogModel->action = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_ADD_USER;
                        $userLogModel->referral_url = $referralUrl;
                        $userLogModel->user_agent = $this->request->getUserAgent();
                        $userLogModel->ip = $this->request->getClientAddress();

                        $post = array(
                            'id' => $user->id,
                            'username' => $user->username,
                            'referral_url' => $referralUrl,
                            'user_agent' => $this->request->getUserAgent(),
                            'ip' => $this->request->getClientAddress(),
                            'logined_at' => $user->logined_at
                        );
                        $userLogModel->log_data = json_encode(array(
                            '[UserController][addAdminAction]' => $post
                        ), JSON_UNESCAPED_UNICODE);
                        $userLogModel->created_at = date('Y-m-d H:i:s');

                        if (!$userLogModel->create()) {
                            $messages = $userLogModel->getMessages();
                            if (isset($messages[0])) {
                                $this->logger->log('[UserController][addAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                            }
                        }

                        $this->flashSession->success('Thêm thành công.');
                        return $this->response->redirect(array('for' => 'user_edit_agent', 'query' => '?' . http_build_query(array('id' => $user->id, 'filter' => 'agent'))));
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách đại lý',
                'url' => $this->url->get([
                    'for' => 'userAgentList',
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm Đại lí',
                'url' => $this->url->get([
                    'for' => 'user_add_agent',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'filter' => $filter,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/user/add_agent');
    }

    public function agentListAction()
    {
        parent::authenticateUser();

        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'    => $q,
                'type' => \ITECH\Data\Lib\Constant::USER_TYPE_AGENT
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result = $userRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userAgentList'));

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
                'title'  => 'Danh sách đại lý',
                'url'    => $this->url->get(['for' => 'userAgentList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q
        ]);
        $this->view->pick(parent::$theme . '/user/agent_list');
    }

    public function agentRemovedListAction()
    {
        parent::authenticateUser();

        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'q'      => $q,
                'type'   => \ITECH\Data\Lib\Constant::USER_TYPE_AGENT,
                'status' => \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED
            ),
            'page'  => $page,
            'limit' => $limit
        );

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $result = $userRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'userAgentRemovedList'));

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
                'title'  => 'Danh sách đại lý đã xóa',
                'url'    => $this->url->get(['for' => 'userAgentRemovedList']),
                'active' => true
            ]
        ];

        $this->view->setVars([
            'breadcrumbs'      => $breadcrumbs,
            'result'           => $result->items,
            'paginationLayout' => $paginationLayout,
            'q'                => $q
        ]);
        $this->view->pick(parent::$theme . '/user/agent_removed_list');
    }

    public function editAgentAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id: AND type = :type:',
            'bind' => array(
                'id' => $id,
                'type' => \ITECH\Data\Lib\Constant::USER_TYPE_AGENT
            )
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        if ($user->id == $userSession['id']) {
            return $this->response->redirect(array('for' => 'user_profile_agent', 'query' => '?' . http_build_query(array('q' => $q, 'filter' => $filter))));
        }

        $form = new \ITECH\Admin\Form\AgentForm($user, array('edit' => true));
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $has = 0;
                if ($this->request->getPost('email') != '') {
                    $has = \ITECH\Data\Model\UserModel::count(array(
                        'conditions' => 'email = :email: AND id <> :user_id:',
                        'bind' => array(
                            'email' => $this->request->getPost('email'),
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
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
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

                    $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                    $user->display = $user->name;
                    $user->slug = \ITECH\Data\Lib\Util::slug($user->name);
                    $user->type = \ITECH\Data\Lib\Constant::USER_TYPE_AGENT;
                    $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                    $user->updated_at = date('Y-m-d H:i:s');

                    if ($user->status == \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED) {
                        $user->display = md5(uniqid() . $user->username);
                        $user->username = md5(uniqid() . $user->username);
                    }

                    if ($this->request->getPost('new_password') != '') {
                        $user->password = \ITECH\Data\Lib\Util::hashPassword($this->request->getPost('new_password'));
                        $user->password_raw = $this->request->getPost('new_password');
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

                        $userLogModel = new \ITECH\Data\Model\UserLogModel();
                        $userLogModel->user_id = $userSession['id'];
                        $userLogModel->action = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_EDIT_USER;
                        $userLogModel->referral_url = $referralUrl;
                        $userLogModel->user_agent = $this->request->getUserAgent();
                        $userLogModel->ip = $this->request->getClientAddress();

                        $post = array(
                            'id' => $user->id,
                            'username' => $user->username,
                            'referral_url' => $referralUrl,
                            'user_agent' => $this->request->getUserAgent(),
                            'ip' => $this->request->getClientAddress(),
                            'logined_at' => $user->logined_at
                        );
                        $userLogModel->log_data = json_encode(array('[UserController][editAdminAction]' => $post), JSON_UNESCAPED_UNICODE);
                        $userLogModel->created_at = date('Y-m-d H:i:s');

                        if (!$userLogModel->create()) {
                            $messages = $userLogModel->getMessages();
                            if (isset($messages[0])) {
                                $this->logger->log('[UserController][editAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                            }
                        }

                        $this->flashSession->success('Cập nhật thành công.');
                        return $this->response->redirect(array('for' => 'user_edit_agent', 'query' => '?' . http_build_query(array('id' => $id, 'q' => $q, 'filter' => $filter))));
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách đại lý',
                'url'    => $this->url->get(['for' => 'userAgentList']),
                'active' => false
            ],
            [
                'title' => $user->name,
                'url'   => $this->url->get([
                    'for' => 'user_edit_agent',
                    'id'  => $user->id
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
            'userSession' => $userSession
        ));
        $this->view->pick(parent::$theme . '/user/edit_agent');
    }

    public function editMemberAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id: AND type = :type:',
            'bind' => array(
                'id' => $id,
                'type' => \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER
            )
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }
        if ($user->id == $userSession['id']) {
            return $this->response->redirect(array('for' => 'user_profile_agent', 'query' => '?' . http_build_query(array('q' => $q, 'filter' => $filter))));
        }

        $form = new \ITECH\Admin\Form\MemberForm($user, array('edit' => true));
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $has = 0;
                if ($this->request->getPost('email') != '') {
                    $has = \ITECH\Data\Model\UserModel::count(array(
                        'conditions' => 'email = :email: AND id <> :user_id:',
                        'bind' => array(
                            'email' => $this->request->getPost('email'),
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
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
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

                    $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                    $user->display = $user->name;
                    $user->slug = \ITECH\Data\Lib\Util::slug($user->name);
                    $user->type = \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER;
                    $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                    $user->updated_at = date('Y-m-d H:i:s');

                    if ($user->status == \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED) {
                        $user->display = md5(uniqid() . $user->username);
                        $user->username = md5(uniqid() . $user->username);
                    }

                    if ($this->request->getPost('new_password') != '') {
                        $user->password = \ITECH\Data\Lib\Util::hashPassword($this->request->getPost('new_password'));
                        $user->password_raw = $this->request->getPost('new_password');
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

                        $userLogModel = new \ITECH\Data\Model\UserLogModel();
                        $userLogModel->user_id = $userSession['id'];
                        $userLogModel->action = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_EDIT_USER;
                        $userLogModel->referral_url = $referralUrl;
                        $userLogModel->user_agent = $this->request->getUserAgent();
                        $userLogModel->ip = $this->request->getClientAddress();

                        $post = array(
                            'id' => $user->id,
                            'username' => $user->username,
                            'referral_url' => $referralUrl,
                            'user_agent' => $this->request->getUserAgent(),
                            'ip' => $this->request->getClientAddress(),
                            'logined_at' => $user->logined_at
                        );
                        $userLogModel->log_data = json_encode(array(
                            '[UserController][editAdminAction]' => $post
                        ), JSON_UNESCAPED_UNICODE);
                        $userLogModel->created_at = date('Y-m-d H:i:s');
                        if (!$userLogModel->create()) {
                            $messages = $userLogModel->getMessages();
                            if (isset($messages[0])) {
                                $this->logger->log('[UserController][editAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                            }
                        }

                        $this->flashSession->success('Cập nhật thành công.');
                        return $this->response->redirect(array('for' => 'user_edit_member', 'query' => '?' . http_build_query(array('id' => $id, 'q' => $q, 'filter' => $filter))));
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách thành viên',
                'url' => $this->url->get([
                    'for' => 'userMemberList',
                ]),
                'active' => false
            ],
            [
                'title' => $user->name,
                'url' => $this->url->get([
                    'for' => 'user_edit_member',
                    'id' => $user->id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'q' => $q,
            'filter' => $filter,
            'user' => $user,
            'form' => $form,
            'userSession' => $userSession
        ));
        $this->view->pick(parent::$theme . '/user/edit_member');
    }

    public function addMemberAction()
    {
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        //$authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');

        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $user = new \ITECH\Data\Model\UserModel();
        $form = new \ITECH\Admin\Form\MemberForm($user);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $has = 0;
                if ($this->request->getPost('email') != '') {
                    $has = \ITECH\Data\Model\UserModel::count(array(
                        'conditions' => 'email = :email:',
                        'bind' => array(
                            'email' => $this->request->getPost('email')
                        )
                    ));
                }
                if ($has > 0) {
                    $this->flashSession->error('Email này đã được sử dụng.');
                } else {
                    $user->password = \ITECH\Data\Lib\Util::hashPassword($user->password);
                    $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                    $user->display = $user->name;
                    $user->slug = \ITECH\Data\Lib\Util::slug($user->name);
                    $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
                    $user->type = \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER;
                    $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                    $user->created_at = date('Y-m-d H:i:s');
                    $user->updated_at = date('Y-m-d H:i:s');
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

                        $userLogModel = new \ITECH\Data\Model\UserLogModel();
                        $userLogModel->user_id = $userSession['id'];
                        $userLogModel->action = \ITECH\Data\Lib\Constant::USER_LOG_TYPE_ADD_USER;
                        $userLogModel->referral_url = $referralUrl;
                        $userLogModel->user_agent = $this->request->getUserAgent();
                        $userLogModel->ip = $this->request->getClientAddress();

                        $post = array(
                            'id' => $user->id,
                            'username' => $user->username,
                            'referral_url' => $referralUrl,
                            'user_agent' => $this->request->getUserAgent(),
                            'ip' => $this->request->getClientAddress(),
                            'logined_at' => $user->logined_at
                        );
                        $userLogModel->log_data = json_encode(array(
                            '[UserController][addAdminAction]' => $post
                        ), JSON_UNESCAPED_UNICODE);
                        $userLogModel->created_at = date('Y-m-d H:i:s');

                        if (!$userLogModel->create()) {
                            $messages = $userLogModel->getMessages();
                            if (isset($messages[0])) {
                                $this->logger->log('[UserController][addAdminAction] ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                            }
                        }

                        $this->flashSession->success('Thêm thành công.');
                        return $this->response->redirect(array('for' => 'user_edit_member', 'query' => '?' . http_build_query(array('id' => $user->id, 'filter' => 'member'))));
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách thành viên',
                'url' => $this->url->get([
                    'for' => 'userMemberList',
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm Thành viên',
                'url' => $this->url->get([
                    'for' => 'user_add_member',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'filter' => $filter,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/user/add_member');
    }

    public function adminProfileAction()
    {
        parent::authenticateUser();

        //$authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $filter = $this->request->getQuery('filter', array('striptags', 'trim'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $userSession['id'])
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        $form = new \ITECH\Admin\Form\UserProfileForm($user);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {

                if ($this->request->hasFiles() == true) {
                    $file = $this->request->getUploadedFiles('file_avatar');
                    if (isset($file[0]) && $file[0]->getName() != '') {
                        parent::deleteImageFromCdn('avatar', $user->avatar);
                        $user->avatar = null;
                        $user->update();

                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        $response = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', 200, $resource);
                        if (isset($response['status']) && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                            $user->avatar = date('Y') . '/' . date('m') . '/' . $response['result'];
                            parent::uploadImageToCdn(ROOT . '/web/admin/asset/upload/', 'avatar', $response['result']);
                            parent::deleteImageFromLocal(ROOT . '/web/admin/asset/upload/', $response['result']);
                        }
                    }
                }

                $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($user->name);
                $user->updated_at = date('Y-m-d H:i:s');

                if ($this->request->getPost('new_password') != '') {
                    $user->password = \ITECH\Data\Lib\Util::hashPassword($this->request->getPost('new_password'));
                }

                if (!$user->update()) {
                    $messages = $user->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $user->membership_value = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN;
                    $avatar_url = null;
                    if ($user->avatar != '') {
                        $avatar_url = $this->config->asset->frontend_url . 'upload/avatar/' . $user->avatar;
                    }
                    $user->avatar_url = $avatar_url;
                    if (!parent::setUserSessionOject($user)) {
                        $this->flashSession->error('Không thể tạo session hoặc cookie.');
                    } else {
                        $this->flashSession->success('Cập nhật thành công.');
                        $this->response->redirect(array('for' => 'user_profile_admin', 'query' => '?' . http_build_query(array('q' => $q, 'filter' => $filter))));
                    }
                }
            }
        }

        $this->view->setVars(array(
            'q' => $q,
            'filter' => $filter,
            'form' => $form,
            'user' => $user
        ));
        $this->view->pick(parent::$theme . '/user/profile');
    }

    public function addMessageAction()
    {
        parent::authenticateUser();

        $userSession = $this->session->get('USER');
        $uid = $this->request->getQuery('uid', array('int'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $uid
            )
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        $message = new \ITECH\Data\Model\MessageModel();
        $form = new \ITECH\Admin\Form\UserMessageForm($message, $this);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->description = $this->request->getPost('description');
                $message->type = \ITECH\Data\Lib\Constant::MESSAGE_INBOX_TYPE_USER_SEND;
                $message->created_by = $userSession['id'];
                $message->updated_by = $userSession['id'];
                $message->created_at = date('Y-m-d H:i:s');
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->create()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $messageTo = new \ITECH\Data\Model\MessageToModel();
                    $messageTo->message_id = $message->id;
                    $messageTo->user_id = $user->id;
                    if (!$messageTo->create()) {
                        $messages = $messageTo->getMessages();
                        if (isset($messages[0])) {
                            $this->flashSession->error($messages[0]->getMessage());
                        }
                    } else {
                        $this->flashSession->success('Gửi thành công.');
                        return $this->response->redirect(array('for' => 'user_edit_message', 'query' => '?' . http_build_query(array('uid' => $user->id, 'mid' => $message->id))));
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Thông báo',
                'url' => $this->url->get([
                    'for' => 'user_add_message',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'user' => $user
        ));

        $this->view->pick(parent::$theme . '/user/add_message');
    }

    public function editMessageAction()
    {
        parent::authenticateUser();

        $userSession = $this->session->get('USER');
        $mid = $this->request->getQuery('mid', array('int'), '');
        $uid = $this->request->getQuery('uid', array('int'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $uid
            )
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        $message = \ITECH\Data\Model\MessageModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $mid
            )
        ));

        if (!$message) {
            throw new \Phalcon\Exception('Không tồn tại thông báo này.');
        }

        $form = new \ITECH\Admin\Form\UserMessageForm($message);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->description = $this->request->getPost('description');
                $message->updated_by = $userSession['id'];
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->update()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'user_edit_message', 'query' => '?' . http_build_query(array('uid' => $user->id, 'mid' => $message->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'message' => $message,
            'user' => $user
        ));

        $this->view->pick(parent::$theme . '/user/edit_message');
    }

    public function addEmailAction()
    {
        parent::authenticateUser();

        $userSession = $this->session->get('USER');

        $uid = $this->request->getQuery('uid', array('int'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $uid
            )
        ));

        if (!$user) {
            throw new \Phalcon\Exception('Không tồn tại tài khoản này.');
        }

        $message = new \ITECH\Data\Model\MessageModel();
        $form = new \ITECH\Admin\Form\UserEmailForm($message);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->email = $user->email;
                $message->type = \ITECH\Data\Lib\Constant::MESSAGE_INBOX_TYPE_SYSTEM_EMAIL;
                $message->description = $this->request->getPost('description');
                $message->created_by = $userSession['id'];
                $message->updated_by = $userSession['id'];
                $message->created_at = date('Y-m-d H:i:s');
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->create()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $mailer = \ITECH\Data\Lib\Mailer::instance(array(
                        'delivery' => $this->config->mailer->delivery,
                        'ssl' => $this->config->mailer->ssl,
                        'port' => $this->config->mailer->port,
                        'host' => $this->config->mailer->host,
                        'username' => $this->config->mailer->username,
                        'password' => $this->config->mailer->password
                    ));

                    $params = array('conditions' => array('file_name' => 'system_email'));
                    $loadComponent = new \ITECH\Admin\Component\LoadComponent();
                    $config = $loadComponent->getFileJson($params);
                    $to = $message->email;

                    if ($config) {}

                    try {
                        $demo = $mailer->send(
                            array('noreplyestatevn@gmail.com' => 'Thông báo'),
                            array($to => 'Thông báo'),
                            array(),
                            array(),
                            $message->name . ' - ' . date('m-d-Y H:i:s'),
                            file_get_contents(ROOT . '/app/admin/view/default/email/notification.tpl'),
                            array($to => array(
                                '{name}' => $user->name,
                                '{description}' => $message->description
                            ))
                        );

                        if ($demo) {}
                    } catch (Exception $e) {
                        throw new \Phalcon\Exception($e->getMessage());
                    }

                    $this->flashSession->success('Gửi thành công.');

                    if ($user->type == \ITECH\Data\Lib\Constant::USER_TYPE_AGENT) {
                        return $this->response->redirect(array('for' => 'userAgentList'));
                    } elseif ($user->type == \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER) {
                       return $this->response->redirect(array('for' => 'userMemberList'));
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Email thông báo',
                'url' => $this->url->get([
                    'for' => 'user_add_email',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/user/add_email');
    }

    public function editEmailAction()
    {
        parent::authenticateUser();

        $userSession = $this->session->get('USER');

        $id = $this->request->getQuery('id', array('int'), '');

        $message = \ITECH\Data\Model\MessageModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$message) {
            throw new \Phalcon\Exception('Không tồn tại thông báo này.');
        }

        $form = new \ITECH\Admin\Form\EmailForm($message);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->email = $this->request->getPost('email');
                $message->description = $this->request->getPost('description');
                $message->created_by = $userSession['id'];
                $message->updated_by = $userSession['id'];
                $message->created_at = date('Y-m-d H:i:s');
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->update()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Tạo thành công.');
                    return $this->response->redirect(array('for' => 'interaction_edit_email', 'query' => '?' . http_build_query(array('id' => $message->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/user/edit_email');
    }
}
