<?php

class ProjectController
{
    public function listMapImageAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $projectId = $this->request->getQuery('project_id', array('int'), '');
        //$typeView = $this->request->getQuery('type_view', array('int'), 1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        // Get project ---------
        $project = array();
        $url = $this->config->application->api_url . 'project/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $projectId,
            'cache' => 'false'
        );

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $project = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại dự án này.');
        }
        // --------- Get project

        $aParams = array();
        $aParams['item_id'] = $projectId;
        $aParams['page'] = $page;
        $aParams['limit'] = $limit;
        $aParams['module'] = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT;
        $aParams['cache'] = 'false';
        $aParams['authorized_token'] = $authorizedToken;

        $query = array();
        $query['project_id'] = $projectId;
        $query['page'] = $page;

        $mapImages = array();
        $url = $this->config->application->api_url . 'map-image/list';
        $url = $url . '?' . http_build_query($aParams);

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $mapImages = $r;
        }

        $url = $this->url->get(array('for' => 'project_list_map_image'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($mapImages['total_pages']) ? $mapImages['total_pages'] : 0,
            'page' => $page,
            'pages_display' => 3
        );
        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'mapImages' => $mapImages,
            'project' => $project
        ));
        $this->view->pick(parent::$theme . '/project/list_map_image');
    }

    public function mapImageAddAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $project_id = $this->request->getQuery('project_id', array('int'), -1);

        // Get project ---------
        $project = array();
        $url = $this->config->application->api_url . 'project/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $project_id,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $project = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại dự án này.');
        }
        // --------- Get project

        $mapImage = new \ITECH\Data\Model\MapImageModel();
        $form = new \ITECH\Admin\Form\ProjectMapImageForm($mapImage);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $mapImage);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $module = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT;
                $url = $this->config->application->api_url . 'map-image/add?authorized_token=' . $authorizedToken . '&item_id=' . $project['id'] . '&module=' . $module;
                $post = array(
                    'type' => $this->request->getPost('type'),
                    'image' => $this->request->getPost('image_view'),
                    'floor' => (int)0,
                    'created_by' => $userSession['id'],
                    'updated_by' => $userSession['id'],
                    'user_id' => $userSession['id']
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Tạo hình ảnh hiển thị thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'project_id' => $project['id']
                    );
                    return $this->response->redirect(array('for' => 'project_map_image_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể tạo hình ảnh hiển thị.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'project' => $project,
            'form' => $form,
        ));
        $this->view->pick(parent::$theme . '/project/map_image_add');
    }

    public function mapImageEditAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $project_id = $this->request->getQuery('project_id', array('int'), -1);
        $id = $this->request->getQuery('id', array('int'), '');

        // Get project ---------
        $project = array();
        $url = $this->config->application->api_url . 'project/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $project_id,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $project = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại dự án này.');
        }
        // --------- Get project

        // --------- Get map image
        $url = $this->config->application->api_url . 'map-image/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $id,
            'item_id' => $project['id'],
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        // Get map image ---------

        $mapImage = new \ITECH\Data\Model\MapImageModel();
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $mapImage->id = $r['result']['id'];
            $mapImage->item_id = $r['result']['item_id'];
            $mapImage->type = $r['result']['type'];
            $mapImage->image = $r['result']['image'];
            $mapImage->module = $r['result']['module'];
            $mapImage->created_at = $r['result']['created_at'];
            $mapImage->updated_at = $r['result']['updated_at'];
        } else {
            if (isset($r['message'])) {
                throw new \Phalcon\Exception($r['message']);
            } else {
                throw new \Phalcon\Exception('Lỗi, không tồn tại hình ảnh này');
            }
        }

        $form = new \ITECH\Admin\Form\ProjectMapImageForm($mapImage);
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                //$module = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT;
                $url = $this->config->application->api_url . 'map-image/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $id,
                    'item_id' => $project['id'],
                    'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
                    'cache' => 'false'
                );
                $url = $url . '?' . http_build_query($get);

                $post = array(
                    'type' => $this->request->getPost('type'),
                    'floor' => (int)0,
                    'image' => $this->request->getPost('image_view'),
                    'updated_by' => $userSession['id'],
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getClientAddress(),
                    'user_id' => $userSession['id']
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Cập nhật hình ảnh này công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'project_id' => $r['result']['item_id']
                    );
                    return $this->response->redirect(array('for' => 'project_map_image_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể cập nhật block.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'mapImage' => $mapImage,
            'project' => $project,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/project/map_image_edit');
    }

    public function mapImageDeleteAction()
    {
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), '');
        $projectId = $this->request->getQuery('project_id', array('int'), '');

        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
            'conditions' => 'id = :map_image_id:',
            'bind' => array('map_image_id' => $mapImageId)
        ));

        if (!$mapImage) {
            throw new \Exception('Không tồn tại hình ảnh chi tiết này.');
        }

        $mapImage->delete();
        $this->flashSession->success('Xóa thành công.');

        return $this->response->redirect(array('for' => 'project_list_map_image', 'query' => '?' . http_build_query(array('project_id' => $projectId))));
    }

    public function mapAddAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $projectId = $this->request->getQuery('project_id', array('int'), -1);
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), -1);

        // Get project ---------
        $project = array();
        $url = $this->config->application->api_url . 'project/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $projectId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $project = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại dự án này.');
        }
        // --------- Get project

        // Get list block ---------
        $blocks = array();
        $url = $this->config->application->api_url . 'block/list-by-project';
        $get = array(
            'authorized_token' => $authorizedToken,
            'project_id' => $projectId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $blocks = $r['result'];
        }
        // --------- Get list block

        // --------- Get map image
        $url = $this->config->application->api_url . 'map-image/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $mapImageId,
            'item_id' => $project['id'],
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $mapImage = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại hình ảnh này.');
        }
        // Get map image ---------
        // --------- Get list map
        $listMapView = array();
        $url = $this->config->application->api_url . 'map/list-by-map-image';
        $get = array(
            'authorized_token' => $authorizedToken,
            'image_map_id' => $mapImageId,
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'cache' => 'false'
        );

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $listMapView = $r['result'];
        }

        $arrMap = array();
        if (count($listMapView)) {
            foreach($listMapView as $item) {
                $arrMap[] = $item['block_id'];
            }
        }
        if (count($blocks)) {
            foreach ($blocks as $key => $item) {
                if (in_array($item['id'], $arrMap)) {
                    unset($blocks[$key]);
                }
            }
        }
        // Get list map ---------
        // --------- Get list map
        $listMapView = array();
        $url = $this->config->application->api_url . 'map/list-by-map-image';
        $get = array(
            'authorized_token' => $authorizedToken,
            'image_map_id' => $mapImageId,
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'cache' => 'false'
        );
        $url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $listMapView = $r['result'];
        }
        // Get list map ---------

        $map = new \ITECH\Data\Model\MapModel();
        $form = new \ITECH\Admin\Form\ProjectMapForm($map);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $map);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $map = $this->request->getPost('image_view');
                $url = $this->config->application->api_url . 'map/add';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'block_id' => $this->request->getPost('block_id'),
                    'project_id' => $project['id'],
                    'cache' => 'false'
                );
                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'item_id' => $this->request->getPost('block_id'),
                    'image_map_id' => $mapImage['id'],
                    'map' => $map,
                    'user_id' => $userSession['id']
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Vẽ thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'map_image_id' => $mapImage['id'],
                        'project_id' => $project['id']
                    );
                    return $this->response->redirect(array('for' => 'project_map_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể tạo hình ảnh hiển thị.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'blocks' => $blocks,
            'project' => $project,
            'mapImage' => $mapImage,
            'form' => $form,
            'listMapView' => $listMapView
        ));
        $this->view->pick(parent::$theme . '/project/map_add');
    }

    public function mapEditAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $projectId = $this->request->getQuery('project_id', array('int'), -1);
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), -1);
        $id = $this->request->getQuery('id', array('int'), -1);

        // Get project ---------
        $project = array();
        $url = $this->config->application->api_url . 'project/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $projectId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $project = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại dự án này.');
        }
        // --------- Get project

        // Get list block ---------
        $blocks = array();
        $url = $this->config->application->api_url . 'block/list-by-project';
        $get = array(
            'authorized_token' => $authorizedToken,
            'project_id' => $projectId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $blocks = $r['result'];
        }
        // --------- Get list block

        // --------- Get map image
        $url = $this->config->application->api_url . 'map-image/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $mapImageId,
            'item_id' => $project['id'],
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $mapImage = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại hình ảnh này.');
        }
        // Get map image ---------
        // --------- Get list map
        $listMapView = array();
        $url = $this->config->application->api_url . 'map/list-by-map-image';
        $get = array(
            'authorized_token' => $authorizedToken,
            'image_map_id' => $mapImageId,
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'cache' => 'false'
        );
        $url = $url . '?' . http_build_query($get);

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $listMapView = $r['result'];
        }

        $arrMap = array();
        if (count($listMapView)) {
            foreach($listMapView as $item) {
                $arrMap[] = $item['block_id'];
            }
        }
        if (count($blocks)) {
            foreach ($blocks as $key => $item) {
                if (in_array($item['id'], $arrMap)) {
                    unset($blocks[$key]);
                }
            }
        }

        // Get list map ---------
        // Get map ---------
        $url = $this->config->application->api_url . 'map/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $id,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        $map = new \ITECH\Data\Model\MapModel();
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $map->id = $r['result']['id'];
            $map->image_map_id = $r['result']['image_map_id'];
            $map->map = $r['result']['map'];
        } else {
            if (isset($r['message'])) {
                throw new \Phalcon\Exception($r['message']);
            } else {
                throw new \Phalcon\Exception('Lỗi, không tồn tại tọa độ này');
            }
        }

        $form = new \ITECH\Admin\Form\ProjectMapForm($map);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $map);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $mapView = $this->request->getPost('image_view');
                $url = $this->config->application->api_url . 'map/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $map->id,
                    'cache' => 'false'
                );
                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'item_id' => $this->request->getPost('block_id'),
                    'map' => $mapView,
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getClientAddress(),
                    'user_id' => $userSession['id']
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Vẽ thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'map_image_id' => $mapImage['id'],
                        'project_id' => $project['id']
                    );
                    return $this->response->redirect(array('for' => 'project_map_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể tạo tọa độ.');
                    }
                }
            }
        }

        $maps = json_decode($map->map);
        $viewMap = array();
        foreach ($maps as $key => $item) {
            if (is_object($item)) {
                $viewMap['data-maphilight'] = json_encode($item);
            } else {
                $viewMap[$key] = $item;
            }
        }

        $this->view->setVars(array(
            'map' => $map,
            'viewMap' => $viewMap,
            'blocks' => $blocks,
            'project' => $project,
            'mapImage' => $mapImage,
            'form' => $form,
            'listMapView' => $listMapView
        ));
        $this->view->pick(parent::$theme . '/project/map_edit');
    }

    public function mapDeleteAction()
    {
        $projectId = $this->request->getQuery('project_id', array('int'), -1);
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), -1);
        $id = $this->request->getQuery('id', array('int'), -1);

        $map = \ITECH\Data\Model\MapModel::findFirst(array(
            'conditions' => 'id = :id: AND image_map_id = :map_image_id:',
            'bind' => array(
                'id' => $id,
                'map_image_id' => $mapImageId
            )
        ));

        if (!$map) {
            throw new \Exception('Không tồn tại hình này.');
        }

        $map->delete();

        $this->flashSession->success('Xoá thành công.');
        return $this->response->redirect(array('for' => 'project_map_add', 'query' => '?' . http_build_query(array('map_image_id' => $mapImageId, 'project_id' => $projectId))));
    }



    public function addAttributeAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $attribute = new \ITECH\Data\Model\AttributeModel();

        $addAction = true;
        $iconsList = array();
        $icons = \ITECH\Data\Model\IconModel::find();

        if (count($icons)) {
            foreach ($icons as $item) {
                $iconsList[] = array(
                    'image_name' => $item->icon1,
                    'image_url' => $this->config->asset->frontend_url . 'upload/icon/black/' . $item->icon1
                );
            }
        }

        $form = new \ITECH\Admin\Form\ProjectAttributeForm($attribute);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $url = $this->config->application->api_url . 'attribute/add?authorized_token=' . $authorizedToken;
                $post = array(
                    'name' => trim(strip_tags($this->request->getPost('name'))),
                    'language' => $this->request->getPost('language'),
                    'type' => $this->request->getPost('type'),
                    'is_search' => \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES,
                    'status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                );

                $post['image_one'] = $this->request->getPost('image_one');
                $post['image_two'] = $this->request->getPost('image_two');

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Thêm thành công.');
                    $query = array('id' => $r['result']['id']);
                    return $this->response->redirect(array('for' => 'project_edit_attribute', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->error($r['message']);
                    } else {
                        $this->flashSession->error('Lỗi, không thể thêm.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'iconsList' => $iconsList,
            'addAction' => $addAction
        ));
        $this->view->pick(parent::$theme . '/project/attribute_add');
    }

    public function editAttributeAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $id = $this->request->getQuery('id', array('int'), '');
        $url = $this->config->application->api_url . 'attribute/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $id,
            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
            'cache' => 'false'
        );

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        $attribute = new \ITECH\Data\Model\AttributeModel();

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $attribute->id = $r['result']['id'];
            $attribute->name = $r['result']['name'];
            $attribute->image_one = $r['result']['image_one'];
            $attribute->image_two = $r['result']['image_two'];
            $attribute->image_one_url = $r['result']['image_one_url'];
            $attribute->image_two_url = $r['result']['image_two_url'];
            $attribute->language = $r['result']['language'];
            $attribute->type = $r['result']['type'];
            $attribute->is_search = $r['result']['is_search'];
            $attribute->status = $r['result']['status'];
        } else {
            if (isset($r['message'])) {
                throw new \Phalcon\Exception($r['message']);
            } else {
                throw new \Phalcon\Exception('Không tồn tại thuộc tính này.');
            }
        }

        $iconsList = array();
        $icons = \ITECH\Data\Model\IconModel::find();

        if (count($icons)) {
            foreach ($icons as $item) {
                $iconsList[] = array(
                    'image_name' => $item->icon1,
                    'image_url' => $this->config->asset->frontend_url . 'upload/icon/black/' . $item->icon1
                );
            }
        }

        $form = new \ITECH\Admin\Form\ProjectAttributeForm($attribute);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $url = $this->config->application->api_url . 'attribute/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $id,
                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                    'cache' => 'false'
                );

                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'name' => trim(strip_tags($this->request->getPost('name'))),
                    'language' => $this->request->getPost('language'),
                    'type' => $this->request->getPost('type'),
                    'is_search' => \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES,
                    'status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE
                );

                $post['image_one'] = $this->request->getPost('image_one');
                $post['image_two'] = $this->request->getPost('image_two');

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Cập nhật thành công.');
                    $query = array('id' => $r['result']['id']);
                    return $this->response->redirect(array('for' => 'project_edit_attribute', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->error($r['message']);
                    } else {
                        $this->flashSession->error('Lỗi, không thể cập nhật.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'attribute' => $attribute,
            'iconsList' => $iconsList
        ));
        $this->view->pick(parent::$theme . '/project/attribute_edit');
    }

    public function deleteAttributeAction()
    {
        $attributeId = $this->request->getQuery('attribute_id', array('int'), '');

        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
            'conditions' => 'id = :attribute_id:',
            'bind' => array('attribute_id' => $attributeId)
        ));

        if (!$attribute) {
            throw new \Exception('Không tồn tại thuộc tính này.');
        }

        $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_REMOVED;
        $attribute->updated_at = date('Y-m-d H:i:s');

        try {
            if (!$attribute->save()) {
                $messages = $attribute->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa.';
                $this->flashSession->error($message);
            } else {
                $this->flashSession->success('Xóa thành công.');
            }

            return $this->response->redirect(array('for' => 'project_list_attribute'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}