<?php
class ApartmentController
{
    public function listByProjectAction()
    {
        $projectId = $this->request->getQuery('project_id', array('int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $projectId,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        ));
        if (!$project) {
            throw new \Exception('Không tồn tại dự án này.');
        }

        $params = array(
            'conditions' => array('project_id' => $projectId),
            'page' => $page,
            'limit' => $limit
        );

        $query = array(
            'project_id' => $projectId,
            'page' => $page
        );

        $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
        $apartments = $apartmentRepo->getPaginationList($params);

        $url = $this->url->get(array('for' => 'apartment_list_by_project'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($apartments->total_pages) ? $apartments->total_pages : 0,
            'page' => $page,
            'pages_display' => 3
        );
        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'apartments' => $apartments,
            'project' => $project,
            'page' => $page
        ));
        $this->view->pick(parent::$theme . '/apartment/list_by_project');
    }

    public function listByBlockAction()
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $blockId = $this->request->getQuery('block_id', array('int'), '');
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
            throw new \Phalcon\Exception('Không tồn tại Block này.');
        }
        // --------- Get Block

        $aParams = array();
        $aParams['page'] = $page;
        $aParams['limit'] = $limit;
        $aParams['cache'] = 'false';
        if ($blockId != '') {
            $aParams['block_id'] = $blockId;
        }
        $aParams['authorized_token'] = $authorizedToken;

        $query = array();
        $query['block_id'] = $blockId;
        $query['page'] = $page;

