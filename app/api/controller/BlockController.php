<?php
namespace ITECH\Api\Controller;

class BlockController extends \ITECH\Api\Controller\BaseController
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

        $projectId = $this->request->getQuery('project_id', array('int'), '');

        $cacheName = md5(serialize(array(
            'BlockController',
            'allAction',
            'ProjectModel',
            'find',
            $projectId,
            \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
        )));

        $blocks = $this->cache->get($cacheName);
        if (!$blocks) {
            $blocks = \ITECH\Data\Model\BlockModel::find(array(
                'conditions' => 'project_id = :project_id: AND status = :status:',
                'bind' => array(
                    'project_id' => $projectId,
                    'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                )
            ));

            $this->cache->save($cacheName, $blocks);
        }

        if (count($blocks)) {
            foreach ($blocks as $item) {
                $response['result'][] = array(
                    'id' => (int)$item->id,
                    'name' => $item->name,
                    'name_eng' => $item->name_eng,
                    'slug' => $item->slug,
                    'slug_eng' => $item->slug_eng,
                    'project_id' => (int)$item->project_id
                );
            }
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

        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        //$type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        $status = $this->request->getQuery('status', array('int'), '');
        $projectId = $this->request->getQuery('project_id', array('int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array(
            'conditions' => array(),
            'order' => 'b1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        if ($projectId != '') {
            $cacheName = md5(serialize(array(
                'BlockController',
                'listAction',
                'ProjectModel',
                'findFirst',
                $projectId
            )));

            $project = $cache == 'true' ? $this->cache->get($cacheName) : null;
            if (!$project) {
                $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'id = :id: AND status = :status:',
                    'bind' => array(
                        'id' => $projectId,
                        'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
                    )
                ));

                if ($cache == 'true') {
                    $this->cache->save($cacheName, $project);
                }
            }

            if (!$project) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Lỗi, không tồn tại dự án này.',
                    'result' => array()
                );

                goto RETURN_RESPONSE;
            }
            $params['conditions']['project_id'] = $project->id;
        }

        if ($sortField != '' && $sortBy != '') {
            if (!in_array($sortBy, array('ASC', 'DESC'))) {
                $sortBy = 'DESC';
            }

            switch ($sortField) {
                case 'updated_at':
                    $params['order'] = 'b1.updated_at ' . $sortField;
                break;

                case 'id':
                    $params['order'] = 'b1.id ' . $sortBy;
                break;
            }
        }

        if ($status != '') {
            $params['conditions']['status'] = $status;
        }

        $cacheName = md5(serialize(array(
            'BlockController',
            'listAction',
            'BlockRepo',
            'getPaginationList',
            $params
        )));

        $blocks = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$blocks) {
            $blockRepo = new \ITECH\Data\Repo\BlockRepo();

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

                        case 'price':
                            $params['order'] = 'id ' . $sortBy;
                        break;
                    }
                }

                $blocks = $blockRepo->getPaginationListByAttribute($params);
            } else {
                $blocks = $blockRepo->getPaginationList($params);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $blocks);
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();

        foreach ($blocks->items as $item) {
            $defaultImageUrl = parent::$noImageUrl;
            if ($item->default_image != '') {
                $defaultImageUrl = $this->config->cdn->dir_upload . $item->default_image;
            }

            $galleryUrl = array();
            $gallery = json_decode($item->gallery, true);
            if (count($gallery)) {
                foreach ($gallery as $g) {
                    if ($g != '') {
                        $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                    }
                }
            }

            $attributeType = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $item->id, 1, 'true');
            $$attributeTypeEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $item->id, 2, 'true');

            $attributeView = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $item->id, 1, 'true');
            $attributeViewEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $item->id, 2, 'true');

            $attributeUtility = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $item->id, 1, 'true');
            $attributeUtilityEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $item->id, 2, 'true');

            $availableCount = 0;
            $processingCount = 0;
            $soldCount = 0;

            $cacheName = md5(serialize(array(
                'BlockController',
                'listAction',
                'ApartmentModel',
                'createBuilder',
                'query',
                'available',
                $item->id
            )));

            $availableCount = $this->cache->get($cacheName);
            if (!$availableCount) {
                $availableCount = 0;

                $apartmentModel = new \ITECH\Data\Model\ApartmentModel();
                $b = $apartmentModel->getModelsManager()->createBuilder();

                $b->columns(array('COUNT(*) AS row_count'));
                $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
                $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
                $b->where('a1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
                $b->andWhere('a1.condition = :apartment_condition:', array('apartment_condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE));
                $b->andWhere('b1.id = :block_id:', array('block_id' => $item->id));

                $result = $b->getQuery()->execute();
                if (isset($result[0]['row_count'])) {
                    $availableCount = $result[0]['row_count'];
                }

                $this->cache->save($cacheName, $availableCount);
            }

            $cacheName = md5(serialize(array(
                'BlockController',
                'listAction',
                'ApartmentModel',
                'createBuilder',
                'query',
                'processing',
                $item->id
            )));

            $processingCount = $this->cache->get($cacheName);
            if (!$processingCount) {
                $processingCount = 0;

                $apartmentModel = new \ITECH\Data\Model\ApartmentModel();
                $b = $apartmentModel->getModelsManager()->createBuilder();

                $b->columns(array('COUNT(*) AS row_count'));
                $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
                $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
                $b->where('a1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
                $b->andWhere('a1.condition = :apartment_condition:', array('apartment_condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD));
                $b->andWhere('b1.id = :block_id:', array('block_id' => $item->id));

                $result = $b->getQuery()->execute();
                if (isset($result[0]['row_count'])) {
                    $processingCount = $result[0]['row_count'];
                }

                $this->cache->save($cacheName, $processingCount);
            }

            $cacheName = md5(serialize(array(
                'BlockController',
                'listAction',
                'ApartmentModel',
                'createBuilder',
                'query',
                'sold',
                $item->id
            )));

            $soldCount = $this->cache->get($cacheName);
            if (!$soldCount) {
                $soldCount = 0;

                $apartmentModel = new \ITECH\Data\Model\ApartmentModel();
                $b = $apartmentModel->getModelsManager()->createBuilder();

                $b->columns(array('COUNT(*) AS row_count'));
                $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
                $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
                $b->where('a1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
                $b->andWhere('a1.condition = :apartment_condition:', array('apartment_condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD));
                $b->andWhere('b1.id = :block_id:', array('block_id' => $item->id));

                $result = $b->getQuery()->execute();
                if (isset($result[0]['row_count'])) {
                    $soldCount = $result[0]['row_count'];
                }

                $this->cache->save($cacheName, $soldCount);
            }

            $response['result'][] = array(
                'id' => (int)$item->id,
                'hash_id' => \ITECH\Data\Lib\Util::hashId($item->id),
                'name' => $item->name,
                'name_eng' => $item->name_eng,
                'slug' => $item->slug,
                'slug_eng' => $item->slug_eng,
                'default_image' => $item->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery' => $item->gallery,
                'gallery_url' => $galleryUrl,
                'floor_count' => (int)$item->floor_count,
                'apartment_count' => (int)$item->apartment_count,
                'available_count' => (int)$availableCount,
                'processing_count' => (int)$processingCount,
                'sold_count' => (int)$soldCount,
                'direction' => (int)$item->direction,
                'direction_text' => isset($direction[$item->direction]) ? $direction[$item->direction] : '',
                'orientation_value' => isset($item->direction) ? (int)$item->direction : null,
                'orientation' => isset($direction[$item->direction]) ? $direction[$item->direction] : '',
                'attribute' => [
                    'type' => $attributeType,
                    'type_eng' => $attributeTypeEng,
                    'view' => $attributeView,
                    'view_eng' => $attributeViewEng,
                    'utility' => $attributeUtility,
                    'utility_eng' => $attributeUtilityEng
                ],
                'view_count' => (int)$item->view_count,
                'status' => (int)$item->status,
                'project' => [
                    'id' => (int)$item->project_id,
                    'name' => $item->project_name,
                    'name_eng' => $item->project_name_eng,
                ],
                'created_by' => (int)$item->created_by,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            );
        }

        $response['total_items'] = (int)$blocks->total_items;
        $response['total_pages'] = isset($blocks->total_pages) ? (int)$blocks->total_pages : ceil($blocks->total_items / $limit);

        RETURN_RESPONSE:
            parent::outputJSON($response);

    }

    public function listByProjectAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $projectId = $this->request->getQuery('project_id', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'BlockController',
            'listByProjectAction',
            'ProjectModel',
            'findFirst',
            $projectId
        )));

        $project = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$project) {
            $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'id = :id: AND status = :status:',
                'bind' => array(
                    'id' => $projectId,
                    'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
                )
            ));

            if ($cache == 'true') {
                $this->cache->save($cacheName, $project);
            }
        }

        if (!$project) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không tồn tại dự án này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $cacheName = md5(serialize(array(
            'ProjectController',
            'listByProjectAction',
            'BlockModel',
            'find',
            $projectId
        )));

        $blocks = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$blocks) {
            $blocks = \ITECH\Data\Model\BlockModel::find(array(
                'conditions' => 'project_id = :project_id: AND status = :status:',
                'bind' => array(
                    'project_id' => $project->id,
                    'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                )
            ));

            if ($cache == 'true') {
                $this->cache->save($cacheName, $blocks);
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();

        foreach ($blocks as $item) {
            $defaultImageUrl = parent::$noImageUrl;
            if ($item->default_image != '') {
                $defaultImageUrl = $this->config->cdn->dir_upload . $item->default_image;
            }

            $galleryUrl = array();
            $gallery = json_decode($item->gallery, true);
            if (count($gallery)) {
                foreach ($gallery as $g) {
                    if ($g != '') {
                        $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                    }
                }
            }

            $type = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $item->id, 1, 'true');
            $type_eng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $item->id, 2, 'true');

            $view = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $item->id, 1, 'true');
            $view_eng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $item->id, 2, 'true');

            $utility = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $item->id, 1, 'true');
            $utility_eng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $item->id, 2, 'true');

            $response['result'][] = array(
                'id' => (int)$item->id,
                'hash_id' => \ITECH\Data\Lib\Util::hashId($item->id),
                'name' => $item->name,
                'name_eng' => $item->name_eng,
                'slug' => $item->slug,
                'slug_eng' => $item->slug_eng,
                'default_image' => $item->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery' => $item->gallery,
                'gallery_url' => $galleryUrl,
                'floor_count' => (int)$item->floor_count,
                'apartment_count' => (int)$item->apartment_count,
                'direction' => (int)$item->direction,
                'direction_text' => isset($direction[$item->direction]) ? $direction[$item->direction] : '',
                'attribute' => [
                    'type' => $type,
                    'type_eng' => $type_eng,
                    'view' => $view,
                    'view_eng' => $view_eng,
                    'utility' => $utility,
                    'utility_eng' => $utility_eng
                ],
                'view_count' => (int)$item->view_count,
                'status' => (int)$item->status,
                'created_by' => (int)$item->created_by,
                'updated_by' => (int)$item->updated_by,
                'approved_by' => (int)$item->approved_by,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'project' => [
                    'id' => (int)$project->id,
                    'name' => $project->name,
                    'name_eng' => $project->name_eng
                ]
            );
        }

        $response['total_items'] = count($blocks);

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function addAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Error.',
            'result' => array()
        );

        $project_id = $this->request->getQuery('project_id', array('int'), '');
        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $project_id
            )
        ));

        if (!$project) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không tồn tại dự án này.',
                'project_id' =>  $project_id,
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên Block.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));

            $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên tiếng anh Block.'
            )));
            $validator->setFilters('name_eng', array('striptags', 'trim'));

            $validator->setFilters('shortname', array('striptags', 'trim'));
            $validator->setFilters('default_image', array('striptags', 'trim'));
            $validator->setFilters('floor_count', array('int'));
            $validator->setFilters('apartment_count', array('int'));
            $validator->setFilters('direction', array('striptags', 'trim'));
            $validator->setFilters('attribute_type', array('striptags', 'trim'));
            $validator->setFilters('attribute_view', array('striptags', 'trim'));
            $validator->setFilters('attribute_utility', array('striptags', 'trim'));
            $validator->setFilters('attribute_type_eng', array('striptags', 'trim'));
            $validator->setFilters('attribute_view_eng', array('striptags', 'trim'));
            $validator->setFilters('attribute_utility_eng', array('striptags', 'trim'));
            $validator->setFilters('policy', array('striptags', 'trim'));
            $validator->setFilters('policy_eng', array('striptags', 'trim'));
            $validator->setFilters('description', array('striptags', 'trim'));
            $validator->setFilters('description_eng', array('striptags', 'trim'));
            $validator->setFilters('price', array('striptags', 'trim'));
            $validator->setFilters('price_eng', array('striptags', 'trim'));
            $validator->setFilters('total_area', array('striptags', 'trim'));
            $validator->setFilters('green_area', array('striptags', 'trim'));
            $validator->setFilters('status', array('int'));
            $validator->setFilters('created_by', array('int'));
            $validator->setFilters('user_id', array('int'));

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

            $block = new \ITECH\Data\Model\BlockModel();

            if ($validator->getValue('name')) {
                $name = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name'));

                $otherBlock = \ITECH\Data\Model\BlockModel::findFirst(array(
                    'conditions' => 'project_id = :project_id:
                        AND name = :name:
                        AND status = :status:',
                    'bind' => array(
                        'project_id' => $project_id,
                        'name' => $name,
                        'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                    )
                ));
                if ($otherBlock) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên block này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $block->name = $name;
                $block->slug = \ITECH\Data\Lib\Util::slug($block->name);
            }

            if ($validator->getValue('name_eng')) {
                $name_eng = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name_eng'));

                $otherBlock = \ITECH\Data\Model\BlockModel::findFirst(array(
                    'conditions' => 'project_id = :project_id:
                        AND name_eng = :name_eng:
                        AND status = :status:',
                    'bind' => array(
                        'project_id' => $project_id,
                        'name_eng' => $name_eng,
                        'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                    )
                ));
                if ($otherBlock) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên tiếng anh của block này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $block->name_eng = $name_eng;
                $block->slug_eng = \ITECH\Data\Lib\Util::slug($block->name_eng);
            }

            $block->shortname = $validator->getValue('shortname');
            if ($block->shortname == '') {
                $block->shortname = substr($block->name, 0, 1);
            }

            $block->slug = \ITECH\Data\Lib\Util::slug($block->name);
            $block->project_id = $project->id;
            $block->default_image = $validator->getValue('default_image');
            $block->floor_count = $validator->getValue('floor_count');
            $block->apartment_count = $validator->getValue('apartment_count');
            $block->price = $validator->getValue('price');
            $block->price_eng = $validator->getValue('price_eng');
            $block->direction = $validator->getValue('direction');
            $block->attribute_type = $validator->getValue('attribute_type');
            $block->attribute_view = $validator->getValue('attribute_view');
            $block->attribute_utility = $validator->getValue('attribute_utility');
            $block->attribute_type = $validator->getValue('attribute_type_eng');
            $block->attribute_view = $validator->getValue('attribute_view_eng');
            $block->attribute_utility = $validator->getValue('attribute_utility_eng');
            $block->total_area = $validator->getValue('total_area');
            $block->green_area = $validator->getValue('green_area');
            $block->description = $validator->getValue('description');
            $block->description_eng = $validator->getValue('description_eng');
            $block->policy = $validator->getValue('policy');
            $block->policy_eng = $validator->getValue('policy_eng');
            $block->status = $validator->getValue('status');
            $block->created_by = $validator->getValue('created_by');
            $block->view_count = 0;
            $block->created_at = date('Y-m-d H:i:s');
            $block->updated_at = date('Y-m-d H:i:s');

            if (isset($post->gallery)) {
                $block->gallery = $post->gallery;
            }

            if (isset($post->floor_name_list)) {
                $block->floor_name_list = $post->floor_name_list;
            }

            if (isset($post->apartment_name_list)) {
                $block->apartment_name_list = $post->apartment_name_list;
            }

            try {
                if (!$block->create()) {
                    $messages = $block->getMessages();
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

                if (isset($post->attribute_type)) {
                    parent::setAttrBlock($post->attribute_type, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE);
                }

                if (isset($post->attribute_view)) {
                    parent::setAttrBlock($post->attribute_view, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW);
                }

                if (isset($post->attribute_utility)) {
                    parent::setAttrBlock($post->attribute_utility, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY);
                }

                if (isset($post->attribute_type_eng)) {
                    parent::setAttrBlock($post->attribute_type_eng, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, 2);
                }

                if (isset($post->attribute_view_eng)) {
                    parent::setAttrBlock($post->attribute_view_eng, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, 2);
                }

                if (isset($post->attribute_utility_eng)) {
                    parent::setAttrBlock($post->attribute_utility_eng, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, 2);
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$block->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[BlockController][addAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), true);
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $cacheName = md5(serialize(array(
            'BlockController',
            'detailAction',
            'BlockModel',
            'findFirst',
            $id,
            $type
        )));

        $block = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$block) {
            switch ($type) {
                case \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_AGENT:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR:
                    $query = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $id)
                    );
                break;

                default:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;
            }

            if ($id != '') {
                $block = \ITECH\Data\Model\BlockModel::findFirst($query);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $block);
            }
        }

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại block này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $block->view_count = $block->view_count + 1;
            $block->save();
        }

        //$attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('user_agent', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_USER_AGENT'
            )));

            $validator->add('project_id', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn tên dự án'
            )));

            $validator->add('ip', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_IP'
            )));

            $validator->setFilters('user_agent', array('striptags', 'trim'));
            $validator->setFilters('ip', array('striptags', 'trim'));

            if (isset($post->name)) {
                $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập tên Block.'
                )));
                $validator->setFilters('name', array('striptags', 'trim'));
            }

            if (isset($post->name_eng)) {
                $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập tên tiếng anh Block.'
                )));
                $validator->setFilters('name_eng', array('striptags', 'trim'));
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
                $name = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name'));

                $otherBlock = \ITECH\Data\Model\BlockModel::findFirst(array(
                    'conditions' => 'id <> :id:
                        AND project_id = :project_id:
                        AND name = :name:
                        AND status = :status:',
                    'bind' => array(
                        'id' => $block->id,
                        'project_id' => $validator->getValue('project_id'),
                        'name' => $name,
                        'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                    )
                ));
                if ($otherBlock) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên block này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $block->name = $name;
                $block->slug = \ITECH\Data\Lib\Util::slug($block->name);
            }

            if ($validator->getValue('name_eng')) {
                $name_eng = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name_eng'));

                $otherBlock = \ITECH\Data\Model\BlockModel::findFirst(array(
                    'conditions' => 'id <> :id:
                        AND project_id = :project_id:
                        AND name_eng = :name_eng:
                        AND status = :status:',
                    'bind' => array(
                        'id' => $block->id,
                        'project_id' => $validator->getValue('project_id'),
                        'name_eng' => $name_eng,
                        'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                    )
                ));
                if ($otherBlock) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên tiếng anh của block này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $block->name_eng = $name_eng;
                $block->slug_eng = \ITECH\Data\Lib\Util::slug($block->name_eng);
            }

            if (isset($post->shortname)) {
                $block->shortname = trim(strip_tags($post->shortname));

                if ($block->shortname == '') {
                    $block->shortname = substr($block->name, 0, 1);
                }
            }

            if (isset($post->floor_name_list)) {
                $block->floor_name_list = $post->floor_name_list;
            }

            if (isset($post->apartment_name_list)) {
                $block->apartment_name_list = $post->apartment_name_list;
            }

            $block->default_image = $post->default_image;
            $block->gallery = $post->gallery;

            if (isset($post->status)) {
                $block->status = $post->status;
            }

            if (isset($post->project_id)) {
                $block->project_id = $post->project_id;
            }

            if (isset($post->direction)) {
                $block->direction = $post->direction;
            }

            if (isset($post->description)) {
                $block->description = trim(strip_tags($post->description));
            }

            if (isset($post->description_eng)) {
                $block->description_eng = trim(strip_tags($post->description_eng));
            }

            if (isset($post->policy)) {
                $block->policy = $post->policy;
            }

            if (isset($post->policy_eng)) {
                $block->policy_eng = $post->policy_eng;
            }

            if (isset($post->total_area)) {
                $block->total_area = trim(strip_tags($post->total_area));
            }

            if (isset($post->green_area)) {
                $block->green_area = trim(strip_tags($post->green_area));
            }

            if (isset($post->floor_count)) {
                $block->floor_count = $post->floor_count;
            }

            if (isset($post->apartment_count)) {
                $block->apartment_count = $post->apartment_count;
            }

            if (isset($post->price)) {
                $block->price = trim(strip_tags($post->price));
            }

            if (isset($post->price_eng)) {
                $block->price_eng = trim(strip_tags($post->price_eng));
            }
            
            if (isset($post->meta_title)) {
                $block->meta_title = trim(strip_tags($post->meta_title));
            }
            
            if (isset($post->meta_title_eng)) {
                $block->meta_title_eng = trim(strip_tags($post->meta_title_eng));
            }
            
            if (isset($post->meta_keywords)) {
                $block->meta_keywords = trim(strip_tags($post->meta_keywords));
            }
            
            if (isset($post->meta_keywords_eng)) {
                $block->meta_keywords_eng = trim(strip_tags($post->meta_keywords_eng));
            }
            
            if (isset($post->meta_description)) {
                $block->meta_description = trim(strip_tags($post->meta_description));
            }
            
            if (isset($post->meta_description_eng)) {
                $block->meta_description_eng = trim(strip_tags($post->meta_description_eng));
            }
            

            $block->updated_at = date('Y-m-d H:i:s');

            try {
                if (!$block->save()) {
                    $messages = $block->getMessages();

                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể cập nhật.'
                        );
                    }
                    goto RETURN_RESPONSE;
                }

                if (isset($post->attribute_type)) {
                    parent::setAttrBlock($post->attribute_type, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE);
                }
                if (isset($post->attribute_view)) {
                    parent::setAttrBlock($post->attribute_view, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW);
                }

                if (isset($post->attribute_utility)) {
                    parent::setAttrBlock($post->attribute_utility, $block, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY);
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$block->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[BlockController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }
        }

        $attributeTypeBlock = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $block->id,  $cache);
        $attributeViewBlock = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $block->id, $cache);
        $attributeUtilityBlock = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $block->id, $cache);
        
        $attributeType = [];
        $attributeTypeEng = [];
        $attributeView = [];
        $attributeViewEng = [];
        $attributeUtility = [];
        $attributeUtilityEng = [];
        
        foreach ($attributeTypeBlock as $key => $attribute) {
            $attributeType[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name']
            ];
            $attributeTypeEng[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name_eng']
            ];
        }
        
        foreach ($attributeViewBlock as $key => $attribute) {
            $attributeView[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name']
            ];
            $attributeViewEng[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name_eng']
            ];
        }
        
        foreach ($attributeUtilityBlock as $key => $attribute) {
            $attributeUtility[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name']
            ];
            $attributeUtilityEng[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name_eng']
            ];
        }
        
        $project = $block->getProject();
        
        $defaultImageUrl = parent::$noImageUrl;
        if ($block->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $block->default_image;
        }

        $galleryUrl = array();
        $gallery = json_decode($block->gallery, true);
        if (count($gallery)) {
            foreach ($gallery as $g) {
                if ($g != '') {
                    $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                }
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array(
                'id' => (int)$block->id,
                'name' => $block->name,
                'name_eng' => $block->name_eng,
                'slug' => $block->slug,
                'slug_eng' => $block->slug_eng,
                'shortname' => $block->shortname,
                'floor_name_list' => $block->floor_name_list,
                'apartment_name_list' => $block->apartment_name_list,
                'default_image' => $block->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery' => $block->gallery,
                'gallery_url' => $galleryUrl,
                'floor_count' => (int)$block->floor_count,
                'apartment_count' => (int)$block->apartment_count,
                'direction' => (int)$block->direction,
                'direction_text' => isset($direction[$block->direction]) ? $direction[$block->direction] : '',
                'attribute' => [
                    'type' => $attributeType,
                    'type_eng' => $attributeTypeEng,
                    'view' => $attributeView,
                    'view_eng' => $attributeViewEng,
                    'utility' => $attributeUtility,
                    'utility_eng' => $attributeUtilityEng
                ],
                'green_area' => $block->green_area,
                'total_area' => $block->total_area,
                'policy' => $block->policy,
                'policy_eng' => $block->policy_eng,
                'price' => $block->price,
                'price_eng' => $block->price_eng,
                'description' => $block->description,
                'description_eng' => $block->description_eng,
                'status' => (int)$block->status,
                'project' => [
                    'id' => (int)$project->id,
                    'name' => $project->name,
                    'name_eng' => $project->name_eng
                ],
                'created_by' => (int)$block->created_by,
                'created_at' => $block->created_at,
                'updated_at' => $block->updated_at,
                'meta_title' => $block->meta_title,
                'meta_title_eng' => $block->meta_title_eng,
                'meta_keywords' => $block->meta_keywords,
                'meta_keywords_eng' => $block->meta_keywords_eng,
                'meta_description' => $block->meta_description,
                'meta_description_eng' => $block->meta_description_eng
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function fullAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'lower'), \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER);
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $cacheName = md5(serialize(array(
            'BlockController',
            'fullAction',
            'BlockModel',
            'findFirst',
            $id,
            $type
        )));

        $block = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$block) {
            switch ($type) {
                case \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_AGENT:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR:
                    $query = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $id)
                    );
                break;

                default:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
            }

            if ($id != '') {
                $block = \ITECH\Data\Model\BlockModel::findFirst($query);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $block);
            }
        }

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại block này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $block->view_count = $block->view_count + 1;
            $block->save();
        }

        /*
        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $block->project_id,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        ));
        if (!$project) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại dự án của block này.'
            );
            goto RETURN_RESPONSE;
        }
        */

        $params = array('conditions' => array('id' => $block->project_id));

        $projectRepo = new \ITECH\Data\Repo\ProjectRepo();
        $project = $projectRepo->getDetail($params);
        $project = $project[0];

        if (!$project) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại dự án của block này.'
            );
            goto RETURN_RESPONSE;
        }

        $apartment = \ITECH\Data\Model\ApartmentModel::find(array(
            'columns' => 'id, name, slug, floor, ordering, condition, status',
            'conditions' => 'block_id = :block_id: AND status = :status:',
            'bind' => array(
                'block_id' => $block->id,
                'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
            )
        ));

        $arrApartment = array();
        $arrApartmentNoneKey = array();
        if (count($apartment->toArray()) > 0) {
            $conditions = \ITECH\Data\Lib\Constant::getApartmentCondition();

            foreach ($apartment->toArray() as $item) {
                $array = array(
                    'id' => (int)$item['id'],
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'floor_count' => (int)$item['floor'],
                    'ordering' => (int)$item['ordering'],
                    'condition' => (int)$item['condition'],
                    'condition_text' => isset($conditions[$item['condition']]) ? $conditions[$item['condition']] : ''
                );

                $arrApartment[$item['floor']][$item['id'] . '_' . $item['ordering']] = $array;
                $arrApartmentNoneKey[] = $array;
            }

            for ($i = 1; $i <= $block->floor_count; $i++) {
                if (isset($arrApartment[$i])) {
                    $numberApartment[$i] = count($arrApartment[$i]);
                } else {
                    $numberApartment[$i] = 0;
                }
            }
        } else {
            $arrApartment = [];
            $numberApartment = [];
        }

        $attributeType = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $block->id, 1, $cache);
        $attributeView = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $block->id, 1, $cache);
        $attributeUtility = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $block->id, 1, $cache);

        $attributeTypeEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE, $block->id, 2, $cache);
        $attributeViewEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $block->id, 2, $cache);
        $attributeUtilityEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY, $block->id, 2, $cache);

        $apartmentAvailableCount = parent::getCountApartmentByBlock($block, \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE);
        $apartmentProcessingCount = parent::getCountApartmentByBlock($block, \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD);
        $apartmentSoldCount = parent::getCountApartmentByBlock($block, \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD);

        $defaultImageUrl = parent::$noImageUrl;
        if ($block->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $block->default_image;
        }

        $galleryUrl = array();
        $gallery = json_decode($block->gallery, true);
        if (count($gallery)) {
            foreach ($gallery as $g) {
                if ($g != '') {
                    $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                }
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array(
                'id' => (int)$block->id,
                'name' => $block->name,
                'name_eng' => $block->name_eng,
                'slug' => $block->slug,
                'slug_eng' => $block->slug_eng,
                'shortname' => $block->shortname,
                'floor_name_list' => $block->floor_name_list,
                'apartment_name_list' => $block->apartment_name_list,
                'price' => $block->price,
                'price_eng' => $block->price_eng,
                'province' => [
                    'id' => (int)$project->province_id,
                    'name' => $project->province_name
                ],
                'default_image' => $block->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery' => $block->gallery,
                'gallery_url' => $galleryUrl,
                'floor_count' => (int)$block->floor_count,
                'apartment_count' => (int)$block->apartment_count,
                'apartment_all_count' => (int)($block->apartment_count * $block->floor_count),
                'apartment_available_count' => (int)$apartmentAvailableCount,
                'apartment_processing_count' => (int)$apartmentProcessingCount,
                'apartment_sold_count' => (int)$apartmentSoldCount,
                'direction' => (int)$block->direction,
                'direction_text' => isset($direction[$block->direction]) ? $direction[$block->direction] : '',
                'attribute' => [
                    'view' => $attributeView,
                    'view_eng' => $attributeViewEng,
                    'type' => $attributeType,
                    'type_eng' => $attributeTypeEng,
                    'utility' => $attributeUtility,
                    'utility_eng' => $attributeUtilityEng
                ],
                'total_area' => $block->total_area,
                'green_area' => $block->green_area,
                'policy' => $block->policy,
                'policy_eng' => $block->policy_eng,
                'description' => $block->description,
                'description_eng' => $block->description_eng,
                'status' => (int)$block->status,
                'created_by' => (int)$block->created_by,
                'updated_by' => (int)$block->updated_by,
                'approved_by' => (int)$block->approved_by,
                'project' => [
                    'id' => (int)$project->id,
                    'name' => $project->name,
                    'name_eng' => $project->name_eng,
                    'slug' => $project->slug,
                    'slug_eng' => $project->slug_eng,
                    'address' => $project->address
                ],
                'apartment' => $arrApartment,
                'apartment_string' => json_encode($arrApartment),
                'apartment_array' => $arrApartmentNoneKey,
                'max_apart_in_floor' => count($numberApartment) > 0 ? max($numberApartment) : 0
            )
        );

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function fullAjaxAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'lower'), \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER);
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'BlockModel',
            'findFirst',
            $id,
            $type
        )));

        $block = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$block) {
            switch ($type) {
                case \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_AGENT:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR:
                    $query = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $id)
                    );
                break;

                default:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                        )
                    );
            }

            if ($id != '') {
                $block = \ITECH\Data\Model\BlockModel::findFirst($query);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $block);
            }
        }

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại block này.'
            );
            goto RETURN_RESPONSE;
        }

        $params = array(
            'conditions' => array(
                'id' => $block->project_id
            )
        );

        $projectRepo = new \ITECH\Data\Repo\ProjectRepo();
        $project = $projectRepo->getDetail($params);
        $project = $project[0];

        if (!$project) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại dự án của block này.'
            );
            goto RETURN_RESPONSE;
        }

        $attributeView = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $block->id, 1, $cache);
        $attributeViewEng = parent::getAttrBlock(\ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW, $block->id, 2, $cache);

        $apartmentAvailableCount = parent::getCountApartmentByBlock($block, \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE);
        $apartmentProcessingCount = parent::getCountApartmentByBlock($block, \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD);
        $apartmentSoldCount = parent::getCountApartmentByBlock($block, \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD);

        $defaultImageUrl = parent::$noImageUrl;
        if ($block->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $block->default_image;
        }

        $galleryUrl = array();
        $gallery = json_decode($block->gallery, true);
        if (count($gallery)) {
            foreach ($gallery as $g) {
                if ($g != '') {
                    $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                }
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array(
                'id' => (int)$block->id,
                'name' => $block->name,
                'name_eng' => $block->name_eng,
                'slug' => $block->slug,
                'slug_eng' => $block->slug_eng,
                'price' => $block->price,
                'price_eng' => $block->price_eng,
                'default_image' => $block->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery' => $block->gallery,
                'gallery_url' => $galleryUrl,
                'floor_count' => (int)$block->floor_count,
                'apartment_count' => (int)$block->apartment_count,
                'apartment_all_count' => (int)($block->apartment_count * $block->floor_count),
                'direction' => $block->direction,
                'direction_text' => isset($direction[$block->direction]) ? $direction[$block->direction] : '',
                'attribute' => [
                    'view' => $attributeView,
                    'view_eng' => $attributeViewEng
                ],
                'total_area' => $block->total_area,
                'green_area' => $block->green_area,
                'status' => (int)$block->status,
                'project' => [
                    'id' => (int)$project->id,
                    'name' => $project->name,
                    'name_eng' => $project->name_eng
                ],
                'apartment_available_count' => $apartmentAvailableCount,
                'apartment_processing_count' => $apartmentProcessingCount,
                'apartment_sold_count' => $apartmentSoldCount
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}
