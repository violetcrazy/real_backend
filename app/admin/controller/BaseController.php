<?php
namespace ITECH\Admin\Controller;

class BaseController extends \Phalcon\Mvc\Controller
{
    public static $theme;
    public static $mainView;
    public static $options;

    public function initialize()
    {
        $this->authorizedToken();

        self::$theme    = 'default';
        self::$mainView = 'default/';

        $url = $this->config->application->api_url . 'option/list?cache=true';
        $r   = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);

        $options = array();

        if ($r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $options = $r['result'];
        }

        self::$options = $options;

        $this->view->setVars(array(
            'options' => self::$options
        ));
        $this->view->setMainView(self::$mainView);
    }

    public function authenticateUser()
    {
        $authenticate = true;
        $hasCookie    = false;

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $user            = [];

        if (!$this->session->has('USER')) {
            if (!$this->cookies->has('USER')) {
                $authenticate = false;
            } else {
                $cookie = $this->cookies->get('USER');
                $user   = @unserialize($cookie->getValue());

                if ($user && is_array($user) && count($user)) {
                    $hasCookie = true;
                } else {
                    $authenticate = false;
                }
            }
        } else {
            $user = $this->session->get('USER');
        }

        if ($authenticate) {
            $url = $this->config->application->api_url . 'user/detail';

            $get = array(
                'authorized_token' => $authorizedToken,
                'id'               => $user['id'],
                'type'             => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                'cache'            => 'false'
            );

            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

            if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                if ($hasCookie) {
                    $requestUri = $this->config->application->protocol . $this->request->getHttpHost() . $this->request->getServer('REQUEST_URI');

                    if ($requestUri != $this->url->get(array('for' => 'home')) && $requestUri != $this->url->get(array('for' => 'user_login'))) {
                        $referralUrl = $requestUri;
                    } else {
                        $referralUrl = $this->url->get(array('for' => 'home'));
                    }
                }

                self::setUserSession($r['result']);
            } else {
                $authenticate = false;
            }
        }

        if (!$authenticate) {
            $this->session->remove('USER');

            $cookie = $this->cookies->get('USER');
            $cookie->delete();

            $requestUri = $this->config->application->protocol . $this->request->getHttpHost() . $this->request->getServer('REQUEST_URI');

            if ($requestUri != $this->url->get(array('for' => 'home')) && $requestUri != $this->url->get(array('for' => 'user_login'))) {
                $referralUrl = $requestUri;
            } else {
                $referralUrl = $this->url->get(array('for' => 'home'));
            }

            $query = http_build_query(array('referral_url' => $referralUrl));
            $url   = $this->url->get(array('for' => 'user_login', 'query' => '?' . $query));

            header('Location: ' . $url);
            exit;
        }
    }

    public function setUserSession($user)
    {
        if ($user && is_array($user) && count($user)) {
            $session = array(
                'id'               => (int)$user['id'],
                'username'         => $user['username'],
                'name'             => $user['name'],
                'type'             => (int)$user['type'],
                'membership'       => (int)$user['membership'],
                'avatar'           => isset($user['avatar']) ? $user['avatar'] : '',
                'avatar_image'     => isset($user['avatar_image']) ? $user['avatar_image'] : '',
                'avatar_image_url' => isset($user['avatar_image_url']) ? $user['avatar_image_url'] : '',
                'logined_at'       => $user['logined_at']
            );

            $this->session->set('USER', $session);
            $this->cookies->set('USER', serialize($session), strtotime('+1 hour'));

            return true;
        } else {
            $this->session->remove('USER');

            $cookie = $this->cookies->get('USER');
            $cookie->delete();
        }

        return false;
    }

    public function setUserSessionOject($user)
    {
        if ($user && is_object($user) && count($user)) {
            $session = array(
                'id'               => (int)$user->id,
                'username'         => $user->username,
                'name'             => $user->name,
                'membership'       => (int)$user->membership,
                'type'             => $user->type,
                'avatar'           => isset($user->avatar) ? $user->avatar : '',
                'avatar_image'     => isset($user->avatar_image) ? $user->avatar_image : '',
                'avatar_image_url' => isset($user->avatar_image_url) ? $user->avatar_image_url : '',
                'logined_at'       => $user->logined_at
            );

            $this->session->set('USER', $session);
            $this->cookies->set('USER', serialize($session), strtotime('+1 hour'));

            return true;
        } else {
            $this->session->remove('USER');

            $cookie = $this->cookies->get('USER');
            $cookie->delete();
        }

        return false;
    }

    public function allowRole(array $roles)
    {
        $user = $this->session->get('USER');

        if (!$user || !is_array($user) || !count($user)) {
            self::authenticateUser();
        }

        if (!$roles || !is_array($roles) || !count($roles)) {
            self::authenticateUser();
        }

        if ($roles && is_array($roles) && count($roles)) {
            if (!in_array($user['membership'], $roles)) {
                $this->flashSession->error('Tài khoản của bạn không có quyền truy cập.');

                return $this->response->redirect(array('for' => 'access'));
            }
        }
    }

    public function authorizedToken()
    {
        $hasAuthorizedToken = false;

        if (!$this->session->has('AUTHORIZED_TOKEN')) {
            if (!$this->cookies->has('AUTHORIZED_TOKEN')) {
                $hasAuthorizedToken = false;
            } else {
                $cookie          = $this->cookies->get('AUTHORIZED_TOKEN');
                $authorizedToken = @unserialize($cookie->getValue());

                if ($authorizedToken != '') {
                    $this->setAuthorizedToken($authorizedToken);
                    $hasAuthorizedToken = true;
                } else {
                    $hasAuthorizedToken = false;
                }
            }
        } else {
            $authorizedToken    = $this->session->get('AUTHORIZED_TOKEN');
            $hasAuthorizedToken = true;
        }

        if (!$hasAuthorizedToken) {
            $url = $this->config->application->api_url . 'authenticate';
            $post = array(
                'application' => \ITECH\Data\Lib\Constant::SESSION_TOKEN_APPLICATION_WEB,
                'secret'      => $this->config->application->secret
            );

            $authorizedToken = false;
            $response = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);

            if (
                isset($response['status'])
                && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS
                && isset($response['result'])
            ) {
                $authorizedToken = $response['result'];
            }

            if ($authorizedToken) {
                $this->setAuthorizedToken($authorizedToken);
            }
        }
    }

    public function setAuthorizedToken($token)
    {
        if ($token && $token != '') {
            $this->session->set('AUTHORIZED_TOKEN', $token);
            $this->cookies->set('AUTHORIZED_TOKEN', serialize($token), strtotime('+1 hour'));
        } else {
            $this->session->remove('AUTHORIZED_TOKEN');

            $cookie = $this->cookies->get('AUTHORIZED_TOKEN');
            $cookie->delete();
        }
    }

    public function outputJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();

        exit;
    }

    public function uploadImageToLocal($dir, $fileName, $scaleX, $resource)
    {
        $response = array();

        if (is_dir($dir)) {
            if ($fileName == '') {
                $fileName = uniqid() . '_' . time();
            }

            if ($resource && count($resource)) {
                $u = new \ITECH\Data\Lib\Upload($resource);
                $u->allowed = array('image/*');
                $u->forbidden = array('application/*');

                try {
                    if (!$u->uploaded) {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể upload.'
                        );
                    } else {
                        if ($u->file_is_image) {
                            if ($scaleX > 0) {
                                $u->image_resize = true;
                                $u->image_x = $scaleX;
                                $u->image_ratio_y = true;
                            }

                            $u->jpeg_quality = 85;
                            $u->file_new_name_body = $fileName;
                            $u->process($dir);

                            if ($u->processed) {
                                $fileName .= '.' . $u->file_src_name_ext;

                                $response = array(
                                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                    'message' => 'Upload thành công.',
                                    'result' => $fileName
                                );
                            } else {
                                $response = array(
                                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                    'message' => 'Lỗi, không thể xử lý hình ảnh.'
                                );
                            }
                        } else {
                            $response = array(
                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                'message' => 'Lỗi, không đúng định dạng hình ảnh.'
                            );
                        }

                        $u->clean();
                    }
                } catch (\Phalcon\Exception $e) {
                    $this->logger->log('[BaseController][uploadImageToLocal] ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                    throw new \Phalcon\Exception($e->getMessage());
                }
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function deleteImageFromLocal($dir, $fileName)
    {
        $response = array();

        if (is_dir($dir)) {
            $file = $dir . $fileName;

            if (file_exists($file)) {
                @chmod($file, 0777);

                if (@unlink($file)) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Xóa hình ảnh thành công.'
                    );
                } else {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không thể xóa hình ảnh.'
                    );
                }
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại hình ảnh.'
                );
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function uploadImageToCdn($local_dir, $remote_folder, $fileName)
    {
        $response = array();

        if (is_dir($local_dir)) {
            $file = $local_dir . $fileName;

            if (!file_exists($file)) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại hình ảnh.'
                );

                return $response;
            }

            $content = file_get_contents($file);

            $url = $this->config->cdn->upload_image_url;
            $post = array(
                'content' => $content,
                'folder' => $remote_folder,
                'filename' => $fileName
            );

            $r = \ITECH\Data\Lib\Util::curlPost($url, $post);
            $r = json_decode($r, true);

            if (!empty($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Upload thành công.'
                );
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Lỗi, không thể upload.'
                );
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function deleteImageFromCdn($remote_folder, $fileName)
    {
        $response = array();

        $url = $this->config->cdn->delete_image_url;
        $get = array(
            'folder' => $remote_folder,
            'filename' => $fileName
        );

        $r = \ITECH\Data\Lib\Util::curlGet($url, $get);
        $r = json_decode($r, true);

        if (!empty($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Xóa thành công.'
            );
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không thể xóa.'
            );
        }

        return $response;
    }

    public function getDataAttribute($module, $type)
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $aParams = array();
        $aParams['module'] = $module;
        $aParams['type'] = $type;
        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/attribute-list?cache=false';
        $url = $url . '&' . http_build_query($aParams);

        $output = array();
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            foreach($r['result'] as $item) {
                $output[] = $item['name'];
            }
        }

        return $output;
    }

    public function getAttrApartment($type, $apartmentId, $language = 1, $cache = true)
    {
        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        $params = array(
            'conditions' => array(
                'type' => $type,
                'apartment_id' => $apartmentId,
                'language' => $language
            )
        );

        $attributeResult = $attributeRepo->getListByApartment($params);
        $attributeOutput = array();

        if (count($attributeResult)) {
            foreach($attributeResult as $item) {
                $attributeOutput[] = $item->name;
            }
        }

        if ($cache) {}

        return $attributeOutput;
    }

    public function saveMapImage($args)
    {
        if (count($args) > 0) {
            $userSession = $this->session->get('USER');

            if (isset($args['item_id']) && isset($args['module'])) {
                if ($args['module'] == \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT) {
                    $itemParentMapImage = \ITECH\Data\Model\ProjectModel::findFirst($args['item_id']);
                } else if ($args['module'] == \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK) {
                    $itemParentMapImage = \ITECH\Data\Model\BlockModel::findFirst($args['item_id']);
                } else {
                    $itemParentMapImage = \ITECH\Data\Model\ApartmentModel::findFirst($args['item_id']);
                }

                if (!$itemParentMapImage) {
                    return 'Không tìm thấy module này';
                }

                $old = \ITECH\Data\Model\MapImageModel::find(array(
                    'conditions' => 'item_id = :item_id: AND module = :module:',
                    'bind' => array(
                        'item_id' => $args['item_id'],
                        'module'  => $args['module']
                    )
                ));

                $new = $args['images'];

                foreach ($old as $itemOld) {
                    $_id   = $itemOld->id;
                    $check = false;

                    foreach ($new as $itemNew) {
                        if (is_array($itemNew) && isset($itemNew['id']) && $_id == $itemNew['id']) {
                            $check = true;
                            continue;
                        }
                    }

                    if (!$check) {
                        $itemOld->delete();
                    }
                }

                $out         = array();
                $out['meta'] = $args['images'];

                foreach ($new as $itemNew) {
                    if (isset($itemNew['id'])) {
                        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
                            'conditions' => 'id = :id_map_image:',
                            'bind'       => array('id_map_image' => $itemNew['id'])
                        ));

                        if ($mapImage) {
                            $mapImage->type       = (int)strip_tags(trim($itemNew['type']));
                            $mapImage->position   = (int)strip_tags(trim($itemNew['position']));
                            $mapImage->updated_by = (int)$userSession['id'];
                            $mapImage->updated_at = date('Y-m-d H:i:s');

                            if (isset($itemNew['floor'])) {
                                $mapImage->floor = (int)strip_tags(trim($itemNew['floor']));
                            }

                            try {
                                if (!$mapImage->update()) {
                                    $out['update_error'][] = array(strip_tags(trim($itemNew['image'])), $mapImage->getMessages());
                                } else {
                                    $out['update_success'][] = array(strip_tags(trim($itemNew['image'])), 'Success' );
                                }
                            } catch (\Exception $e) {
                                $out['update_error'][] = array(strip_tags(trim($itemNew['image'])), $e->getMessage());
                            }
                        }
                    } else {
                        $mapImage = new \ITECH\Data\Model\MapImageModel();

                        if (isset($itemNew['image'])) {
                            $mapImage->image    = strip_tags(trim($itemNew['image']));
                            $mapImage->type     = (int)strip_tags(trim($itemNew['type']));
                            $mapImage->position = (int)strip_tags(trim($itemNew['position']));
                            $mapImage->module   = (int)$args['module'];
                            $mapImage->item_id  = (int)$args['item_id'];

                            $mapImage->created_by = (int)$userSession['id'];
                            $mapImage->created_at = date('Y-m-d H:i:s');
                            $mapImage->updated_by = (int)$userSession['id'];
                            $mapImage->updated_at = date('Y-m-d H:i:s');

                            try {
                                if (!$mapImage->save()) {
                                    $out['create_error'][] = array(strip_tags(trim($itemNew['image'])),$mapImage->getMessages());
                                } else {
                                    $out['create_success'][] = array(strip_tags(trim($itemNew['image'])),'Success');
                                }
                            } catch (\Exception $e) {
                                $out['create_error'][] = array( strip_tags(trim($itemNew['image'])),$e->getMessage());
                            }
                        }
                    }
                }

                $defaultImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
                    'conditions' => '
                        item_id    = :item_id: 
                        AND module = :module: 
                        AND type   = :type:
                    ',
                    'bind' => array(
                        'item_id' => $args['item_id'],
                        'type'    => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_THUMBNAIL,
                        'module'  => $args['module']
                    )
                ));

                if ($defaultImage) {
                    $itemParentMapImage->default_image = $defaultImage->image;
                } else {
                    $itemParentMapImage->default_image = '';
                }

                $itemParentMapImage->update();

                return $out;
            } else {
                return $args;
            }
        }

        return 'Parameter is required';
    }

    public function getMapImage($args)
    {
        $out = array();

        if (isset($args['module']) && isset($args['item_id'])) {
            $out = \ITECH\Data\Model\MapImageModel::find(array(
                'conditions' => 'item_id = :item_id: AND module = :module:',
                'order' => 'id DESC',
                'bind' => array(
                    'item_id' => (int)$args['item_id'],
                    'module'  => (int)$args['module']
                )
            ));
        }

        return $out;
    }

    public function saveAttr($args)
    {
        $res = array();

        if ($args['value'] != '') {
            $array      = array_filter(array_unique(explode(',', $args['value'])));
            $conditions = 'name = :attribute_name: AND type = :attribute_type:';
            $bind       = array('attribute_type' => $args['type']);

            if (isset($args['module'])) {
                if ($args['module'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK) {
                    $conditions = '
                        name        = :attribute_name: 
                        AND type    = :attribute_type: 
                        AND (module = :module_project: OR module = :module_block:)
                    ';

                    $bind['module_project'] = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT;
                    $bind['module_block']   = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK;
                } else {
                    $conditions = '
                        name       = :attribute_name: 
                        AND type   = :attribute_type: 
                        AND module = :module_project:
                    ';

                    $bind['module_project'] = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT;
                }
            }

            foreach ($array as $item) {
                $item = trim($item);

                if ($item != '') {
                    $bind['attribute_name'] = $item;

                    $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                        'conditions' => $conditions,
                        'bind'       => $bind
                    ));

                    $bind['attribute_name'] = '';

                    if ($attribute) {
                        $res[] = $attribute->id;
                    }
                }
            }
        }

        return $res;
    }
}