        $apartments = array();
        $url = $this->config->application->api_url . 'apartment/list';
        $url = $url . '?' . http_build_query($aParams);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $apartments = $r;
        }

        $url = $this->url->get(array('for' => 'apartment_list_by_block'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($apartments['total_pages']) ? $apartments['total_pages'] : 0,
            'page' => $page,
            'pages_display' => 3
        );
        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'apartments' => $apartments,
            'result' => $block,
            'page' => $page
        ));
        $this->view->pick(parent::$theme . '/apartment/list_by_block');
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

        $form = new \ITECH\Admin\Form\ApartmentAttributeForm($attribute);
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
                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
                );

                $post['image_one'] = $this->request->getPost('image_one');
                $post['image_two'] = $this->request->getPost('image_two');

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Thêm thành công.');
                    $query = array('id' => $r['result']['id']);
                    return $this->response->redirect(array('for' => 'apartment_edit_attribute', 'query' => '?' . http_build_query($query)));
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
        $this->view->pick(parent::$theme . '/apartment/attribute_add');
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
            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
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

        $form = new \ITECH\Admin\Form\ApartmentAttributeForm($attribute);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {

                $url = $this->config->application->api_url . 'attribute/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $id,
                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
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
                    return $this->response->redirect(array('for' => 'apartment_edit_attribute', 'query' => '?' . http_build_query($query)));
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
        $this->view->pick(parent::$theme . '/apartment/attribute_edit');
    }

    public function listGalleryAction()
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $apartmentId = $this->request->getQuery('apartment_id', array('int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $apartment = array();
        $url = $this->config->application->api_url . 'apartment/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $apartmentId,
            'cache' => 'false',
            'type' => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR
        );

        $url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $apartment = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại sản phẩm này.');
        }

        $aParams = array();
        $aParams['apartment_id'] = $apartmentId;
        $aParams['page'] = $page;
        $aParams['limit'] = $limit;
        $aParams['cache'] = 'false';
        $aParams['authorized_token'] = $authorizedToken;

        $query = array();
        $query['apartment_id'] = $apartmentId;
        $query['page'] = $page;

        $gallery = array();
        $url = $this->config->application->api_url . 'apartment/gallery/list';
        $url = $url . '?' . http_build_query($aParams);

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $gallery = $r;
        }

        $url = $this->url->get(array('for' => 'apartment_list_gallery'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($gallery['total_pages']) ? $gallery['total_pages'] : 0,
            'page' => $page,
            'pages_display' => 3
        );
        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'gallery' => $gallery,
            'apartment' => $apartment
        ));
        $this->view->pick(parent::$theme . '/apartment/list_gallery');
    }

    public function addGalleryAction()
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $apartmentId = $this->request->getQuery('apartment_id', array('int'), '');
        $apartment = array();
        $url = $this->config->application->api_url . 'apartment/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $apartmentId,
            'cache' => 'false',
            'type' => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR
        );
        $url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $apartment = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại sản phẩm này.');
        }

        $gallery = new \ITECH\Data\Model\ApartmentGalleryModel();
        $form = new \ITECH\Admin\Form\ApartmentGalleryForm($gallery);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $gallery);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {

                $url = $this->config->application->api_url . 'apartment/gallery/add';
                $get = array(
                    'apartment_id' => $apartment['id'],
                    'authorized_token' => $authorizedToken
                );
                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'name' => $this->request->getPost('name'),
                    'price' => $this->request->getPost('price'),
                    'gallery' => json_encode($this->request->getPost('gallery'))
                );

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Thêm thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'apartment_id' => $apartmentId
                    );
                    return $this->response->redirect(array('for' => 'apartment_edit_gallery', 'query' => '?' . http_build_query($query)));
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
            'apartment' => $apartment
        ));
        $this->view->pick(parent::$theme . '/apartment/gallery_add');
    }

    public function editGalleryAction()
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $apartmentId = $this->request->getQuery('apartment_id', array('int'), '');
        $id = $this->request->getQuery('id', array('int'), '');

        $apartment = array();
        $url = $this->config->application->api_url . 'apartment/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $apartmentId,
            'cache' => 'false',
            'type' => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR
        );
        $url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $apartment = $r['result'];
        } else {
            throw new \Phalcon\Exception('Không tồn tại sản phẩm này.');
        }

        $url = $this->config->application->api_url . 'apartment/gallery/detail';
        $get = array(
            'authorized_token' => $authorizedToken,
            'id' => $id,
            'apartment_id' => $apartmentId,
            'cache' => 'false'
        );

        $url = $url . '?' . http_build_query($get);
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $gallery = new \ITECH\Data\Model\ApartmentGalleryModel();
        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $gallery->id = $r['result']['id'];
            $gallery->apartment_id = $r['result']['apartment_id'];
            $gallery->name = $r['result']['name'];
            $gallery->price = $r['result']['price'];
            $gallery->gallery = json_decode($r['result']['gallery']);
        } else {
            if (isset($r['message'])) {
                throw new \Phalcon\Exception($r['message']);
            } else {
                throw new \Phalcon\Exception('Lỗi, không tồn tại gallery này.');
            }
        }

        $form = new \ITECH\Admin\Form\ApartmentGalleryForm($gallery);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $gallery);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {

                $url = $this->config->application->api_url . 'apartment/gallery/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $id,
                    'apartment_id' => $apartmentId,
                    'cache' => 'false'
                );

                $url = $url . '?' . http_build_query($get);
                $post = array(
                    'name' => $this->request->getPost('name'),
                    'price' => $this->request->getPost('price')
                );

                $gallery = $this->request->getPost('gallery');

                if (count($gallery) > 0) {
                    $post['gallery'] = json_encode(array_unique($gallery));
                } else {
                    $post['gallery'] = json_encode(array());
                }

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Cập nhật thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'apartment_id' => $apartmentId
                    );
                    return $this->response->redirect(array('for' => 'apartment_edit_gallery', 'query' => '?' . http_build_query($query)));
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
            'gallery' => $gallery,
            'apartment' => $apartment
        ));
        $this->view->pick(parent::$theme . '/apartment/gallery_edit');
    }

    public function deleteGalleryAction()
    {
        $id = $this->request->getQuery('id', array('int'), '');
        $apartmentId = $this->request->getQuery('apartment_id', array('int'), '');

        $gallery = \ITECH\Data\Model\ApartmentGalleryModel::findFirst(array(
            'conditions' => 'id = :gallery_id: AND apartment_id = :apartment_id:',
            'bind' => array(
                'gallery_id' => $id,
                'apartment_id' => $apartmentId
            )
        ));

        if (!$gallery) {
            throw new \Exception('Không tồn tại gallery này.');
        }

        $gallery->delete();
        $this->flashSession->success('Xóa thành công.');

        return $this->response->redirect(array('for' => 'apartment_list_gallery', 'query' => '?' . http_build_query(array('apartment_id' => $apartmentId))));
    }
    public function deleteAttributeAction()
    {
        $id = $this->request->getQuery('id', array('int'), '');

        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
            'conditions' => 'id = :attribute_id:',
            'bind' => array('attribute_id' => $id)
        ));

        if (!$attribute) {
            throw new \Exception('Không tồn tại thuộc tính này.');
        }

        $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_REMOVED;

        try {
            if (!$attribute->save()) {
                $messages = $attribute->getMessages();
                $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xoá.';
                $this->flashSession->error($m);
            } else {
                $this->flashSession->success('Xoá thành công.');
            }

            return $this->response->redirect(array('for' => 'apartment_list_attribute'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}