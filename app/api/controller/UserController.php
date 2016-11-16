<?php
namespace ITECH\Api\Controller;

class UserController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function loginAction()
    {
        $response = array(
            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result'  => array()
        );

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('username', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_USERNAME.'
            )));
            $validator->setFilters('username', array('striptags', 'trim'));

            $validator->add('password', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu.'
            )));

            $validator->add('application', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_APPLICATION_ID.'
            )));
            $validator->setFilters('application', array('striptags', 'trim', 'lower'));

            $validator->add('user_agent', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_USER_AGENT.'
            )));
            $validator->setFilters('user_agent', array('striptags', 'trim'));

            $validator->add('ip', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_IP_ADDRESS.'
            )));
            $validator->setFilters('ip', array('striptags', 'trim'));

            $validator->setFilters('referral_url', array('striptags', 'trim'));
            $validator->setFilters('type', array('striptags', 'trim', 'lower'));

            $messages = $validator->validate($post);
            if (count($messages)) {
                $result = array();

                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result'  => $result
                );

                goto RETURN_RESPONSE;
            }

            $username = $validator->getValue('username');
            $password = \ITECH\Data\Lib\Util::hashPassword($validator->getValue('password'));

            //$user = new \ITECH\Data\Model\UserModel();

            if ($validator->getValue('type') != '') {
                switch ($validator->getValue('type')) {
                    default:
                    case \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER:
                        $actionLog = \ITECH\Data\Lib\Constant::USER_LOG_MEMBER_LOGIN;

                        $username = \ITECH\Data\Lib\Util::numberOnly($validator->getValue('username'));
                        if (substr($username, 0, 2) == '84') {
                            $username = '0' . substr_replace($username, '', 0, 2);
                        }

                        $email = $validator->getValue('username');
                        $user = \ITECH\Data\Model\UserModel::findFirst(array(
                            'conditions' => '
                                (username = :username: OR email = :email:)
                                AND type = :type:
                            ',
                            'bind' => array(
                                'username' => $username,
                                'email'    => $email,
                                'type'     => \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER
                            )
                        ));
                    break;

                    case \ITECH\Data\Lib\Constant::USER_TYPE_AGENT:
                        $actionLog = \ITECH\Data\Lib\Constant::USER_LOG_AGENT_LOGIN;

                        $username = \ITECH\Data\Lib\Util::numberOnly($validator->getValue('username'));
                        if (substr($username, 0, 2) == '84') {
                            $username = '0' . substr_replace($username, '', 0, 2);
                        }

                        $email = $validator->getValue('username');
                        $user = \ITECH\Data\Model\UserModel::findFirst(array(
                            'conditions' => '
                                (username = :username: OR email = :email:)
                                AND type = :type:
                            ',
                            'bind' => array(
                                'username' => $username,
                                'email'    => $email,
                                'type'     => \ITECH\Data\Lib\Constant::USER_TYPE_AGENT
                            )
                        ));
                    break;

                    case \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR:
                        $actionLog = \ITECH\Data\Lib\Constant::USER_LOG_ADMINISTRATOR_LOGIN;

                        $username = $validator->getValue('username');
                        $user = \ITECH\Data\Model\UserModel::findFirst(array(
                            'conditions' => 'username = :username: AND type = :type:',
                            'bind' => array(
                                'username' => $username,
                                'type'     => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR
                            )
                        ));
                    break;
                }
            } else {
                $actionLog = \ITECH\Data\Lib\Constant::USER_LOG_AGENT_LOGIN;

                $email = $validator->getValue('username');
                $user = \ITECH\Data\Model\UserModel::findFirst(array(
                    'conditions' => '
                        (username = :username: OR email = :email:)
                        AND type = :type:
                    ',
                    'bind' => array(
                        'username' => $username,
                        'email'    => $email,
                        'type'     => \ITECH\Data\Lib\Constant::USER_TYPE_AGENT
                    )
                ));
            }

            if (!$user) {
                $response = array(
                    'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại tài khoản này.'
                );

                goto RETURN_RESPONSE;
            }

            if ($user->password != $password) {
                $response = array(
                    'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Mật khẩu không chính xác.'
                );

                goto RETURN_RESPONSE;
            }

            if ($user->status != \ITECH\Data\Lib\Constant::USER_STATUS_ACTIVE) {
                $response = array(
                    'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Tài khoản chưa được quyền sử dụng hoặc đã bị xóa.'
                );

                goto RETURN_RESPONSE;
            }

            $token       = md5(uniqid() . time());
            $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WEB;

            if ($validator->getValue('application') != '') {
                switch ($validator->getValue('application')) {
                    default:
                    case 'web':
                        $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WEB;
                    break;

                    case 'android':
                        $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_ANDROID;
                    break;

                    case 'ios':
                        $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_IOS;
                    break;

                    case 'winphone':
                        $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WINPHONE;
                    break;
                }
            }

            $userAuthenticateModel              = new \ITECH\Data\Model\UserAuthenticateModel();
            $userAuthenticateModel->user_id     = $user->id;
            $userAuthenticateModel->application = $application;
            $userAuthenticateModel->token       = $token;
            $userAuthenticateModel->created_at  = date('Y-m-d H:i:s');
            $userAuthenticateModel->expired_at  = date('Y-m-d H:i:s', strtotime('+7 days'));

            $userLogModel               = new \ITECH\Data\Model\UserLogModel();
            $userLogModel->user_id      = $user->id;
            $userLogModel->action       = $actionLog;
            $userLogModel->referral_url = $validator->getValue('referral_url');
            $userLogModel->user_agent   = $validator->getValue('user_agent');
            $userLogModel->ip           = $validator->getValue('ip');
            $userLogModel->created_at   = date('Y-m-d H:i:s');

            $user->logined_at = date('Y-m-d H:i:s');

            try {
                if (!$user->update()) {
                    $messages = $user->getMessages();

                    if (isset($messages[0])) {
                        $response = array(
                            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $messages[0]->getMessage()
                        );
                    } else {
                        $response = array(
                            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể đăng nhập.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                if (!$userAuthenticateModel->create()) {
                    $messages = $userAuthenticateModel->getMessages();

                    if (isset($messages[0])) {
                        $response = array(
                            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $messages[0]->getMessage()
                        );
                    } else {
                        $response = array(
                            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể đăng nhập.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                if (!$userLogModel->create()) {
                    $messages = $userLogModel->getMessages();

                    if (isset($messages[0])) {
                        $response = array(
                            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $messages[0]->getMessage()
                        );
                    } else {
                        $response = array(
                            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo dữ liệu log.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                $avatar_image_url = null;
                if ($user->avatar_image != '') {
                    $avatar_image_url = $this->config->asset->frontend_url . 'upload/avatar/' . $user->avatar_image;
                }

                $cover_image_url = null;
                if ($user->cover_image != '') {
                    $cover_image_url = $this->config->asset->frontend_url . 'upload/cover/' . $user->cover_image;
                }

                $userGender     = \ITECH\Data\Lib\Constant::getUserGender();
                $userMembership = \ITECH\Data\Lib\Constant::getUserMembership();

                $response = array(
                    'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result'  => array(
                        'id'               => (int)$user->id,
                        'username'         => $user->username,
                        'name'             => $user->name,
                        'slug'             => $user->slug,
                        'email'            => $user->email,
                        'phone'            => $user->phone,
                        'avatar_image'     => $user->avatar_image,
                        'avatar_image_url' => $avatar_image_url,
                        'cover_image'      => $user->cover_image,
                        'cover_image_url'  => $cover_image_url,
                        'gender'           => (int)$user->gender,
                        'gender_text'      => isset($userGender[$user->gender]) ? $userGender[$user->gender] : null,
                        'type'             => $user->type,
                        'membership'       => (int)$user->membership,
                        'membership_text'  => isset($userMembership[$user->membership]) ? $userMembership[$user->membership] : null,
                        'is_verified'      => $user->is_verified,
                        'logined_at'       => $user->logined_at,
                        'token'            => $token
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[UserController][loginAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);

                $response = array(
                    'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }
        } else {
            $response = array(
                'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Invalid POST method.'
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function detailAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $type = $this->request->getQuery('type', array('int'), \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER);
        $cache = $this->request->getQuery('$cache', array('striptags', 'trim', 'lower'), 'false');
        $cacheName = md5(serialize(array(
            'UserModel',
            'findFirst',
            $id
        )));

        $user = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$user) {
            if ($id != '') {
                $user = \ITECH\Data\Model\UserModel::findFirst(array(
                    'conditions' => 'id = :id: AND type = :type:',
                    'bind' => array(
                        'id' => $id,
                        'type' => $type
                    )
                ));
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $user);
            }
        }

        if (!$user) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tài khoản này.'
            );
            goto RETURN_RESPONSE;
        }

        if (!$this->request->isPost()) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => '',
                'result' => $user,
                'update' => false
            );
            goto RETURN_RESPONSE;
        }

        $post = $this->request->getJsonRawBody();
        if ($post == '') {
            $post = $this->request->getPost();
        }

        $validator = new \Phalcon\Validation();
        $validator->add('username', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 5,
            'messageMinimum' => 'Tên nhất phải 5 ký tự.'
        )));
        $validator->setFilters('username', array('striptags', 'trim'));
        $validator->add('name', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 5,
            'messageMinimum' => 'Tên nhất phải 5 ký tự.'
        )));
        $validator->setFilters('name', array('striptags', 'trim'));
        $validator->setFilters('password', array('striptags', 'trim'));
        $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
            'message' => 'Định dạng e-mail không đúng'
        )));
        $validator->setFilters('email', array('striptags', 'trim'));
        $validator->setFilters('membership', array('striptags', 'trim'));
        $validator->setFilters('status', array('striptags', 'trim'));
        $messages = $validator->validate($post);
        if(count($messages)) {
            $result = array();
            foreach ($messages as $message) {
                $result[$message->getField()] = $message->getMessage();
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Thông tin chưa hợp lệ.',
                'result' => $result,
                'update' => false
            );

            goto RETURN_RESPONSE;
        }
        $has = 0;
        if ($this->request->getPost('email') != '') {
            $has = \ITECH\Data\Model\UserModel::count(array(
                'conditions' => 'email = :email:',
                'bind' => array(
                    'email' => $this->request->getPost('email')
                )
            ));
        }
        if ($has > 1) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Đã tồn tại email này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $user = new \ITECH\Data\Model\UserModel();

        $user->password = \ITECH\Data\Lib\Util::hashPassword($validator->getValue('password'));
        $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('name'));
        $user->username = $validator->getValue('username');
        $user->display = $validator->getValue('name');
        $user->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('name'));
        $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
        $user->type = $this->request->getPost('type');
        $user->membership = $validator->getValue('membership');
        $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        if (!$user->update()) {
            $messages = $user->getMessages();
            if (isset($messages[0])) {
                $user->password = $this->request->getPost('password');
                $this->flashSession->error($messages[0]->getMessage());
            }
        } else {
            $this->flashSession->success('Thêm thành công.');
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $user,
                'update' => true
            );

            goto RETURN_RESPONSE;
        }

       RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function listAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $type = $this->request->getQuery('type', array('striptags', 'trim', 'lower'), 3);
        $membership = $this->request->getQuery('membership', array('striptags', 'trim', 'lower'), 'member');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);

        $params = array(
            'limit' => $limit,
            'page' => $page
        );
        switch ($type) {
            default:
            case 3:
                $params['conditions']['type'] = \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER;
                $userMembership = \ITECH\Data\Lib\Constant::getUserMembership();
            break;

            case 1:
                $params['conditions']['type'] = \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR;
                $userMembership = \ITECH\Data\Lib\Constant::getUserMembershipAdministrator();
            break;

            case 2:
                $params['conditions']['type'] = \ITECH\Data\Lib\Constant::USER_TYPE_AGENT;
                $userMembership = \ITECH\Data\Lib\Constant::getUserMembershipAgent();
            break;
        }

        switch ($membership) {
            case 'admin':
                $params['conditions']['membership'] = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN;
            break;

            case 'agent':
                $params['conditions']['membership'] = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_USER_AGENT;
            break;

            case 'member':
                $params['conditions']['membership'] = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_USER_USER;
            break;
        }

        $userRepo = new \ITECH\Data\Repo\UserRepo();
        $users = $userRepo->getPaginationList($params);
        $userGender = \ITECH\Data\Lib\Constant::getUserGender();
        $userStatus = \ITECH\Data\Lib\Constant::getUserStatus();
        $result = array();
        if (isset($users->items) && count($users->items)) {
            foreach ($users->items as $user) {
                $result[] = array(
                    'id' => (int)$user['id'],
                    'username' => $user['username'],
                    'name' => $user['name'],
                    'slug' => $user['slug'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'avatar' => $user['avatar_image'],
                    'cover' => $user['cover_image'],
                    'gender' => isset($userGender[$user['gender']]) ? $userGender[$user['gender']] : null,
                    'birthday' => strtotime($user['birthday']) ? $user['birthday'] : null,
                    'address' => $user['address'],
                    'province_id' => ($user['province_id']) ? (int)$user['province_id'] : null,
                    'district_id' => ($user['district_id']) ? (int)$user['district_id'] : null,
                    'membership' => $user['membership'],
                    'status' => isset($userStatus[$user['status']]) ? $userStatus[$user['status']] : null,
                    'is_verified' => (int)$user['is_verified'],
                    //'referral_by' => $user['referral_by'],
                    'created_at' => $user['created_at'],
                    'updated_at' => $user['updated_at'],
                    'logined_at' => $user['logined_at']
                );
            }
            $response['result']['items'] = $result;
            $response['result']['total_items'] = $users->total_items;
            $response['result']['total_pages'] = $users->total_pages;
            $response['result']['first'] = $users->first;
            $response['result']['before'] = $users->before;
            $response['result']['current'] = $users->current;
            $response['result']['last'] = $users->last;
            $response['result']['next'] = $users->next;
            $response['result']['limit'] = $users->limit;
        }
        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function addAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
        }

        if ($post == '') {
            $post = $this->request->getPost();
        }

        $validator = new \Phalcon\Validation();
        $validator->add('username', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 5,
            'messageMinimum' => 'Tên nhất phải 5 ký tự.'
        )));
        $validator->setFilters('username', array('striptags', 'trim'));
        $validator->add('name', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 5,
            'messageMinimum' => 'Tên nhất phải 5 ký tự.'
        )));
        $validator->setFilters('name', array('striptags', 'trim'));
        $validator->setFilters('password', array('striptags', 'trim'));
        $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
            'message' => 'Định dạng e-mail không đúng'
        )));
        $validator->setFilters('email', array('striptags', 'trim'));
        $validator->setFilters('membership', array('striptags', 'trim'));
        $validator->setFilters('status', array('striptags', 'trim'));
        $validator->setFilters('type', array('striptags', 'trim'));
        $messages = $validator->validate($post);
        if(count($messages)) {
            $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
        }
        $has = 0;
        if ($this->request->getPost('email') != '') {
            $has = \ITECH\Data\Model\UserModel::count(array(
                'conditions' => 'email = :email:',
                'bind' => array(
                    'email' => $validator->getValue('email')
                )
            ));
        }
        if ($has > 1) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Đã tồn tại email này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $user = new \ITECH\Data\Model\UserModel();

        $user->password = \ITECH\Data\Lib\Util::hashPassword($validator->getValue('password'));
        $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('name'));
        $user->username = $validator->getValue('username');
        $user->display = $validator->getValue('name');
        $user->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('name'));
        $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
        $user->type = $validator->getValue('type');
        $user->membership = $validator->getValue('membership');
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
            $this->flashSession->success('Thêm thành công.');
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $user,
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function uploadImageAction()
    {
        $user_id = $this->request->getQuery('user_id', array('int'), '');
        $folder = $this->request->getQuery('folder', array('striptags', 'trim'), 'default');
        $w = $this->request->getQuery('w', array('striptags', 'trim'), '600');
        $h = $this->request->getQuery('h', array('striptags', 'trim'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $user_id
            )
        ));

        if (!$user) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tài khoản này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
            $validator = new \Phalcon\Validation();

            $validator->add('user_agent', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALuser_id_AGENT'
            )));

            $validator->add('ip', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_IP'
            )));

            $validator->setFilters('referral_url', array('striptags', 'trim'));
            $validator->setFilters('user_agent', array('striptags', 'trim'));
            $validator->setFilters('ip', array('striptags', 'trim'));

            $messages = $validator->validate($post);
            if (count($messages)) {
                $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result' => $result
                );
                goto RETURN_RESPONSE;
            }

            if (!isset($post->content) || !isset($post->extension)) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Hình ảnh không hợp lệ.'
                );
                goto RETURN_RESPONSE;
            }

            if ($post->content == '' || $post->extension == '') {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Hình ảnh không hợp lệ.'
                );
                goto RETURN_RESPONSE;
            }

            $content = base64_decode(trim($post->content));
            $extension = trim(strip_tags(strtolower($post->extension)));

            $file_name = uniqid() . '_' . time() . '.' . $extension;
            $file = ROOT . '/web/api/asset/upload/' . $file_name;

            $h = fopen($file, 'w');
            if (!$h) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không thể tạo file.'
                );
                goto RETURN_RESPONSE;
            } else {
                if (!fwrite($h, $content)) {
                        $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Không thể ghi nội dung file.'
                    );
                    goto RETURN_RESPONSE;
                }
            }
            fclose($h);

            $resizeImage = new \ITECH\Data\Lib\ResizeImage();
            $resizeImage->load($file, $extension);
            $thumbWidth = $resizeImage->getWidth();
            //$thumbHeight = $resizeImage->getHeight();

            if ($thumbWidth > $w) {
                $resizeImage->resizeToWidth($w);
            }

            $resizeImage->save($file);
            parent::uploadImageToCdn(ROOT . '/web/api/asset/upload/', $folder, $file_name);
            parent::deleteImageFromLocal(ROOT . '/web/api/asset/upload/', $file_name);

            $userLog = new \ITECH\Data\Model\UserLogModel();
            $userLog->user_id = $user->id;
            $userLog->action = \ITECH\Data\Lib\Constant::USER_LOG_ADMINISTRATOR_UPLOAD_IMAGE;
            $userLog->user_agent = $validator->getValue('user_agent');
            $userLog->ip = $validator->getValue('ip');
            $userLog->created_at = date('Y-m-d H:i:s');

            try {
                if (!$userLog->create()) {
                    $messages = $userLog->getMessages();
                    if (isset($messages[0])) {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $messages[0]->getMessage()
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo dữ liệu log.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                $file_name = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $file_name;
                $image_url = $this->config->asset->frontend_url . 'upload/' . $folder . '/' . $file_name;
                $default_thumbnail_url = $this->config->asset->frontend_url . 'upload/' . $folder . '/thumbnail/' . $file_name;
                $image = $file_name;

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'image_url' => $image_url,
                        'default_thumbnail_url' => $default_thumbnail_url,
                        'image' => $image
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[UserController][uploadImageAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
                goto RETURN_RESPONSE;
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function deleteImageAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        $user_id = $this->request->getQuery('user_id', array('int'), '');
        $folder = $this->request->getQuery('folder', array('striptags', 'trim'), '');
        $image = $this->request->getQuery('image', array('striptags', 'trim'), '');
        $userAgent = $this->request->getQuery('user_agent', array('striptags', 'trim'), '');
        $ip = $this->request->getQuery('ip', array('striptags', 'trim'), '');

        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $user_id
            )
        ));

        if (!$user) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tài khoản này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($image != '') {
            $userLog = new \ITECH\Data\Model\UserLogModel();
            $userLog->user_id = $user->id;
            $userLog->action = \ITECH\Data\Lib\Constant::USER_LOG_ADMINISTRATOR_DELETE_IMAGE;
            if ($userAgent != '') {
                $userLog->user_agent = $userAgent;
            } else {
                $userLog->user_agent = $this->request->getUserAgent();
            }

            if ($ip != '') {
                $userLog->ip = $ip;
            } else {
                $userLog->ip = $this->request->getClientAddress();
            }

            $userLog->created_at = date('Y-m-d H:i:s');
            $userLog->create();
            parent::deleteImageFromCdn($folder, $image);
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function registerAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
        }

        if ($post == '') {
            $post = $this->request->getPost();
        }
        $language = $post->language;
        $validator = new \Phalcon\Validation();

        $validator->add('firstname', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 2,
            'messageMinimum' => ($language == 'vi') ? 'Họ ít nhất là 2 ký tự' : 'First name at least 2 characters'
        )));
        $validator->setFilters('firstname', array('striptags', 'trim'));

        $validator->add('lastname', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 2,
            'messageMinimum' => ($language == 'vi') ? 'Tên ít nhất là 2 ký tự.' : 'Last name at least 2 characters'
        )));
        $validator->setFilters('lastname', array('striptags', 'trim'));

        $validator->add('password', new \Phalcon\Validation\Validator\PresenceOf(array(
            'message' => ($language == 'vi') ? 'Bạn chưa nhập mật khẩu.' : 'Enter your password'
        )));
        $validator->setFilters('password', array('striptags', 'trim'));

        $validator->add('email', new \Phalcon\Validation\Validator\PresenceOf(array(
            'message' => ($language == 'vi') ? 'Bạn chưa nhập email.' : 'Enter your email'
        )));

        $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
            'message' => ($language == 'vi') ? 'Email không đúng định dạng.' : 'Invalid email'
        )));
        $validator->setFilters('email', array('striptags', 'trim', 'lower'));

        $validator->setFilters('birthday', array('striptags', 'trim'));
        $validator->setFilters('email', array('striptags', 'trim'));
        $validator->setFilters('district', array('striptags', 'trim'));
        $validator->setFilters('province', array('striptags', 'trim'));
        $validator->setFilters('job_type', array('striptags', 'trim'));

        $validator->add('address', new \Phalcon\Validation\Validator\PresenceOf(array(
            'message' => ($language == 'vi') ? 'Bạn chưa nhập địa chỉ.' : 'Enter your address'
        )));
        $validator->setFilters('address', array('striptags', 'trim'));

        $validator->setFilters('carrer', array('striptags', 'trim'));
        $validator->setFilters('type_user', array('striptags', 'trim'));

        $messages = $validator->validate($post);
        if(count($messages)) {
            $result = array();

            foreach ($messages as $message) {
                $result[$message->getField()] = $message->getMessage();
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => ($language == 'vi') ? 'Thông tin chưa hợp lệ.' : 'Info invalid',
                'result' => $result
            );

            goto RETURN_RESPONSE;
        }

        $has = 0;
        if ($this->request->getPost('email') != '') {
            $has = \ITECH\Data\Model\UserModel::count(array(
                'conditions' => 'email = :email:',
                'bind' => array(
                    'email' => $validator->getValue('email')
                )
            ));
        }
        if ($has > 1) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => ($language == 'vi') ? 'Đã tồn tại email này.' : 'This email already exists.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $user = new \ITECH\Data\Model\UserModel();

        $user->password = \ITECH\Data\Lib\Util::hashPassword($validator->getValue('password'));
        $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('lastname'));
        $user->firstname = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('firstname'));
        $user->username = $validator->getValue('email');
        $user->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('lastname'));
        $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
        $user->type = $validator->getValue('type_user');
        $user->job_type = ($validator->getValue('type_user') == \ITECH\Data\Lib\Constant::USER_TYPE_AGENT) ? $validator->getValue('job_type') : '';
        $user->email = $validator->getValue('email');
        $user->address = $validator->getValue('address');
        $user->province_id = $validator->getValue('province');
        $user->district_id = $validator->getValue('district');
        $user->birthday = $validator->getValue('birthday');
        $user->membership = $validator->getValue('membership');
        $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $user->logined_at = date('Y-m-d H:i:s');

        if (!$user->create()) {
            $messages = $user->getMessages();
            if (isset($messages[0])) {
                $user->password = $this->request->getPost('password');
                $this->flashSession->error($messages[0]->getMessage());
                 $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'loi.',
                    'result' => $messages[0]->getMessage()
                );
            }
        } else {
            $this->flashSession->success('Thêm thành công.');
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $user,
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function profileAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $type = $this->request->getQuery('type', array('int'), \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), false);
        $cacheName = md5(serialize(array(
            'UserModel',
            'findFirst',
            $id
        )));

        $user = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$user) {
            if ($id != '') {
                $user = \ITECH\Data\Model\UserModel::findFirst(array(
                    'conditions' => 'id = :id: AND type = :type:',
                    'bind' => array(
                        'id' => $id,
                        'type' => $type
                    )
                ));
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $user);
            }
        }

        if (!$user) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tài khoản này.'
            );
            goto RETURN_RESPONSE;
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $user
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function forgotPasswordAction()
    {
         $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $email = $this->request->getQuery('email', array('striptags', 'trim'), '');
        $password = $this->request->getQuery('password', array('striptags', 'trim'), '');

        $has = 0;
        $has = \ITECH\Data\Model\UserModel::count(array(
            'conditions' => 'email = :email:',
            'bind' => array('email' => $email)
        ));

        if ($has == 0) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại email này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $user = new \ITECH\Data\Model\UserModel();
        $user = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'email = :email:',
            'bind' => array('email' => $email)
        ));

        $user->password = \ITECH\Data\Lib\Util::hashPassword($password);

        if (!$user->update()) {
            $messages = $user->getMessages();
            $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể thay đổi mật khẩu.';

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => $m
            );
            goto RETURN_RESPONSE;
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Thay đổi mật khẩu thành công.'
            );
           goto RETURN_RESPONSE;
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function settingAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );
        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
        }

        if ($post == '') {
            $post = $this->request->getPost();
        }
        if ($post->id != '') {
            $user = \ITECH\Data\Model\UserModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array(
                    'id' => $post->id,
                )
            ));
        }
        if (!$user) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tài khoản này.'
            );
            goto RETURN_RESPONSE;
        }
        $validator = new \Phalcon\Validation();
        $validator->add('firstname', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 2,
            'messageMinimum' => 'Tên nhất phải 2 ký tự.'
        )));
        $validator->setFilters('firstname', array('striptags', 'trim'));
        $validator->add('lastname', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 2,
            'messageMinimum' => 'Tên nhất phải 2 ký tự.'
        )));
        $validator->setFilters('lastname', array('striptags', 'trim'));
        $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
            'message' => 'Định dạng e-mail không đúng'
        )));
        $validator->setFilters('birthday', array('striptags', 'trim'));
        $validator->setFilters('email', array('striptags', 'trim'));
        $validator->setFilters('id_district', array('striptags', 'trim'));
        $validator->setFilters('id_province', array('striptags', 'trim'));
        $validator->setFilters('address', array('striptags', 'trim'));
        $validator->setFilters('phone', array('striptags', 'trim'));
        $validator->setFilters('job_title', array('striptags', 'trim'));
        $validator->setFilters('experience', array('striptags', 'trim'));
        $validator->setFilters('description', array('striptags', 'trim'));
        $validator->setFilters('type', array('striptags', 'trim'));
        $validator->setFilters('save_search', array('striptags', 'trim'));
        $validator->setFilters('save_home', array('striptags', 'trim'));
        $validator->setFilters('purchased_properties', array('striptags', 'trim'));
        $validator->setFilters('new_letter', array('striptags', 'trim'));
        $messages = $validator->validate($post);
        if(count($messages)) {
            $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
        }
        if ($post->password != '') {
            $user->password = \ITECH\Data\Lib\Util::hashPassword($post->password);
        }
        $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('lastname'));
        $user->firstname = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('firstname'));
        $user->username = $validator->getValue('email');
        $user->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('lastname'));
        $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
        $user->email = $validator->getValue('email');
        $user->address = $validator->getValue('address');
        $user->phone = $validator->getValue('phone');
        $user->type = $validator->getValue('type');
        $user->province_id = $validator->getValue('id_province');
        $user->district_id = $validator->getValue('id_district');
        $user->birthday = $validator->getValue('birthday');
        $user->job_title = $validator->getValue('job_title');
        $user->experience = $validator->getValue('experience');
        $user->description = $validator->getValue('description');
        $user->save_search = $validator->getValue('save_search');
        $user->save_home = $validator->getValue('save_home');
        $user->purchased_properties = $validator->getValue('purchased_properties');
        $user->new_letter = $validator->getValue('new_letter');
        $user->updated_at = date('Y-m-d H:i:s');
        $user->status = \ITECH\Data\Lib\Constant::USER_STATUS_ACTIVE;
        $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
        if ($this->request->getPost('avatar')) {
            $user->avatar = $this->request->getPost('avatar');
        }
        if (!$user->update()) {
            $messages = $user->getMessages();
            if (isset($messages[0])) {
                $this->flashSession->error($messages[0]->getMessage());
                 $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'loi.',
                    'result' => $messages[0]->getMessage()
                );
            }
        } else {
            $this->flashSession->success('Cập nhật thành công.');
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $user,
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function uploadAvatarAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );
        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
        }

        if ($post == '') {
            $post = $this->request->getPost();
        }
        if ($post->id != '') {
            $user = \ITECH\Data\Model\UserModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array(
                    'id' => $post->id,
                )
            ));
        }
        if (!$user) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tài khoản này.'
            );
            goto RETURN_RESPONSE;
        }
        $validator = new \Phalcon\Validation();
        $validator->setFilters('avatar', array('striptags', 'trim'));
        $messages = $validator->validate($post);
        if(count($messages)) {
            $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
        }
        $user->avatar = $validator->getValue('avatar');
        if (!$user->update()) {
            $messages = $user->getMessages();
            if (isset($messages[0])) {
                $this->flashSession->error($messages[0]->getMessage());
                 $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'loi.',
                    'result' => $messages[0]->getMessage()
                );
            }
        } else {
            $this->flashSession->success('Cập nhật thành công.');
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $user,
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function saveBookmarkAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id_apartment = $this->request->getQuery('id_apartment', array('striptags', 'trim'));
        $user_id = $this->request->getQuery('id_user', array('striptags', 'trim'));
        $saveHome = new \ITECH\Data\Model\UserSaveModel();
        $saveHome->user_id = $user_id;
        $saveHome->value = $id_apartment;
        $saveHome->key = \ITECH\Data\Lib\Constant::USER_SAVE_HOME;
        $saveHome->created_at = date('Y-m-d H:i:s');
        if (!$saveHome->create()) {
            $messages = $saveHome->getMessages();
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Error.',
                'result' => $messages
            );
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $saveHome,
            );
        }
        return parent::outputJSON($response);
    }

    public function deleteBookmarkAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id_apartment = $this->request->getQuery('id_apartment', array('striptags', 'trim'));
        $user_id = $this->request->getQuery('user_id', array('striptags', 'trim'));
        $saveHome = new \ITECH\Data\Model\UserSaveModel();
        $saveHome->user_id = $user_id;
        $saveHome->value = $id_apartment;
        $saveHome->key = \ITECH\Data\Lib\Constant::USER_SAVE_HOME;
        $r = \ITECH\Data\Model\UserSaveModel::findFirst(array(
            'conditions' => 'user_id = :user_id: AND value = :value: AND key = :key:',
            'bind' => array(
                'user_id' => $user_id,
                'value' => $id_apartment,
                'key' => \ITECH\Data\Lib\Constant::USER_SAVE_HOME
            )
        ));

        if ($r != false) {
            if ($r->delete() == false) {
                $messages = $saveHome->getMessages();
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error.',
                    'result' => $messages
                );
            } else {
                $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => ''
            );
            }
        }
        return parent::outputJSON($response);
    }

    public function saveHomeAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $user_id = $this->request->getQuery('id', array('striptags', 'trim'));
        $key = $this->request->getQuery('key', array('striptags', 'trim'));
        $r = \ITECH\Data\Model\UserSaveModel::find(array(
            'conditions' => 'user_id = :user_id: AND key = :key:',
            'bind' => array(
                'user_id' => $user_id,
                'key' => $key
            )
        ));
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $params = array(
            'conditions' => array(
            ),
            'order' => 'a1.id DESC',
            'page' => $page,
            'limit' => $limit
        );
        if($r && count($r->toArray()) > 0) {
            if (count($r->toArray()) > 1) {
                $arrId = array();
                foreach($r->toArray() as $key=>$value) {
                    $arrId[] = $value['value'];
                }
                $params['conditions']['ids'] = $arrId;
            } else {
                $params['conditions']['id'] = $r->toArray()[0]['value'];
            }
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
            $apartments = $apartmentRepo->getPaginationList($params);
            $typeTrend = \ITECH\Data\Lib\Constant::getDirection();
            foreach ($apartments->items as $item) {
                if (is_object($item)) {
                    $item = (array)$item;
                }

                $item['save_home'] = 'true';

                $default_image_url = parent::$noImageUrl;
                $default_thumbnail_url = parent::$noImageUrl;

                if ($item['default_image'] != '') {
                    $default_image_url = $this->config->cdn->dir_upload . $item['default_image'];
                    $default_thumbnail_url = $this->config->cdn->dir_upload . 'thumbnail/' . $item['default_image'];
                }

                $response['result'][] = array(
                    'id' => (int)$item['id'],
                    'name' => $item['name'],
                    'name_eng' => $item['name_eng'],
                    'slug' => \ITECH\Data\Lib\Util::slug($item['name']),
                    'user_id' => $item['user_id'],
                    'block_id' => $item['block_id'],
                    'block_name' => $item['block_name'],
                    'block_slug' => \ITECH\Data\Lib\Util::slug($item['block_name']),
                    'project_id' => $item['project_id'],
                    'project_name' => $item['project_name'],
                    'project_slug' => \ITECH\Data\Lib\Util::slug($item['project_name']),
                    'condition' => $item['conditions'],
                    'type' => $item['type'],
                    'price' => $item['price'],
                    'price_eng' => $item['price_eng'],
                    'price_sale_off' => $item['price_sale_off'],
                    'position' => $item['position'],
                    'area' => $item['area'],
                    'space' => $item['space'],
                    'view_count' => $item['view_count'],
                    'default_image' => $item['default_image'],
                    'default_image_url' => $default_image_url,
                    'default_thumbnail_url' => $default_thumbnail_url,
                    'gallery' => $item['gallery'],
                    'floor_count' => $item['floor_count'],
                    'status' => $item['status'],
                    'created_by' => $item['created_by'],
                    'updated_by' => $item['updated_by'],
                    'approved_by' => $item['approved_by'],
                    'address' => $item['project_address'],
                    'address_eng' => $item['project_address_eng'],
                    'trend' => $item['trend'],
                    'save_home' => $item['save_home'],
                    'trend_value' => isset($typeTrend[$item['trend']]) ? $typeTrend[$item['trend']] : '',
                );
            }

            $response['total_items'] = $apartments->total_items;
            $response['total_pages'] = isset($apartments->total_pages) ? $apartments->total_pages : ceil($apartments->total_items / $limit);

            goto RETURN_RESPONSE;
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy sản phẩm nào',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        }
        //var_dump($params); die;

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function saveSearchAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $url_search = $this->request->getQuery('url_search', array('striptags', 'trim'));
        $user_id = $this->request->getQuery('id_user', array('striptags', 'trim'));
        $saveHome = new \ITECH\Data\Model\UserSaveModel();
        $saveHome->user_id = $user_id;
        $saveHome->value = $url_search;
        $saveHome->key = \ITECH\Data\Lib\Constant::USER_SAVE_SEARCH;
        $saveHome->created_at = date('Y-m-d H:i:s');
        $saveHome->notify = \ITECH\Data\Lib\Constant::SAVE_SEARCH_NOTIFY_DAILY;
        $has_search = \ITECH\Data\Model\UserSaveModel::findFirst(array(
            'conditions' => 'key = :key: AND value = :value: AND user_id = :user_id:',
            'bind' => array(
                'key' => \ITECH\Data\Lib\Constant::USER_SAVE_SEARCH,
                'value' => $url_search,
                'user_id' => $user_id
            )
        ));
        if ($has_search) {
            $saveHome->id = $has_search->id;
            if (!$saveHome->update()) {
                $messages = $saveHome->getMessages();
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error.',
                    'result' => $messages
                );
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => $saveHome,
                );
            }
        } else {
            if (!$saveHome->create()) {
                $messages = $saveHome->getMessages();
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error.',
                    'result' => $messages
                );
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => $saveHome,
                );
            }
        }
        return parent::outputJSON($response);
    }

    public function listSaveSearchAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $user_id = $this->request->getQuery('user_id', array('striptags', 'trim'));
        $key = $this->request->getQuery('key', array('striptags', 'trim'));
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $params = array(
            'conditions' => array(
                'user_id' => $user_id,
                'key' => $key
            ),
            'order' => 'b1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        $saveSearchRepo = new \ITECH\Data\Repo\SaveSearchRepo();
        $saveSearch= $saveSearchRepo->getPaginationList($params);
        if(count($saveSearch)) {
            foreach ($saveSearch->items as $item) {
                $response['result'][] = array(
                    'id' => (int)$item['id'],
                    'user_id' => $item['user_id'],
                    'value' => $item['value'],
                    'key' => $item['key'],
                    'created_at' => $item['created_at'],
                );
            }

            $response['total_items'] = $saveSearch->total_items;
            $response['total_pages'] = isset($saveSearch->total_pages) ? $saveSearch->total_pages : ceil($saveSearch->total_items / $limit);

            goto RETURN_RESPONSE;
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy sản phẩm nào',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function addContactAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Invalid POST method.',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $post = (array)$this->request->getJsonRawBody();
            if (!$post) {
                $post = $this->request->getPost();
            }

            if (isset($post['user_id'])) {
                $userId = $post['user_id'];
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'user_id không để trống.',
                    'result' => array()
                );
                goto RETURN_RESPONSE;
            }
            $user = new \ITECH\Data\Model\UserModel();
            $userDetail = $user::findFirst(array(
                'conditions' => 'id = :user_id:',
                'bind' => array(
                    'user_id' => $userId,
                )
            ));
            if (!$userDetail) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Tài khoản '. $userId .' này không tồn tại.',
                    'result' => array()
                );
                goto RETURN_RESPONSE;
            }

            $id = 0;
            $userContact = new \ITECH\Data\Model\UserContactModel();
            if (isset($post['id']) && !empty($post['id'])) {
                $id = $post['id'];

                $userContactDetail = $userContact::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $id,
                    )
                ));
                if (!$userContactDetail) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Danh bạ '. $id .' này không tồn tại.',
                        'result' => array()
                    );
                    goto RETURN_RESPONSE;
                }

            }

            $validator = new \Phalcon\Validation();

            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Tên khách hàng không được để trống'
            )));
            $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
                'message' => 'Email không đúng định dạng'
            )));

            $validator->setFilters('name', array('striptags', 'trim'));
            $validator->setFilters('phone', array('striptags', 'trim'));
            $validator->setFilters('email', array('striptags', 'trim'));
            $validator->setFilters('note', array('striptags', 'trim'));
            $validator->setFilters('avatar', array('striptags', 'trim'));
            $validator->setFilters('address', array('striptags', 'trim', 'int'));
            $validator->setFilters('gender', array('striptags', 'trim', 'int'));

            $messages = $validator->validate($post);
            if (count($messages)) {
                $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Vui lòng điền đầy đủ thông tin còn thiếu.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
            } else {
                $dataCustomer = array(
                  'name' => $validator->getValue('name'),
                  'phone' => $validator->getValue('phone'),
                  'email' => $validator->getValue('email'),
                  'note' => $validator->getValue('note'),
                  'address' => $validator->getValue('address'),
                  'avatar' => $validator->getValue('avatar'),
                  'gender' => $validator->getValue('gender')
                );

                if ($id) {
                    $userContact->id = $id;
                }
                $userContact->user_id = $userId;
                $userContact->customer = json_encode($dataCustomer);

                try {
                    if ($id) {
                        $userContact->update();
                    } else {
                        $userContact->create();
                    }

                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Success.',
                        'result' => array(
                            'id' => $userContact->id,
                            'user_id' => $userContact->user_id,
                            'customer' => json_decode($userContact->customer,  true),
                        )
                    );
                } catch (\Phalcon\Exception $e) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $e->getMessage(),
                        'result' => false
                    );
                }
            }

        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function listContactByUserAction()
    {
        $page = $this->request->getQuery('page', array('striptags', 'int', 'trim'), 1);
        $limit = $this->request->getQuery('limit', array('striptags', 'int', 'trim'), $this->config->application->pagination_limit);
        $userId = $this->request->getQuery('user_id', array('striptags', 'int', 'trim'), 0);

        if (!$userId) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'user_id không để trống.',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        }
        $user = new \ITECH\Data\Model\UserModel();
        $userDetail = $user::findFirst(array(
            'conditions' => 'id = :user_id:',
            'bind' => array(
                'user_id' => $userId,
            )
        ));
        if (!$userDetail) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Tài khoản '. $userId .' này không tồn tại.',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        }

        $params = array(
            'page' => $page,
            'limit' => $limit,
            'user_id' => $userId
        );

        $listContactRepo = new \ITECH\Data\Repo\UserContactRepo();
        $listContact = $listContactRepo->getPaginationList($params);

        $result = array();
        foreach ($listContact->items as $item) {
            $item->customer = json_decode($item->customer, true);
            $result[] = $item;
        }
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => $result,
            'total_items' => $listContact->total_items,
            'total_pages' => isset($listContact->total_pages) ? $listContact->total_pages : ceil($listContact->total_items / $limit)
        );

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function deleteContactAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Method post is required.',
            'result' => array()
        );

        if ($this->request->isPost()) {

            $customerId = $this->request->getPost('customer_id', array('int'), 0);
            $userId = $this->request->getPost('user_id', array('int'), 0);

            $user = new \ITECH\Data\Model\UserModel();
            $userDetail = $user::findFirst(array(
                'conditions' => 'id = :user_id:',
                'bind' => array(
                    'user_id' => $userId,
                )
            ));
            if (!$userDetail) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Tài khoản '. $userId .' này không tồn tại.',
                    'result' => array()
                );
                goto RETURN_RESPONSE;
            }

            $customer = new \ITECH\Data\Model\UserContactModel();
            $customerDetail = $customer::findFirst(array(
                'conditions' => 'user_id = :user_id: AND id = :id:',
                'bind' => array(
                    'user_id' => $userId,
                    'id' => $customerId
                )
            ));
            if (!$customer) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Khách hàng '. $customerId .' này không tồn tại.',
                    'result' => array()
                );
                goto RETURN_RESPONSE;
            }


            $customerDetail->delete();
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }
}
