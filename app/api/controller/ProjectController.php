<?php
namespace ITECH\Api\Controller;

class ProjectController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function allAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $cacheName = md5(serialize(array(
            'ProjectController',
            'allAction',
            'ProjectModel',
            'find',
            \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
        )));

        $projects = $this->cache->get($cacheName);
        if (!$projects) {
            $projects = \ITECH\Data\Model\ProjectModel::find(array(
                'conditions' => 'status = :status:',
                'bind' => array('status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE)
            ));

            $this->cache->save($cacheName, $projects);
        }

        if (count($projects)) {
            foreach ($projects as $item) {
                $response['result'][] = array(
                    'id' => (int)$item->id,
                    'name' => $item->name,
                    'name_eng' => $item->name_eng,
                    'slug' => $item->slug,
                    'slug_eng' => $item->slug_eng
                );

                $response['total_items'] = count($projects);
            }
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function listAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $status = $this->request->getQuery('status', array('int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);

        $params = array(
            'order' => 'p1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        if ($q != '') {
            $params['conditions']['q'] = $q;
        }

        if ($status != '') {
            $params['conditions']['status'] = $status;
        }

        if ($sortField != '' && $sortBy != '') {
            if (!in_array($sortBy, array('ASC', 'DESC'))) {
                $sortBy = 'DESC';
            }

            switch ($sortField) {
                case 'updated_at':
                    $params['order'] = 'p1.updated_at ' . $sortBy;
                break;

                case 'id':
                    $params['order'] = 'p1.id ' . $sortBy;
                break;

                case 'view_count':
                    $params['order'] = 'p1.view_count ' . $sortBy . ', p1.id DESC';
                break;
            }
        }

        $cacheName = md5(serialize(array(
            'ProjectController',
            'listAction',
            'ProjectRepo',
            'getPaginationList',
            $params
        )));

        $projects = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$projects) {
            $projectRepo = new \ITECH\Data\Repo\ProjectRepo();

            if (isset($params['conditions']['attributes'])) {
                $params['order'] = 'id DESC';

                if ($sortField != '' && $sortBy != '') {
                    if (!in_array($sortBy, array('ASC', 'DESC'))) {
                        $sortBy = 'DESC';
                    }

                    switch ($sortField) {
                        case 'updated_at':
                            $params['order'] = 'updated_at ' . $sortBy;
                        break;

                        case 'id':
                            $params['order'] = 'id ' . $sortBy;
                        break;

                        case 'view_count':
                            $params['order'] = 'view_count ' . $sortBy;
                        break;
                    }
                }

                $projects = $projectRepo->getPaginationListByAttribute($params);
            } else {
                $projects = $projectRepo->getPaginationList($params);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $projects);
            }
        }

        //$attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        foreach ($projects->items as $item) {
            $attributeType = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE, $item['id'], $cache);
            $attributeTypeEng = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE, $item['id'], $cache);

            $attributeView = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW, $item['id'], $cache);
            $attributeViewEng = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW, $item['id'], $cache);

            $attributeUtility = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY, $item['id'], $cache);
            $attributeUtilityEng = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY, $item['id'], $cache);

            $defaultImageUrl = parent::$noImageUrl;
            if ($item['default_image'] != '') {
                $defaultImageUrl = $this->config->cdn->dir_upload . $item['default_image'];
            }

            $imageViewUrl = parent::$noImageUrl;
            if ($item['image_view'] != '') {
                $imageViewUrl = $this->config->cdn->dir_upload . $item['image_view'];
            }

            $planViewUrl = parent::$noImageUrl;
            if ($item['plan_view'] != '') {
                $planViewUrl = $this->config->cdn->dir_upload . $item['plan_view'];
            }

            $galleryUrl = array();
            $gallery = json_decode($item['gallery'], true);
            if (count($gallery)) {
                foreach ($gallery as $g) {
                    if ($g != '') {
                        $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                    }
                }
            }

            $direction = \ITECH\Data\Lib\Constant::getDirection();

            $specialCount = 0;

            $cacheName = md5(serialize(array(
                'ProjectController',
                'listAction',
                'ArticleModel',
                'createBuilder',
                'count',
                'special',
                $item['id']
            )));

            $specialCount = $this->cache->get($cacheName);
            if (!$specialCount) {
                $specialCount = 0;

                $articleModel = new \ITECH\Data\Model\ArticleModel();
                $b = $articleModel->getModelsManager()->createBuilder();

                $b->columns(array('COUNT(*) AS row_count'));
                $b->from(array('a1' => 'ITECH\Data\Model\ArticleModel'));
                $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = a1.project_id', 'p1');
                $b->where('p1.id = :project_id:', array('project_id' => $item['id']));
                $b->andWhere('a1.status = :article_status:', array('article_status' => \ITECH\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE));

                $result = $b->getQuery()->execute();
                if (isset($result[0]['row_count'])) {
                    $specialCount = $result[0]['row_count'];
                }

                $this->cache->save($cacheName, $specialCount);
            }

            $response['result'][] = array(
                'id' => (int)$item['id'],
                'hash_id' => \ITECH\Data\Lib\Util::hashId($item['id']),
                'name' => $item['name'],
                'name_eng' => $item['name_eng'],
                'slug' => $item['slug'],
                'slug_eng' => $item['slug_eng'],
                'address' => $item['address'],
                'address_eng' => $item['address_eng'],
                'description' => \ITECH\Data\Lib\Util::htmlEntityDecode($item['description']),
                'description_eng' => \ITECH\Data\Lib\Util::htmlEntityDecode($item['description_eng']),
                'default_image' => $item['default_image'],
                'default_image_url' => $defaultImageUrl,
                'gallery' => $item['gallery'],
                'gallery_url' => $galleryUrl,
                'image_view' => $item['image_view'],
                'image_view_url' => $imageViewUrl,
                'plan_view' => $item['plan_view'],
                'plan_view_url' => $planViewUrl,
                'block_count' => (int)$item['block_count'],
                'apartment_count' => (int)$item['apartment_count'],
                'available_count' => (int)$item['available_count'],
                'processing_count' => (int)$item['processing_count'],
                'sold_count' => (int)$item['sold_count'],
                'direction' => (int)$item['direction'],
                'direction_text' => isset($direction[$item['direction']]) ? $direction[$item['direction']] : '',
                'attribute' => [
                    'type' => $attributeType,
                    'type_eng' => $attributeTypeEng,
                    'view' => $attributeView,
                    'view_eng' => $attributeViewEng,
                    'utility' => $attributeUtility,
                    'utility_eng' => $attributeUtilityEng
                ],
                'view_count' => (int)$item['view_count'],
                'total_area' => $item['total_area'],
                'green_area' => $item['green_area'],
                'status' => (int)$item['status'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'is_special' => $specialCount > 0 ? true : false,
                'province' => [
                    'id' => (int)$item['province_id'],
                    'name' => $item['province_name']
                ],
                'district' => [
                    'id' => (int)$item['district_id'],
                    'name' => $item['district_name']
                ]
            );
        }

        $response['total_items'] = (int)$projects->total_items;
        $response['total_pages'] = isset($projects->total_pages) ? (int)$projects->total_pages : (int)ceil($projects->total_items / $limit);

        RETURN_RESPONSE:
            parent::outputJSON($response);
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

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('name', new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên dự án ít nhất phải 5 ký tự.'
            )));
            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên dự án.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));

            $validator->add('name_eng', new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên dự án ít nhất phải 5 ký tự.'
            )));
            $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên dự án.'
            )));
            $validator->setFilters('name_eng', array('striptags', 'trim'));

            $validator->setFilters('description', array('striptags', 'trim'));
            $validator->setFilters('description_eng', array('striptags', 'trim'));

            $validator->setFilters('address', array('striptags', 'trim'));
            $validator->setFilters('address_eng', array('striptags', 'trim'));

            $validator->setFilters('address_latitude', array('striptags', 'trim'));
            $validator->setFilters('address_longitude', array('striptags', 'trim'));

            $validator->setFilters('default_image', array('striptags', 'trim'));
            $validator->setFilters('direction', array('striptags', 'trim'));

            $validator->setFilters('area', array('striptags', 'trim'));
            $validator->setFilters('space', array('striptags', 'trim'));
            $validator->setFilters('status', array('striptags', 'trim'));
            $validator->setFilters('created_by', array('striptags', 'trim', 'int'));
            $validator->setFilters('updated_by', array('striptags', 'trim', 'int'));
            $validator->setFilters('approved_by', array('striptags', 'trim', 'int'));
            $validator->setFilters('user_id', array('striptags', 'trim', 'int'));
            $validator->setFilters('apartment_count', array('striptags', 'trim', 'int'));
            $validator->setFilters('block_count', array('striptags', 'trim', 'int'));

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

            $name = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name'));
            $name_eng = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name_eng'));

            $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'name = :name:',
                'bind' => array('name' => $name)
            ));

            if ($project) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Đã tồn tại tên dự án này.'
                );
                goto RETURN_RESPONSE;
            }

            $project = new \ITECH\Data\Model\ProjectModel();


            $project->name = $name;
            $project->name_eng = $name_eng;
            $project->slug = \ITECH\Data\Lib\Util::slug($project->name);
            $project->description = $validator->getValue('description');
            $project->description_eng = $validator->getValue('description_eng');

            $project->province_id = $validator->getValue('province_id');
            $project->district_id = $validator->getValue('district_id');
            $project->address = $validator->getValue('address');
            $project->address_eng = $validator->getValue('address_eng');

            $project->address_latitude = $validator->getValue('address_latitude');
            $project->address_longitude = $validator->getValue('address_longitude');

            $project->default_image = $validator->getValue('default_image');
            $project->direction = $validator->getValue('direction');
            $project->area = $validator->getValue('area');
            $project->space = $validator->getValue('space');
            $project->status = $validator->getValue('status');
            $project->view_count = (int)0;
            $project->floor_count = (int)0;
            $project->apartment_count = (int) $validator->getValue('apartment_count');
            $project->block_count = (int) $validator->getValue('block_count');
            $project->created_by = $validator->getValue('created_by');
            $project->updated_by = $validator->getValue('updated_by');
            $project->approved_by = $validator->getValue('approved_by');
            $project->created_at = date('Y-m-d H:i:s');
            $project->updated_at = date('Y-m-d H:i:s');

            try {
                if (!$project->create()) {
                    $messages = $project->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo dự án.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                if (isset($post->property_type) && $post->property_type != '') {
                    parent::saveAttrProject($post->property_type, $project->id, \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE, 1);
                }

                if (isset($post->property_view) && $post->property_view != '') {
                    parent::saveAttrProject($post->property_view, $project->id, \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW, 1);
                }

                if (isset($post->property_utility) && $post->property_utility != '') {
                    parent::saveAttrProject($post->property_utility, $project->id, \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY, 1);
                }

                if (isset($post->property_type_eng) && $post->property_type_eng != '') {
                    parent::saveAttrProject($post->property_type_eng, $project->id, \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE, 2);
                }

                if (isset($post->property_view_eng) && $post->property_view_eng != '') {
                    parent::saveAttrProject($post->property_view_eng, $project->id, \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW, 2);
                }

                if (isset($post->property_utility_eng) && $post->property_utility_eng != '') {
                    parent::saveAttrProject($post->property_utility_eng, $project->id, \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY, 2);
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$project->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[ProjectController][addAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Invalid POST method.'
            );
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function detailAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'lower'), \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $cacheName = md5(serialize(array(
            'ProjectModel',
            'findFirst',
            $id,
            $type
        )));

        $project = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$project) {
            switch ($type) {
                case \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_AGENT:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR:
                    $query = array(
                        'conditions' => 'id = :id:',
                        'bind' => array(
                            'id' => $id
                        )
                    );
                break;

                default:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
                        )
                    );
                break;
            }

            if ($id != '') {
                $project = \ITECH\Data\Model\ProjectModel::findFirst($query);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $project);
            }
        }

        if (!$project) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại dự án này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $project->view_count = $project->view_count + 1;
            $project->save();
        }

        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('user_agent', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALuser_id_AGENT'
            )));

            $validator->add('ip', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_IP'
            )));

            $validator->setFilters('user_agent', array('striptags', 'trim'));
            $validator->setFilters('ip', array('striptags', 'trim'));

            if (isset($post->name)) {
                $validator->add('name', new \Phalcon\Validation\Validator\StringLength(array(
                    'min' => 5,
                    'messageMinimum' => 'Tên dự án ít nhất phải 5 ký tự.'
                )));
                $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập tên dự án.'
                )));
                $validator->setFilters('name', array('striptags', 'trim'));
            }

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

            if ($validator->getValue('name')) {
                $name = \ITECH\Data\Lib\Util::upperFirstLetters(\ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name')));
                $otherProject = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'id <> :id: AND name = :name:',
                    'bind' => array(
                        'id' => $project->id,
                        'name' => $name
                    )
                ));

                if ($otherProject) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên dự án này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $project->name = $name;
                $project->slug = \ITECH\Data\Lib\Util::slug($project->name);
            }

            if (isset($post->description)) {
                $project->description = $post->description;
            }

            if (isset($post->province_id)) {
                $project->province_id = $post->province_id;
            }

            if (isset($post->district_id)) {
                $project->district_id = $post->district_id;
            }
            if (isset($post->address)) {
                $project->address = $post->address;
            }
            if (isset($post->address_latitude)) {
                $project->address_latitude = $post->address_latitude;
            }

            if (isset($post->address_longitude)) {
                $project->address_longitude = $post->address_longitude;
            }

            if (isset($post->default_image)) {
                $project->default_image = $post->default_image;
            }

            if (isset($post->gallery)) {
                $project->gallery = $post->gallery;
            }

            if (isset($post->direction)) {
                $project->direction = $post->direction;
            }

            if (isset($post->area)) {
                $project->area = $post->area;
            }

            if (isset($post->space)) {
                $project->space = $post->space;
            }

            if (isset($post->status)) {
                $project->status = $post->status;
            }

            if (isset($post->updated_by)) {
                $project->updated_by = $post->updated_by;
            }

            if (isset($post->approved_by)) {
                $project->approved_by = $post->approved_by;
            }

            try {
                if (!$project->update()) {
                    $messages = $project->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không cập nhật dự án.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                if (isset($post->property_type) && $post->property_type != '') {
                    $params = array(
                        'conditions' => array(
                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                            'project_id' => $project->id
                        )
                    );
                    $attributeType = $attributeRepo->getListByProject($params);
                    $type = array();
                    if (count($attributeType)) {
                        foreach($attributeType as $item) {
                            $type[] = $item->id;
                        }
                    }

                    $propertyTypes = explode(',', $post->property_type);
                    if (count($propertyTypes)) {
                        foreach($propertyTypes as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                                )
                            ));

                            if ($attribute) {
                                if (!in_array($attribute->id, $type)) {
                                    $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                    $projectAttribute->project_id = $project->id;
                                    $projectAttribute->attribute_id = $attribute->id;
                                    $projectAttribute->create();
                                } else {
                                    foreach ($type as $k => $v) {
                                        if ($v == $attribute->id) {
                                            unset($type[$k]);
                                        }
                                    }
                                }
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT;
                                $attribute->create();

                                $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                $projectAttribute->project_id = $project->id;
                                $projectAttribute->attribute_id = $attribute->id;
                                $projectAttribute->create();
                            }
                        }

                        if (count($type)) {
                            $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                            $q = 'DELETE FROM `land_project_attribute`
                                WHERE `attribute_id` IN (' . implode(',', $type) . ')';
                            $projectAttribute->getWriteConnection()->query($q);
                        }
                    }
                } else {
                    $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                    $q = 'DELETE `pa1` FROM `land_project_attribute` AS `pa1`
                        INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `pa1`.`attribute_id`
                        WHERE `pa1`.`project_id` = "' . $project->id . '" AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE . '"';
                    $projectAttribute->getWriteConnection()->query($q);
                }

                if (isset($post->property_view) && $post->property_view != '') {
                    $params = array(
                        'conditions' => array(
                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                            'project_id' => $project->id
                        )
                    );
                    $attributeView = $attributeRepo->getListByProject($params);
                    $view = array();
                    if (count($attributeView)) {
                        foreach($attributeView as $item) {
                            $view[] = $item->id;
                        }
                    }

                    $propertyViews = array_filter(explode(',', $post->property_view));
                    if (count($propertyViews)) {
                        foreach($propertyViews as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                                )
                            ));

                            if ($attribute) {
                                if (!in_array($attribute->id, $view)) {
                                    $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                    $projectAttribute->project_id = $project->id;
                                    $projectAttribute->attribute_id = $attribute->id;
                                    $projectAttribute->create();
                                } else {
                                    foreach ($view as $k => $v) {
                                        if ($v == $attribute->id) {
                                            unset($view[$k]);
                                        }
                                    }
                                }
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT;
                                $attribute->create();

                                $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                $projectAttribute->project_id = $project->id;
                                $projectAttribute->attribute_id = $attribute->id;
                                $projectAttribute->create();
                            }
                        }

                        if (count($view)) {
                            $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                            $q = 'DELETE FROM `land_project_attribute`
                                WHERE `attribute_id` IN (' . implode(',', $view) . ')';
                            $projectAttribute->getWriteConnection()->query($q);
                        }
                    }
                } else {
                    $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                    $q = 'DELETE `pa1` FROM `land_project_attribute` AS pa1
                        INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `pa1`.`attribute_id`
                        WHERE `pa1`.`project_id` = "' . $project->id . '" AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW . '"';
                    $projectAttribute->getWriteConnection()->query($q);
                }

                if (isset($post->property_utility) && $post->property_utility != '') {
                    $params = array(
                        'conditions' => array(
                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                            'project_id' => $project->id
                        )
                    );
                    $attributeUtility = $attributeRepo->getListByProject($params);
                    $utility = array();
                    if (count($attributeUtility)) {
                        foreach($attributeUtility as $item) {
                            $utility[] = $item->id;
                        }
                    }

                    $propertyUtilities = array_filter(explode(',', $post->property_utility));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                                )
                            ));

                            if ($attribute) {
                                if (!in_array($attribute->id, $utility)) {
                                    $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                    $projectAttribute->project_id = $project->id;
                                    $projectAttribute->attribute_id = $attribute->id;
                                    $projectAttribute->create();
                                } else {
                                    foreach ($utility as $k => $v) {
                                        if ($v == $attribute->id) {
                                            unset($utility[$k]);
                                        }
                                    }
                                }
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT;
                                $attribute->create();

                                $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                $projectAttribute->project_id = $project->id;
                                $projectAttribute->attribute_id = $attribute->id;
                                $projectAttribute->create();
                            }
                        }

                        if (count($utility)) {
                            $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                            $q = 'DELETE FROM `land_project_attribute`
                                WHERE `attribute_id` IN (' . implode(',', $utility) . ')';
                            $projectAttribute->getWriteConnection()->query($q);
                        }
                    }
                } else {
                    $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                    $q = 'DELETE `pa1` FROM `land_project_attribute` AS `pa1`
                        INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `pa1`.`attribute_id`
                        WHERE `pa1`.`project_id` = "' . $project->id . '" AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY . '"';
                    $projectAttribute->getWriteConnection()->query($q);
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$project->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[ProjectController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }
        }

        $attributeTypeProject = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE, $project->id);
        $attributeViewProject = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW, $project->id);
        $attributeUtilityProject = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY, $project->id);

        $attributeType = [];
        $attributeTypeEng = [];
        $attributeView = [];
        $attributeViewEng = [];
        $attributeUtility = [];
        $attributeUtilityEng = [];

        foreach ($attributeTypeProject as $key => $attribute) {
            $attributeType[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name']
            ];
            $attributeTypeEng[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name_eng']
            ];
        }

        foreach ($attributeViewProject as $key => $attribute) {
            $attributeView[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name']
            ];
            $attributeViewEng[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name_eng']
            ];
        }

        foreach ($attributeUtilityProject as $key => $attribute) {
            $attributeUtility[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name']
            ];
            $attributeUtilityEng[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name_eng']
            ];
        }

        $defaultImageUrl = parent::$noImageUrl;
        if ($project->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $project->default_image;
        }

        $imageViewUrl = parent::$noImageUrl;
        if ($project->image_view != '') {
            $imageViewUrl = $this->config->cdn->dir_upload . $project->image_view;
        }

        $planViewUrl = parent::$noImageUrl;
        if ($project->plan_view != '') {
            $planViewUrl = $this->config->cdn->dir_upload . $project->plan_view;
        }

        $galleryUrl = array();
        $gallery = json_decode($project->gallery, true);
        if (count($gallery)) {
            foreach ($gallery as $g) {
                if ($g != '') {
                    $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                }
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();

        // Get province info
        $province = [];
        $cacheName = md5(serialize([
            'ProjectController',
            'detailAction',
            'LocationModel',
            'findFirst',
            $project->province_id
        ]));

        $province = $this->cache->get($cacheName);
        if (!$province) {
            $province = [];

            $p = \ITECH\Data\Model\LocationModel::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $project->province_id]
            ]);
            if ($p) {
                $province = [
                    'id' => (int)$p->id,
                    'name' => $p->name
                ];
            }

            $this->cache->save($cacheName, $province);
        }

        // Get district info
        $district = [];
        $cacheName = md5(serialize([
            'ProjectController',
            'detailAction',
            'LocationModel',
            'findFirst',
            $project->district_id
        ]));

        $district = $this->cache->get($cacheName);
        if (!$district) {
            $district = [];

            $d = \ITECH\Data\Model\LocationModel::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $project->district_id]
            ]);
            if ($d) {
                $district = [
                    'id' => (int)$d->id,
                    'name' => $d->name
                ];
            }

            $this->cache->save($cacheName, $district);
        }

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array(
                'id' => (int)$project->id,
                'name' => $project->name,
                'name_eng' => $project->name_eng,
                'slug' => $project->slug,
                'slug_eng' => $project->slug_eng,
                'description' => \ITECH\Data\Lib\Util::htmlEntityDecode($project->description),
                'description_eng' => \ITECH\Data\Lib\Util::htmlEntityDecode($project->description_eng),
                'address' => $project->address,
                'address_eng' => $project->address_eng,
                'address_latitude' => $project->address_latitude,
                'address_longitude' => $project->address_longitude,
                'default_image' => $project->default_image,
                'default_image_url' => $defaultImageUrl,
                'image_view' => $project->image_view,
                'image_view_url' => $imageViewUrl,
                'plan_view' => $project->plan_view,
                'plan_view_url' => $planViewUrl,
                'gallery' => $project->gallery,
                'gallery_url' => $galleryUrl,
                'block_count' => (int)$project->block_count,
                'apartment_count' => (int)$project->apartment_count,
                'available_count' => (int)$project->available_count,
                'processing_count' => (int)$project->processing_count,
                'sold_count' => (int)$project->sold_count,
                'direction' => (int)$project->direction,
                'direction_text' => isset($direction[$project->direction]) ? $direction[$project->direction] : '',
                'attribute' => [
                    'type' => $attributeType,
                    'type_eng' => $attributeTypeEng,
                    'view' => $attributeView,
                    'view_eng' => $attributeViewEng,
                    'utility' => $attributeUtility,
                    'utility_eng' => $attributeUtilityEng
                ],
                'total_area' => $project->total_area,
                'green_area' => $project->green_area,
                'view_count' => (int)$project->view_count,
                'status' => (int)$project->status,
                'created_by' => (int)$project->created_by,
                'updated_by' => (int)$project->updated_by,
                'approved_by' => (int)$project->approved_by,
                'province' => $province,
                'district' => $district
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function fullAction()
    {
        $response = array(
            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result'  => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $params = array(
            'conditions' => array(
                'id'     => $id,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        );

        $cacheName = md5(serialize(array(
            'ProjectController',
            'fullAction',
            'ProjectModel',
            'findFirst',
            $params
        )));

        $project = ($cache == 'true') ? $this->cache->get($cacheName) : null;

        if (!$project) {
            $projectRepo = new \ITECH\Data\Repo\ProjectRepo();
            $project = $projectRepo->getDetail($params);

            if (isset($project[0])) {
                $project = $project[0];
            } else {
                $project = array();
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $project);
            }
        }

        if (!$project) {
            $response = array(
                'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại dự án này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $projectModel = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind'       => array('id' => $id)
            ));

            $projectModel->view_count = $project->view_count + 1;
            $projectModel->save();
        }

        $attributeType = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE, $project['id'], $cache);
        $attributeView = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW, $project['id'], $cache);
        $attributeUtility = parent::getAttrProject(\ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY, $project['id'], $cache);

        $mapImageList = \ITECH\Data\Model\MapImageModel::find(array(
            'conditions' => 'item_id = :item_id: 
                AND module = :module:',
            'bind' => array(
                'item_id' => $project->id,
                'module'  => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT
            ))
        );

        $mapViewList = array();
        $galleries = array();

        if (count($mapImageList)) {
            foreach ($mapImageList as $item) {
                $reMap = array();

                if (
                    $item->type == \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_FLOOR
                    && $item->position == \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_MAP
                ) {
                    $mapList = \ITECH\Data\Model\MapModel::find(array(
                        'conditions' => 'map_image_id = :map_image_id:',
                        'bind' => array('map_image_id' => $item->id)
                    ));

                    if (count($mapList)) {
                        foreach ($mapList as $itemMap) {
                            if (count(json_decode($itemMap->point))) {
                                $viewMap = array();

                                foreach (json_decode($itemMap->point) as $key => $value) {
                                    if (is_object($value)) {
                                        $viewMap['data-maphilight'] = json_encode($value);
                                    } else {
                                        $viewMap[$key] = $value;
                                    }
                                }

                                $reMap[$itemMap->item_id] = $viewMap;
                            }
                        }
                    }

                    $mapViewList[] = array(
                        'id'       => (int)$item->id,
                        'image'    => $item->image,
                        'view_map' => $reMap
                    );
                } else {
                    $galleries[$item->type][] = array(
                        'id'       => (int)$item->id,
                        'image'    => $item->image,
                        'view_map' => $reMap
                    );
                }
            }
        }

        $blockList = array();

        $blocks = \ITECH\Data\Model\BlockModel::find(array(
            'conditions' => 'project_id = :project_id:',
            'bind' => array('project_id' => $project->id))
        );

        if (count($blocks)) {
            foreach ($blocks as $item) {
                $blockList[$item->id] = array(
                    'id'   => (int)$item->id,
                    'name' => $item->name,
                    'slug' => $item->slug
                );
            }
        }

        $cacheName = md5(serialize(array(
            'ProjectController',
            'fullAction',
            'ApartmentRepo',
            'getQuery',
            'execute',
            $project->id,
            \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE
        )));

        $apartmentAvailableCount = $this->cache->get($cacheName);

        if (!$apartmentAvailableCount) {
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
            $b = $apartmentRepo->getModelsManager()->createBuilder();
            $b->columns(array('COUNT(*) AS total'));

            $b->from(array('ap1' => 'ITECH\Data\Model\ApartmentModel'));
            $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = ap1.block_id', 'b1');
            $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

            $b->andWhere('ap1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
            $b->andWhere('ap1.condition = :apartment_condition:', array('apartment_condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE));
            $b->andWhere('b1.status = :block_status:', array('block_status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE));
            $b->andWhere('p1.id = :project_id:', array('project_id' => $project->id));

            $result = $b->getQuery()->execute();
            $apartmentAvailableCount = isset($result[0]['total']) ? (int)$result[0]['total'] : 0;

            $p = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'id = :project_id:',
                'bind' => array('project_id' => $project->id)
            ));
            $p->available_count = $apartmentAvailableCount;
            $p->save();

            $this->cache->save($cacheName, $apartmentAvailableCount);
        }

        $cacheName = md5(serialize(array(
            'ProjectController',
            'fullAction',
            'ApartmentRepo',
            'getQuery',
            'execute',
            $project->id,
            \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD
        )));

        $apartmentProcessingCount = $this->cache->get($cacheName);

        if (!$apartmentProcessingCount) {
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
            $b = $apartmentRepo->getModelsManager()->createBuilder();
            $b->columns(array('COUNT(*) AS total'));

            $b->from(array('ap1' => 'ITECH\Data\Model\ApartmentModel'));
            $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = ap1.block_id', 'b1');
            $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

            $b->andWhere('ap1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
            $b->andWhere('ap1.condition = :apartment_condition:', array('apartment_condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD));
            $b->andWhere('b1.status = :block_status:', array('block_status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE));
            $b->andWhere('p1.id = :project_id:', array('project_id' => $project->id));

            $result = $b->getQuery()->execute();
            $apartmentProcessingCount = isset($result[0]['total']) ? (int)$result[0]['total'] : 0;

            $p = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'id = :project_id:',
                'bind' => array('project_id' => $project->id)
            ));
            $p->processing_count = $apartmentProcessingCount;
            $p->save();

            $this->cache->save($cacheName, $apartmentProcessingCount);
        }

        $cacheName = md5(serialize(array(
            'ProjectController',
            'fullAction',
            'ApartmentRepo',
            'getQuery',
            'execute',
            $project->id,
            \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD
        )));

        $apartmentSoldCount = $this->cache->get($cacheName);

        if (!$apartmentSoldCount) {
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
            $b = $apartmentRepo->getModelsManager()->createBuilder();
            $b->columns(array('COUNT(*) AS total'));

            $b->from(array('ap1' => 'ITECH\Data\Model\ApartmentModel'));
            $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = ap1.block_id', 'b1');
            $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

            $b->andWhere('ap1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
            $b->andWhere('ap1.condition = :apartment_condition:', array('apartment_condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD));
            $b->andWhere('b1.status = :block_status:', array('block_status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE));
            $b->andWhere('p1.id = :project_id:', array('project_id' => $project->id));

            $result = $b->getQuery()->execute();
            $apartmentSoldCount = isset($result[0]['total']) ? (int)$result[0]['total'] : 0;

            $p = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'id = :project_id:',
                'bind' => array('project_id' => $project->id)
            ));
            $p->sold_count = $apartmentSoldCount;
            $p->save();

            $this->cache->save($cacheName, $apartmentSoldCount);
        }

        $defaultImageUrl = parent::$noImageUrl;

        if ($project->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $project->default_image;
        }

        $imageViewUrl = parent::$noImageUrl;

        if ($project->image_view != '') {
            $imageViewUrl = $this->config->cdn->dir_upload . $project->image_view;
        }

        $planViewUrl = parent::$noImageUrl;

        if ($project->plan_view != '') {
            $planViewUrl = $this->config->cdn->dir_upload . $project->plan_view;
        }

        $galleryResult = json_decode($project->gallery, true);
        $galleryUrl = array();

        if (count($galleryResult)) {
            foreach ($galleryResult as $g) {
                if ($g != '') {
                    $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                }
            }
        }

        $textTrend = \ITECH\Data\Lib\Constant::getDirection();

        $response = array(
            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result'  => array(
                'id'              => (int)$project->id,
                'name'            => $project->name,
                'name_eng'        => $project->name_eng,
                'slug'            => $project->slug,
                'slug_eng'        => $project->slug_eng,
                'description'     => $project->description,
                'description_eng' => $project->description_eng,
                'province' => array(
                    'id'   => (int)$project->province_id,
                    'name' => $project->province_name
                ),
                'district' => array(
                    'id'   => (int)$project->district_id,
                    'name' => $project->district_name
                ),
                'address'           => $project->address,
                'address_eng'       => $project->address_eng,
                'address_latitude'  => $project->address_latitude,
                'address_longitude' => $project->address_longitude,
                'default_image'     => $project->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery'           => $project->gallery,
                'gallery_url'       => $galleryUrl,
                'image_view'        => $project->image_view,
                'image_view_url'    => $imageViewUrl,
                'plan_view'         => $project->plan_view,
                'plan_view_url'     => $planViewUrl,
                'block_count'       => (int)$project->block_count,
                'apartment_count'   => (int)$project->apartment_count,
                'apartment_available_count'  => (int)$apartmentAvailableCount,
                'apartment_processing_count' => (int)$apartmentProcessingCount,
                'apartment_sold_count'       => (int)$apartmentSoldCount,
                'direction'         => (int)$project->direction,
                'direction_text'    => isset($textTrend[$project['direction']]) ? $textTrend[$project['direction']] : '',
                'orientation_value' => (int)$project->direction,
                'orientation'       => isset($textTrend[$project['direction']]) ? $textTrend[$project['direction']] : '',
                'attribute' => array(
                    'type'    => $attributeType,
                    'view'    => $attributeView,
                    'utility' => $attributeUtility,
                ),
                'total_area' => $project->total_area,
                'green_area' => $project->green_area,
                'status'     => (int)$project->status,
                'created_by' => (int)$project->created_by,
                'map_view'   => $mapViewList,
                'galleries'  => $galleries,
                'blocks'     => $blockList
            )
        );

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function otherProjectAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $projectId = $this->request->getQuery('project_id', array('int'), '');
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array(
            'conditions' => 'id <> :id: AND status = :status:',
            'bind' => array(
                'id' => $projectId,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            ),
            'order' => 'id DESC',
            'limit' => $limit
        );

        $cacheName = md5(serialize(array(
            'ProjectController',
            'otherProjectAction',
            'ProjectModel',
            'find',
            $params
        )));

        $projects = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$projects) {
            $projects = \ITECH\Data\Model\ProjectModel::find($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $projects);
            }
        }

        $list = array();
        if (count($projects)) {
            foreach ($projects as $item) {
                $defaultImageUrl = parent::$noImageUrl;
                if ($item->default_image != '') {
                    $defaultImageUrl = $this->config->cdn->dir_upload . $item->default_image;
                }

                $list[] = array(
                    'id' => (int)$item->id,
                    'name' => $item->name,
                    'name_eng' => $item->name_eng,
                    'slug' => $item->slug,
                    'slug_eng' => $item->slug_eng,
                    'default_image' => $item->default_image,
                    'default_image_url' => $defaultImageUrl
                );
            }
        }

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => $list
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}
