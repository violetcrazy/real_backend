<?php
function listByProjectAction()
{
    //$userSession = $this->session->get('USER');
    $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

    $projectId = $this->request->getQuery('project_id', array('int'), -1);
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
    $aParams['page'] = $page;
    $aParams['limit'] = $limit;
    $aParams['cache'] = 'false';
    $aParams['project_id'] = $project['id'];
    $aParams['authorized_token'] = $authorizedToken;

    $query = array();
    $query['project_id'] = $project['id'];
    $query['page'] = $page;

    $blocks = array();
    $url = $this->config->application->api_url . 'block/list';
    $url = $url . '?' . http_build_query($aParams);

    $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
    if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
        $blocks = $r;
    }

    $url = $this->url->get(array('for' => 'block_list_by_project'));
    $options = array(
        'url' => $url,
        'query' => $query,
        'total_pages' => isset($blocks['total_pages']) ? $blocks['total_pages'] : 0,
        'page' => $page,
        'pages_display' => 3
    );
    $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
    $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

    $this->view->setVars(array(
        'paginationLayout' => $paginationLayout,
        'blocks' => $blocks,
        'project' => $project,
        'page' => $page
    ));
    $this->view->pick(parent::$theme . '/block/list_by_project');
}



class BlockController {
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

            return $this->response->redirect(array('for' => 'block_list_attribute'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function addAttributeAction()
    {
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

        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $attribute = new \ITECH\Data\Model\AttributeModel();

        $form = new \ITECH\Admin\Form\ProjectAttributeForm($attribute);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $url = $this->config->application->api_url . 'attribute/add?authorized_token=' . $authorizedToken;
                $post = array(
                    'name' => trim(strip_tags($this->request->getPost('name'))),
                    'language' => $this->request->getPost('language'),
                    'type' => $this->request->getPost('type'),
                    'is_search' => \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES,
                    'status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK
                );

                $post['image_one'] = $this->request->getPost('image_one');
                $post['image_two'] = $this->request->getPost('image_two');

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Thêm thành công.');
                    $query = array('id' => $r['result']['id']);
                    return $this->response->redirect(array('for' => 'block_edit_attribute', 'query' => '?' . http_build_query($query)));
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
            'iconsList' => $iconsList
        ));
        $this->view->pick(parent::$theme . '/block/attribute_add');
    }

    public function editAttributeAction()
    {
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

        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $id = $this->request->getQuery('id', array('int'), '');
        $url = $this->config->application->api_url . 'attribute/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $id,
            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
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

        $form = new \ITECH\Admin\Form\ProjectAttributeForm($attribute);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {

                $url = $this->config->application->api_url . 'attribute/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $id,
                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
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
                    return $this->response->redirect(array('for' => 'block_edit_attribute', 'query' => '?' . http_build_query($query)));
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
        $this->view->pick(parent::$theme . '/block/attribute_edit');
    }

    public function listMapImageAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $blockId = $this->request->getQuery('block_id', array('int'), '');
        //$typeView = $this->request->getQuery('type_view', array('int'), 1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        // Get block ---------
        $block = array();
        $url = $this->config->application->api_url . 'block/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $blockId,
            'cache' => 'false'
        );

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $block = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại block này.');
        }
        // --------- Get block

        $aParams = array();
        $aParams['page'] = $page;
        $aParams['limit'] = $limit;
        $aParams['module'] = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK;
        $aParams['cache'] = 'false';
        $aParams['item_id'] = $block['id'];
        $aParams['authorized_token'] = $authorizedToken;

        $query = array();
        $query['block_id'] = $blockId;
        $query['page'] = $page;

        $mapImages = array();
        $url = $this->config->application->api_url . 'map-image/list';
        $url = $url . '?' . http_build_query($aParams);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $mapImages = $r;
        }

        $url = $this->url->get(array('for' => 'block_list_map_image'));
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
            'result' => $block
        ));
        $this->view->pick(parent::$theme . '/block/list_map_image');
    }

    public function mapImageAddAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $blockId = $this->request->getQuery('block_id', array('int'), -1);

        // Get block ---------
        $block = array();
        $url = $this->config->application->api_url . 'block/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $blockId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $block = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại Block này.');
        }
        // --------- Get block

        $mapImage = new \ITECH\Data\Model\MapImageModel();
        $form = new \ITECH\Admin\Form\ProjectMapImageForm($mapImage);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $mapImage);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $module = \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK;
                $url = $this->config->application->api_url . 'map-image/add';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'item_id' => $block['id'],
                    'module' => $module,
                );

                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'type' => $this->request->getPost('type'),
                    'image' => $this->request->getPost('image_view'),
                    'floor' => $this->request->getPost('floor'),
                    'created_by' => $userSession['id'],
                    'updated_by' => $userSession['id'],
                    'user_id' => $userSession['id']
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Thêm thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'block_id' => $block['id']
                    );
                    return $this->response->redirect(array('for' => 'block_map_image_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể thêm.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'result' => $block,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/block/map_image_add');
    }

    public function mapImageEditAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $blockId = $this->request->getQuery('block_id', array('int'), -1);
        $id = $this->request->getQuery('id', array('int'), '');

        // Get block ---------
        $block = array();
        $url = $this->config->application->api_url . 'block/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $blockId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $block = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại Block này.');
        }
        // --------- Get block

        // --------- Get map image
        $url = $this->config->application->api_url . 'map-image/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $id,
            'item_id' => $block['id'],
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        // Get map image ---------

        $mapImage = new \ITECH\Data\Model\MapImageModel();
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $mapImage->id = $r['result']['id'];
            $mapImage->item_id = $r['result']['item_id'];
            $mapImage->type = $r['result']['type'];
            $mapImage->floor = $r['result']['floor'];
            $mapImage->image = $r['result']['image'];
            $mapImage->module = $r['result']['module'];
            $mapImage->created_at = $r['result']['created_at'];
            $mapImage->updated_at = $r['result']['updated_at'];
        } else {
            if (isset($r['message'])) {
                throw new \Phalcon\Exception($r['message']);
            } else {
                throw new \Phalcon\Exception('Lỗi, không tồn tại hình ảnh này.');
            }
        }

        $form = new \ITECH\Admin\Form\ProjectMapImageForm($mapImage);
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $url = $this->config->application->api_url . 'map-image/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $id,
                    'item_id' => $block['id'],
                    'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
                    'cache' => 'false'
                );
                $url = $url . '?' . http_build_query($get);

                $post = array(
                    'type' => $this->request->getPost('type'),
                    'floor' => $this->request->getPost('floor'),
                    'image' => $this->request->getPost('image_view'),
                    'updated_by' => $userSession['id'],
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getClientAddress(),
                    'user_id' => $userSession['id']
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Cập nhật thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'block_id' => $block['id']
                    );
                    return $this->response->redirect(array('for' => 'block_map_image_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể cập nhật.');
                    }
                }
            }
        }

        $this->view->setVars(array(
            'mapImage' => $mapImage,
            'result' => $block,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/block/map_image_edit');
    }

    public function mapImageDeleteAction()
    {
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), '');
        $blockId = $this->request->getQuery('block_id', array('int'), '');

        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
            'conditions' => 'id = :map_image_id: AND item_id = :map_image_item_id:',
            'bind' => array(
                'map_image_id' => $mapImageId,
                'map_image_item_id' => $blockId
            )
        ));

        if (!$mapImage) {
            throw new \Exception('Không tồn tại hình ảnh chi tiết này.');
        }

        $mapImage->delete();
        $this->flashSession->success('Xóa thành công.');

        return $this->response->redirect(array('for' => 'block_list_map_image', 'query' => '?' . http_build_query(array('block_id' => $blockId))));
    }

    public function mapImageCloneAction()
    {
        $userSession = $this->session->get('USER');
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), '');
        $blockId = $this->request->getQuery('block_id', array('int'), '');
        $floorNumber = $this->request->getQuery('floor_number', array('int'), '');

        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
            'conditions' => 'id = :id:
                AND item_id = :item_id:
                AND type = :type:
                AND module = :module:',
            'bind' => array(
                'id' => $mapImageId,
                'item_id' => $blockId,
                'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_MAP_VIEW,
                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK
            )
        ));
        if (!$mapImage) {
            throw new \Exception('Không tồn tại hình ảnh chi tiết này.');
        }

        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $blockId,
                'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
            )
        ));
        if (!$block) {
            throw new \Exception('Không tồn tại block này.');
        }

        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $block->project_id,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        ));
        if (!$project) {
            throw new \Exception('Không tồn tại dự án này.');
        }

        // --------- Floor list
        $maps = \ITECH\Data\Model\MapImageModel::find(array(
            'conditions' => 'item_id = :item_id:
                AND type = :type:
                AND module = :module:',
            'bind' => array(
                'item_id' => $blockId,
                'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_MAP_VIEW,
                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK
            )
        ));

        $currentFloor = $mapImage->floor;

        if ($floorNumber == $currentFloor) {
            $floorNumber = '';
        }

        $usedFloors = array();
        $floorSelect = array();

        if (count($maps)) {
            foreach ($maps as $item) {
                $usedFloors[] = $item->floor;
            }
        }

        $floors = json_decode($block->floor_name_list, true);

        if (count($floors)) {
            foreach ($floors as $key => $value) {
                if ($currentFloor == $key) {
                    $currentFloor = $value;
                }

                if (!in_array($key, array_values($usedFloors))) {
                    $floorSelect[$key] = $value;
                }
            }
        } else {
            for ($i = 1; $i <= $block->floor_count; $i++) {
                if (!in_array($i, array_values($usedFloors))) {
                    $floorSelect[$i] = $i;
                }
            }
        }

        foreach ($floorSelect as $key => $value) {
            if ($value) {}

            $hasApartment = \ITECH\Data\Model\ApartmentModel::count(array(
                'conditions' => 'block_id = :block_id:
                    AND floor_count = :floor:
                    AND status = :status:',
                'bind' => array(
                    'block_id' => $blockId,
                    'floor' => $key,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));
            if ($hasApartment == 0) {
                unset($floorSelect[$key]);
            }
        }
        // Floor list ---------

        $apartments = \ITECH\Data\Model\ApartmentModel::find(array(
            'conditions' => 'block_id = :block_id:
                AND floor_count = :floor_count:
                AND status = :status:',
            'bind' => array(
                'block_id' => $blockId,
                'floor_count' => $mapImage->floor,
                'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
            )
        ));

        $newApartments = array();
        if ($floorNumber != '') {
            $newApartments = \ITECH\Data\Model\ApartmentModel::find(array(
                'conditions' => 'block_id = :block_id:
                    AND floor_count = :floor_count:
                    AND status = :status:',
                'bind' => array(
                    'block_id' => $blockId,
                    'floor_count' => $floorNumber,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));
        }

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (!isset($post['map_item_id'])) {
                $this->flashSession->error('Thông tin không hợp lệ. Vui lòng chọn các sản phẩm');
            } else {
                $mapItemId = $post['map_item_id'];

                $invalid = false;
                foreach ($post['map_item_id'] as $item) {
                    if ($item == '') {
                        $invalid = true;
                        break;
                    }
                }

                if ($invalid) {
                    $this->flashSession->error('Thông tin không hợp lệ. Vui lòng chọn tất cả các sản phẩm tương ứng');
                } else {
                    if (isset($post['floor'])) {
                        if ($post['floor'] == '') {
                            $this->flashSession->error('Thông tin không hợp lệ. Chưa cung cấp tầng');
                        } else {
                            $newFloor = (int)$post['floor'];

                            if (!in_array($newFloor, array_values($usedFloors))) {
                                $newMapImage = new \ITECH\Data\Model\MapImageModel();
                                $newMapImage->item_id = $mapImage->item_id;
                                $newMapImage->type = $mapImage->type;
                                $newMapImage->floor = $newFloor;
                                $newMapImage->image = $mapImage->image;
                                $newMapImage->module = $mapImage->module;
                                $newMapImage->created_by = $userSession['id'];
                                $newMapImage->created_at = date('Y-m-d H:i:s');

                                if (!$newMapImage->save()) {
                                    $messages = $newMapImage->getMessages();
                                    if (isset($messages[0])) {
                                        $this->flashSession->error($messages[0]->getMessage());
                                    }
                                }

                                $imageMapId = $newMapImage->id;

                                $maps = \ITECH\Data\Model\MapModel::find(array(
                                    'conditions' => 'image_map_id = :image_map_id:',
                                    'bind' => array('image_map_id' => $mapImage->id)
                                ));
                                if (count($maps)) {
                                    foreach ($maps as $item) {
                                        if (isset($mapItemId[$item->item_id])) {
                                            $mapModel = new \ITECH\Data\Model\MapModel();
                                            $mapModel->image_map_id = $imageMapId;
                                            $mapModel->item_id = $mapItemId[$item->item_id];
                                            $mapModel->map = $item->map;
                                            $mapModel->save();
                                        }
                                    }
                                }

                                $this->flashSession->success('Sao chép thành công.');
                                return $this->response->redirect(array('for' => 'block_list_map_image', 'query' => '?' . http_build_query(array('block_id' => $blockId))));
                            }
                        }
                    }
                }
            }
        }

        $this->view->setVars(array(
            'mapImage' => $mapImage,
            'blockModel' => $block,
            'project' => $project,
            'currentFloor' => $currentFloor,
            'floorSelect' => $floorSelect,
            'floorNumber' => $floorNumber,
            'apartments' => $apartments,
            'newApartments' => $newApartments
        ));
        $this->view->pick(parent::$theme . '/block/map_image_clone');
    }

    public function mapAddAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $blockId = $this->request->getQuery('block_id', array('int'), -1);
        $floorCount = $this->request->getQuery('floor_count', array('int'), -1);
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), -1);

        // Get block ---------
        $block = array();
        $url = $this->config->application->api_url . 'block/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $blockId,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $block = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại Block này.');
        }
        // --------- Get block

        // Get list apartment ---------
        $apartments = array();
        $url = $this->config->application->api_url . 'apartment/list-by-block';
        $get = array(
            'authorized_token' => $authorizedToken,
            'block_id' => $blockId,
            'floor_count' => $floorCount,
            'cache' => 'false'
        );
        $url = $url . '?' . http_build_query($get);
        //var_dump($url); exit;
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $apartments = $r['result'];
        }

        // --------- Get list apartment

        // --------- Get map image
        $url = $this->config->application->api_url . 'map-image/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $mapImageId,
            'item_id' => $block['id'],
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
            'cache' => 'false'
        );
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $mapImage = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại hình ảnh này.');
        }
        // Get map image ---------

        // Get list apartment ---------
        $apartments = array();
        $url = $this->config->application->api_url . 'apartment/list-by-block';
        $get = array(
            'authorized_token' => $authorizedToken,
            'block_id' => $blockId,
            'cache' => 'false',
            'floor_count' => $mapImage['floor']
        );
        //$url = $url . '?' . http_build_query($get);
        //var_dump($url); exit;
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $apartments = $r['result'];
        }
        // --------- Get list apartment

        // --------- Get list map
        $listMapView = array();
        $url = $this->config->application->api_url . 'map/list-by-map-image';
        $get = array(
            'authorized_token' => $authorizedToken,
            'image_map_id' => $mapImageId,
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
            'cache' => 'false'
        );
        //$url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $listMapView = $r['result'];
        }

        $arrMap = array();
        if (count($listMapView)) {
            foreach($listMapView as $item) {
                $arrMap[] = $item['apartment_id'];
            }
        }
        if (count($apartments)) {
            foreach ($apartments as $key => $item) {
                if (in_array($item['id'], $arrMap)) {
                    unset($apartments[$key]);
                }
            }
        }

        // Get list map ---------

        $map = new \ITECH\Data\Model\MapModel();
        $form = new \ITECH\Admin\Form\BlockMapForm($map);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $map);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $map = $this->request->getPost('image_view');
                $url = $this->config->application->api_url . 'map/add';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'cache' => 'false'
                );
                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'item_id' => $this->request->getPost('apartment_id'),
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
                        'block_id' => $block['id'],
                        'floor_count' => $mapImage['floor']
                    );
                    return $this->response->redirect(array('for' => 'block_map_edit', 'query' => '?' . http_build_query($query)));
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
            'apartments' => $apartments,
            'result' => $block,
            'mapImage' => $mapImage,
            'form' => $form,
            'listMapView' => $listMapView,
            'arrMap' => $arrMap
        ));
        $this->view->pick(parent::$theme . '/block/map_add');
    }

    public function mapEditAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $blockId = $this->request->getQuery('block_id', array('int'), -1);
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), -1);
        $floorCount = $this->request->getQuery('floor_count', array('int'), -1);
        $id = $this->request->getQuery('id', array('int'), -1);

        // Get block ---------
        $block = array();
        $url = $this->config->application->api_url . 'block/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $blockId,
            'cache' => 'false'
        );

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $block = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại dự án này.');
        }
        // --------- Get project

        // Get list apartment ---------
        $apartments = array();
        $url = $this->config->application->api_url . 'apartment/list-by-block';
        $get = array(
            'authorized_token' => $authorizedToken,
            'block_id' => $blockId,
            'floor_count' => $floorCount,
            'cache' => 'false'
        );
        //$url = $url . '?' . http_build_query($get);
        //var_dump($url); exit;
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $apartments = $r['result'];
        }
        // --------- Get list apartment

        // --------- Get map image
        $url = $this->config->application->api_url . 'map-image/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $mapImageId,
            'item_id' => $block['id'],
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
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
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
            'cache' => 'false'
        );
        //$url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $listMapView = $r['result'];
        }

        $arrMap = array();
        if (count($listMapView)) {
            foreach($listMapView as $item) {
                $arrMap[] = $item['apartment_id'];
            }
        }

        $apartment = array();
        if (count($apartments)) {
            foreach ($apartments as $key => $item) {
                if (in_array($item['id'], $arrMap)) {
                    $apartment = $apartments[$key];
                    unset($apartments[$key]);
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
                    'item_id' => $this->request->getPost('apartment_id'),
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
                        'block_id' => $block['id'],
                        'floor_count' => $floorCount
                    );
                    return $this->response->redirect(array('for' => 'block_map_edit', 'query' => '?' . http_build_query($query)));
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
            'apartments' => $apartments,
            'result' => $block,
            'mapImage' => $mapImage,
            'apartment' => $apartment,
            'form' => $form,
            'listMapView' => $listMapView
        ));
        $this->view->pick(parent::$theme . '/block/map_edit');
    }
}