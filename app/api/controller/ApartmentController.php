<?php
namespace ITECH\Api\Controller;

class ApartmentController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
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
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $userId = $this->request->getQuery('user_id', array('striptags', 'trim', 'int'), '');
        $blockId = $this->request->getQuery('block_id', array('striptags', 'trim', 'int'), '');

        $notId = $this->request->getQuery('not_id', array('int'), '');
        $price = $this->request->getQuery('price', array('int'), '');
        $priceMin = $this->request->getQuery('price_min', array('int'), '');
        $priceMax = $this->request->getQuery('price_max', array('int'), '');
        $direction = $this->request->getQuery('direction');
        $type = $this->request->getQuery('type', array('int'), '');
        $floor = $this->request->getQuery('floor', array('int'), '');

        $direction = $this->request->getQuery('trends');
        $attributes = $this->request->getQuery('attributes');
        //$filter = $this->request->getQuery('filter');

        $params = array(
            'conditions' => array(),
            'order' => 'a1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        if ($q != '') {
            $params['conditions']['q'] = $q;
        }

        if ($notId != '') {
            $params['conditions']['not_id'] = $notId;
        }

        if ($price != '') {
            $params['conditions']['price'] = $price;
        }

        if ($priceMin != '') {
            $params['conditions']['price_min'] = $priceMin;
        }

        if ($priceMax != '' && $priceMax > 0 ) {
            $params['conditions']['price_max'] = $priceMax;
        }

        if ($direction != '') {
            $params['conditions']['direction'] = $direction;
        }

        if ($type != '') {
            $params['conditions']['type'] = $type;
        }

        if ($status != '') {
            $params['conditions']['status'] = $status;
        }

        if ($blockId != '') {
            $params['conditions']['block_id'] = $blockId;
        }

        if ($floor != '') {
            $params['conditions']['floor'] = $floor;
        }

        if ($sortField != '' && $sortBy != '') {
            if (!in_array($sortBy, array('ASC', 'DESC'))) {
                $sortBy = 'DESC';
            }

            switch ($sortField) {
                case 'updated_at':
                    $params['order'] = 'a1.updated_at ' . $sortField;
                break;

                case 'id':
                    $params['order'] = 'a1.id ' . $sortBy;
                break;
            }
        }

        $attributesId = array();
        $directionId = array();

        if (count($attributes)) {
            foreach ($attributes as $item) {
                $attributesId[] = $item;
            }

            if (count($attributesId)) {
                $params['conditions']['attributes_id'] = $attributesId;
            }
        }

        if (count($direction)) {
            foreach ($direction as $item) {
                $directionId[] = $item;
            }

            if (count($directionId)) {
                $params['conditions']['directions_id'] = $directionId;
            }
        }

        $cacheName = md5(serialize(array(
            'ApartmentController',
            'listAction',
            'ApartmentRepo',
            'getPaginationList',
            $params
        )));

        $apartments = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartments) {
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();

            if (isset($params['conditions']['attributes_id'])) {
                $params['order'] = 'id ASC';

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

                        case 'price':
                            $params['order'] = 'price ' . $sortBy;
                        break;
                    }
                }

                $apartments = $apartmentRepo->getPaginationListByAttribute($params);
            } else {
                $apartments = $apartmentRepo->getPaginationList($params);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $apartments);
            }
        }

        foreach ($apartments->items as $item) {
            if ($userId != '') {
                $cacheName = md5(serialize(array(
                    'ApartmentController',
                    'listAction',
                    'UserSaveModel',
                    'count',
                    $userId,
                    $item->id
                )));

                $saveHome = $this->cache->get($cacheName);
                if (!$saveHome) {
                    $saveHome = \ITECH\Data\Model\UserSaveModel::count(array(
                        'conditions' => 'user_id = :user_id: AND value = :value: AND key = :key:',
                        'bind' => array(
                            'user_id' => $userId,
                            'value' => $item->id,
                            'key' => \ITECH\Data\Lib\Constant::USER_SAVE_HOME
                        )
                    ));

                    $this->cache->save($cacheName, $saveHome);
                }

                if ($saveHome > 0) {
                    $item->save_home = 'true';
                } else {
                    $item->save_home = 'false';
                }
            } else {
                $item->save_home = 'false';
            }

            $attributes = $this->getAllAttributeApartment($item);
            $item->attributes = $attributes;

            $response['result'][] = $this->buildItemApartment($item);
        }

        $response['total_items'] = (int)$apartments->total_items;
        $response['total_pages'] = isset($apartments->total_pages) ? (int)$apartments->total_pages : ceil($apartments->total_items / $limit);

        RETURN_RESPONSE:
            parent::outputJSON($response);

    }

    public function listByBlockAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $blockId = $this->request->getQuery('block_id', array('int'), '');
        $floorCount = $this->request->getQuery('floor_count', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $userId = $this->request->getQuery('user_id', array('striptags', 'trim', 'int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);

        $params = array(
            'conditions' => array(),
            'order' => 'a1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        $cacheName = md5(serialize(array(
            'ApartmentController',
            'listByBlockAction',
            'BlockModel',
            'findFirst',
            $blockId
        )));

        $block = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$block) {
            $block = \ITECH\Data\Model\BlockModel::findFirst(array(
                'conditions' => 'id = :id: AND status = :status:',
                'bind' => array(
                    'id' => $blockId,
                    'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                )
            ));

            if ($cache == 'true') {
                $this->cache->save($cacheName, $block);
            }
        }

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không tồn tại Block này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $cacheName = md5(serialize(array(
            'ApartmentController',
            'listByBlockAction',
            'ApartmentModel',
            'find',
            $blockId,
            $floorCount
        )));

        $apartments = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartments) {

            if ($blockId != '') {
                $params['conditions']['block_id'] = $blockId;
            }

            if ($floorCount != '') {
                $params['conditions']['floor_count'] = $floorCount;
            }

            $params['conditions']['status'] = \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE;

            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
            $apartments = $apartmentRepo->getPaginationList($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $apartments);
            }
        }


        foreach ($apartments->items as $item) {
            if ($userId != '') {
                $cacheName = md5(serialize(array(
                    'ApartmentController',
                    'listAction',
                    'UserSaveModel',
                    'count',
                    $userId,
                    $item->id
                )));

                $saveHome = $this->cache->get($cacheName);
                if (!$saveHome) {
                    $saveHome = \ITECH\Data\Model\UserSaveModel::count(array(
                        'conditions' => 'user_id = :user_id: AND value = :value: AND key = :key:',
                        'bind' => array(
                            'user_id' => $userId,
                            'value' => $item->id,
                            'key' => \ITECH\Data\Lib\Constant::USER_SAVE_HOME
                        )
                    ));

                    $this->cache->save($cacheName, $saveHome);
                }

                if ($saveHome > 0) {
                    $item->save_home = 'true';
                } else {
                    $item->save_home = 'false';
                }
            } else {
                $item->save_home = 'false';
            }

            $attributes = $this->getAllAttributeApartment($item);
            $item->attributes = $attributes;
            $response['result'][] = $this->buildItemApartment($item);
        }

        $response['total_items'] = (int)$apartments->total_items;
        $response['total_pages'] = isset($apartments->total_pages) ? (int)$apartments->total_pages : ceil($apartments->total_items / $limit);

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function addAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Error.',
            'result' => array()
        );

        $blockId = $this->request->getQuery('block_id', array('int'), '');
        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $blockId
            )
        ));

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không tồn tại Block của sản phẩm này.',
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
                'message' => 'Yêu cầu nhập tên sản phẩm.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));

            $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên sản phẩm (Tiếng Anh).'
            )));
            $validator->setFilters('name_eng', array('striptags', 'trim'));

            $validator->setFilters('block_id', array('striptags', 'trim', 'int'));
            $validator->setFilters('condition', array('striptags', 'trim', 'int'));
            $validator->setFilters('type', array('striptags', 'trim', 'int'));
            $validator->setFilters('price', array('striptags', 'trim', 'int'));
            $validator->setFilters('price_eng', array('striptags', 'trim', 'int'));
            $validator->setFilters('price_sale_off', array('striptags', 'trim', 'int'));
            $validator->setFilters('price_sale_off_eng', array('striptags', 'trim', 'int'));
            $validator->setFilters('type', array('striptags', 'trim', 'int'));
            $validator->setFilters('floor_count', array('int'));
            $validator->setFilters('room_count', array('int'));
            $validator->setFilters('bedroom_count', array('int'));
            $validator->setFilters('bathroom_count', array('int'));
            $validator->setFilters('ordering', array('int'));
            $validator->setFilters('position', array('striptags', 'trim'));
            $validator->setFilters('position_eng', array('striptags', 'trim'));
            $validator->setFilters('panorama_view', array('striptags', 'trim'));
            $validator->setFilters('direction', array('striptags', 'trim'));
            $validator->setFilters('description', array('striptags', 'trim'));
            $validator->setFilters('description_eng', array('striptags', 'trim'));
            $validator->setFilters('area', array('striptags', 'trim'));
            $validator->setFilters('space', array('striptags', 'trim'));
            $validator->setFilters('adults', array('int'));
            $validator->setFilters('children', array('int'));
            $validator->setFilters('rose', array('int'));
            $validator->setFilters('status', array('striptags', 'trim'));
            $validator->setFilters('furniture_name', array('striptags', 'trim'));
            $validator->setFilters('furniture_name_eng', array('striptags', 'trim'));
            $validator->setFilters('furniture_address', array('striptags', 'trim'));
            $validator->setFilters('furniture_address_eng', array('striptags', 'trim'));
            $validator->setFilters('furniture_email', array('striptags', 'trim'));
            $validator->setFilters('furniture_logo', array('striptags', 'trim'));
            $validator->setFilters('furniture_note', array('striptags', 'trim'));
            $validator->setFilters('furniture_note_eng', array('striptags', 'trim'));
            $validator->setFilters('created_by', array('int'));
            $validator->setFilters('updated_by', array('int'));
            $validator->setFilters('approved_by', array('int'));

            $userId = $post->user_id;
            if ($userId > 0) {
                $user = \ITECH\Data\Model\UserModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $userId
                    )
                ));
                if (!$user) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không tồn tại User này. ID: ' . $userId,
                        'result' => array()
                    );
                    goto RETURN_RESPONSE;
                }
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

            $apartment = new \ITECH\Data\Model\ApartmentModel();

            $name = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name'));
            $otherApartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                'conditions' => 'block_id = :block_id:
                    AND name = :name:
                    AND status = :status:',
                'bind' => array(
                    'block_id' => $blockId,
                    'name' => $name,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));

            if ($otherApartment) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Đã tồn tại tên sản phẩm này.'
                );
                goto RETURN_RESPONSE;
            }

            $nameEng = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name_eng'));
            $otherApartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                'conditions' => 'block_id = :block_id:
                    AND name_eng = :name_eng:
                    AND status = :status:',
                'bind' => array(
                    'block_id' => $blockId,
                    'name_eng' => $nameEng,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));

            if ($otherApartment) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Đã tồn tại tên sản phẩm này (Tiếng Anh).'
                );
                goto RETURN_RESPONSE;
            }

            $type = $validator->getValue('type') ? $validator->getValue('type') : \ITECH\Data\Lib\Constant::APARTMENT_TYPE_BUY;

            $apartment->user_id = $userId;
            $apartment->name = $name;
            $apartment->name_eng = $nameEng;

            $apartment->slug = \ITECH\Data\Lib\Util::slug($apartment->name);
            $apartment->slug_eng = \ITECH\Data\Lib\Util::slug($apartment->name_eng);
            $apartment->block_id = $block->id;
            $apartment->condition = $validator->getValue('condition');
            $apartment->type = $type;
            $apartment->price = $validator->getValue('price');
            $apartment->price_eng = $validator->getValue('price_eng');
            $apartment->price_sale_off = $validator->getValue('price_sale_off');
            $apartment->price_sale_off_eng = $validator->getValue('price_sale_off_eng');
            $apartment->position = $validator->getValue('position');
            $apartment->panorama_view = $validator->getValue('panorama_view');
            $apartment->trend = $validator->getValue('direction');
            $apartment->default_image = $validator->getValue('default_image');
            $apartment->floor_count = $validator->getValue('floor_count');
            $apartment->room_count = $validator->getValue('room_count');
            $apartment->bedroom_count = $validator->getValue('bedroom_count');
            $apartment->bathroom_count = $validator->getValue('bathroom_count');
            $apartment->ordering = $validator->getValue('ordering');
            $apartment->description = $validator->getValue('description');
            $apartment->description_eng = $validator->getValue('description_eng');
            $apartment->furniture_name = $validator->getValue('furniture_name');
            $apartment->furniture_name_eng = $validator->getValue('furniture_name_eng');
            $apartment->furniture_address = $validator->getValue('furniture_address');
            $apartment->furniture_address_eng = $validator->getValue('furniture_address_eng');
            $apartment->furniture_email = $validator->getValue('furniture_email');
            $apartment->furniture_logo = $validator->getValue('furniture_logo');
            $apartment->furniture_note = $validator->getValue('furniture_note');
            $apartment->furniture_note_eng = $validator->getValue('furniture_note_eng');
            $apartment->view_count = 0;
            $apartment->area = $validator->getValue('area');
            $apartment->space = $validator->getValue('space');
            $apartment->rose = $validator->getValue('rose');
            $apartment->adults = $validator->getValue('adults');
            $apartment->children = $validator->getValue('children');
            $apartment->status = $validator->getValue('status');
            $apartment->created_by = $validator->getValue('created_by');
            $apartment->updated_by = $validator->getValue('updated_by');
            $apartment->approved_by = $validator->getValue('approved_by');
            $apartment->created_at = date('Y-m-d H:i:s');
            $apartment->updated_at = date('Y-m-d H:i:s');

            if (isset($post->gallery)) {
                $apartment->gallery = $post->gallery;
            }

            try {
                if (!$apartment->create()) {
                    $messages = $apartment->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo sản phẩm.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                $block->apartment_count = $block->apartment_count + 1;
                if (!$block->update()) {
                    $messages = $block->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $this->logger->log('[ApartmentController][addAction]: ' . $error_message, \Phalcon\Logger::ERROR);
                    } else {
                        $this->logger->log('[ApartmentController][addAction]: Lỗi, không thể cập nhật Block', \Phalcon\Logger::ERROR);
                    }
                }

                if (isset($post->property_type) && $post->property_type != '') {
                    $propertyTypes = explode(',', $post->property_type);
                    if (count($propertyTypes)) {
                        foreach($propertyTypes as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->property_type_eng) && $post->property_type_eng != '') {
                    $propertyTypes = explode(',', $post->property_type_eng);
                    if (count($propertyTypes)) {
                        foreach ($propertyTypes as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                        'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                        'bind' => array(
                                            'slug' => \ITECH\Data\Lib\Util::slug($item),
                                            'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                            'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                        )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->property_view) && $post->property_view != '') {
                    $propertyViews = array_filter(explode(',', $post->property_view));
                    if (count($propertyViews)) {
                        foreach($propertyViews as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->property_view_eng) && $post->property_view_eng != '') {
                    $propertyViews = array_filter(explode(',', $post->property_view_eng));
                    if (count($propertyViews)) {
                        foreach($propertyViews as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->property_utility) && $post->property_utility != '') {
                    $propertyUtilities = array_filter(explode(',', $post->property_utility));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->property_utility_eng) && $post->property_utility_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->property_utility_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->entertaining_control_system) && $post->entertaining_control_system != '') {
                    $propertyUtilities = array_filter(explode(',', $post->entertaining_control_system));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENTERTAINING_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENTERTAINING_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->entertaining_control_system_eng) && $post->entertaining_control_system_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->entertaining_control_system_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENTERTAINING_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENTERTAINING_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->security_control_system) && $post->security_control_system != '') {
                    $propertyUtilities = array_filter(explode(',', $post->security_control_system));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SECURITY_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SECURITY_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->security_control_system_eng) && $post->security_control_system_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->security_control_system_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SECURITY_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SECURITY_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->environment_control_system) && $post->environment_control_system != '') {
                    $propertyUtilities = array_filter(explode(',', $post->environment_control_system));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENVIRONMENT_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENVIRONMENT_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->environment_control_system_eng) && $post->environment_control_system_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->environment_control_system_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENVIRONMENT_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENVIRONMENT_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->energy_control_system) && $post->energy_control_system != '') {
                    $propertyUtilities = array_filter(explode(',', $post->energy_control_system));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENERGY_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENERGY_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->energy_control_system_eng) && $post->energy_control_system_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->energy_control_system_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENERGY_CONTROL_SYSTEM,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ENERGY_CONTROL_SYSTEM;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->room_type) && $post->room_type != '') {
                    $propertyUtilities = array_filter(explode(',', $post->room_type));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->room_type_eng) && $post->room_type_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->room_type_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->best_for) && $post->best_for != '') {
                    $propertyUtilities = array_filter(explode(',', $post->best_for));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->best_for_eng) && $post->best_for_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->best_for_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH

                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->suitable_for) && $post->suitable_for != '') {
                    $propertyUtilities = array_filter(explode(',', $post->suitable_for));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                if (isset($post->suitable_for_eng) && $post->suitable_for_eng != '') {
                    $propertyUtilities = array_filter(explode(',', $post->suitable_for_eng));
                    if (count($propertyUtilities)) {
                        foreach($propertyUtilities as $item) {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'slug = :slug: AND type = :type: AND module = :module: AND language = :language:',
                                'bind' => array(
                                    'slug' => \ITECH\Data\Lib\Util::slug($item),
                                    'type' => \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR,
                                    'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                                    'language' => \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH
                                )
                            ));

                            if ($attribute) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            } else {
                                $attribute = new \ITECH\Data\Model\AttributeModel();
                                $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                                $attribute->type = \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR;
                                $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                                $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                                $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
                                $attribute->language = \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH;
                                $attribute->create();

                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $attribute->id;
                                $apartmentAttribute->create();
                            }
                        }
                    }
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$apartment->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[ApartmentController][addAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $cacheName = md5(serialize(array(
            'ApartmentController',
            'detailAction',
            'ApartmentModel',
            'findFirst',
            $id,
            $type
        )));

        $apartment = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartment) {
            switch ($type) {
                case \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                        )
                    );
                break;

                case \ITECH\Data\Lib\Constant::USER_TYPE_AGENT:
                    $query = array(
                        'conditions' => 'id = :id: AND status = :status:',
                        'bind' => array(
                            'id' => $id,
                            'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
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
                            'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                        )
                    );
                break;
            }

            if ($id != '') {
                $apartment = \ITECH\Data\Model\ApartmentModel::findFirst($query);
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $apartment);
            }
        }

        if (!$apartment) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại sản phẩm này.'
            );
            goto RETURN_RESPONSE;
        }

        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $apartment->block_id,
                'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
            )
        ));

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại Block sản phẩm này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $apartment->view_count = $apartment->view_count + 1;
            $apartment->save();
        }

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();
            if (isset($post->name)) {
                $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập tên sản phẩm.'
                )));
                $validator->setFilters('name', array('striptags', 'trim'));
            }

            if (isset($post->name_eng)) {
                $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập tên sản phẩm (Tiếng Anh).'
                )));
                $validator->setFilters('name_eng', array('striptags', 'trim'));
            }

            if (isset($post->rose)) {
                $validator->setFilters('rose', array('striptags', 'trim', 'int'));
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

                $otherApartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                    'conditions' => 'id <> :id:
                        AND block_id = :block_id:
                        AND name = :name:
                        AND status = :status:',
                    'bind' => array(
                        'id' => $apartment->id,
                        'block_id' => $apartment->block_id,
                        'name' => $name,
                        'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                    )
                ));

                if ($otherApartment) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên căn hộ này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $apartment->name = $name;
                $apartment->slug = \ITECH\Data\Lib\Util::slug($apartment->name);
            }

            if ($validator->getValue('name_eng')) {
                $nameEng = \ITECH\Data\Lib\Util::removeJunkSpace($validator->getValue('name_eng'));

                $otherApartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                    'conditions' => 'id <> :id:
                        AND block_id = :block_id:
                        AND name_eng = :name_eng:
                        AND status = :status:',
                    'bind' => array(
                        'id' => $apartment->id,
                        'block_id' => $apartment->block_id,
                        'name_eng' => $nameEng,
                        'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                    )
                ));

                if ($otherApartment) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Đã tồn tại tên căn hộ này (Tiếng Anh).'
                    );
                    goto RETURN_RESPONSE;
                }

                $apartment->name_eng = $nameEng;
                $apartment->slug_eng = \ITECH\Data\Lib\Util::slug($apartment->name_eng);
            }

            if ($validator->getValue('rose')) {
                $apartment->rose = $validator->getValue('rose');
            }

            if ($validator->getValue('adults_count')) {
                $apartment->adults_count = $validator->getValue('adults_count');
            }

            if ($validator->getValue('children_count')) {
                $apartment->children_count = $validator->getValue('children_count');
            }

            if (isset($post->user_id)) {
                $apartment->user_id = $post->user_id;
            }

            if (isset($post->block_id)) {
                $apartment->block_id = $post->block_id;
            }

            if (isset($post->gallery)) {
                $apartment->gallery = $post->gallery;
            }

            if (isset($post->type)) {
                $apartment->type = $post->type;
            }

            if (isset($post->price)) {
                $apartment->price = $post->price;
            }

            if (isset($post->price_eng)) {
                $apartment->price_eng = $post->price_eng;
            }

            if (isset($post->price_sale_off)) {
                $apartment->price_sale_off = $post->price_sale_off;
            }

            if (isset($post->price_sale_off_eng)) {
                $apartment->price_sale_off_eng = $post->price_sale_off_eng;
            }

            if (isset($post->position)) {
                $apartment->position = $post->position;
            }

            if (isset($post->default_image)) {
                $apartment->default_image = $post->default_image;
            }

            if (isset($post->panorama_view)) {
                $apartment->panorama_view = $post->panorama_view;
            }

            if (isset($post->description)) {
                $apartment->description = trim(strip_tags($post->description));
            }

            if (isset($post->description_eng)) {
                $apartment->description_eng = trim(strip_tags($post->description_eng));
            }

            if (isset($post->direction)) {
                $apartment->direction = $post->direction;
            }

            if (isset($post->status)) {
                $apartment->status = $post->status;
            }

            if (isset($post->total_area)) {
                $apartment->total_area = $post->total_area;
            }

            if (isset($post->green_area)) {
                $apartment->green_area = $post->green_area;
            }

            if (isset($post->floor)) {
                $apartment->floor = (int)$post->floor;
            }

            if (isset($post->room_count)) {
                $apartment->room_count = (int)$post->room_count;
            }

            if (isset($post->bedroom_count)) {
                $apartment->bedroom_count = (int)$post->bedroom_count;
            }

            if (isset($post->bathroom_count)) {
                $apartment->bathroom_count = (int)$post->bathroom_count;
            }

            if (isset($post->ordering)) {
                $apartment->ordering = $post->ordering;
            }

            $apartment->updated_at = date('Y-m-d H:i:s');

            try {
                if (!$apartment->update()) {
                    $messages = $apartment->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể cập nhật sản phẩm.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                if (isset($post->attribute_type)) {
                    if ($post->attribute_type != '') {
                        parent::setAttrApartment($post->attribute_type, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_type_eng)) {
                    if ($post->attribute_type_eng != '') {
                       parent::setAttrApartment($post->attribute_type_eng, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE, \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS aa1
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_view)) {
                    if ($post->attribute_view != '') {
                        parent::setAttrApartment($post->attribute_view, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_view_eng)) {
                    if ($post->attribute_view_eng != '') {
                        parent::setAttrApartment($post->attribute_view_eng, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW, \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_utility)) {
                    if ($post->attribute_utility != '') {
                        parent::setAttrApartment($post->attribute_utility, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_utility_eng)) {
                    if ($post->attribute_utility_eng != '') {
                        parent::setAttrApartment($post->attribute_utility_eng, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY, \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }



                if (isset($post->attribute_best_for)) {
                    if ($post->attribute_best_for != '') {
                        parent::setAttrApartment($post->attribute_best_for, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_best_for_eng)) {
                    if ($post->attribute_best_for_eng != '') {
                        parent::setAttrApartment($post->attribute_best_for_eng, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR, \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_suitable_for)) {
                    if ($post->attribute_suitable_for != '') {
                        parent::setAttrApartment($post->attribute_suitable_for, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_VIETNAMESE . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                if (isset($post->attribute_suitable_for_eng)) {
                    if ($post->attribute_suitable_for_eng != '') {
                        parent::setAttrApartment($post->attribute_suitable_for_eng, $apartment, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR, \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH);
                    } else {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();

                        $q = 'DELETE `aa1`
                            FROM `land_apartment_attribute` AS `aa1`
                            INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `aa1`.`attribute_id`
                            WHERE
                                `aa1`.`apartment_id` = "' . $apartment->id . '"
                                AND `a1`.`type` = "' . \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR . '"
                                AND `a1`.`language` = "' . \ITECH\Data\Lib\Constant::ATTRIBUTE_LANGUAGE_ENGLISH . '"';
                        $apartmentAttribute->getWriteConnection()->query($q);
                    }
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array('id' => (int)$apartment->id)
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[ApartmentController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }
        }

        $defaultImageUrl = parent::$noImageUrl;
        if ($apartment->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $apartment->default_image;
        }

        $galleryUrl = array();
        $gallery = json_decode($apartment->gallery, true);
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
                'id' => (int)$apartment->id,
                'user_id' => (int)$apartment->user_id,
                'name' => $apartment->name,
                'name_eng' => $apartment->name_eng,
                'slug' => $apartment->slug,
                'slug_eng' => $apartment->slug_eng,
                'default_image' => $apartment->default_image,
                'default_image_url' => $defaultImageUrl,
                'gallery' => $apartment->gallery,
                'gallery_url' => $galleryUrl,
                'block_id' => (int)$apartment->block_id,
                'condition' => (int)$apartment->condition,
                'type' => (int)$apartment->type,
                'price' => (int)$apartment->price,
                'price_eng' => (int)$apartment->price_eng,
                'price_sale_off' => (int)$apartment->price_sale_off,
                'price_sale_off_eng' => (int)$apartment->price_sale_off_eng,
                'position' => $apartment->position,
                'attribute' => [],
                'direction' => (int)$apartment->direction,
                'direction_text' => isset($direction[$apartment->direction]) ? $direction[$apartment->direction] : '',
                'total_area' => $apartment->total_area,
                'green_area' => $apartment->green_area,
                'rose' => (int)$apartment->rose,
                'view_count' => (int)$apartment->view_count,
                'description' => $apartment->description,
                'description_eng' => $apartment->description_eng,
                'floor' => (int)$apartment->floor,
                'room_count' => (int)$apartment->room_count,
                'bedroom_count' => (int)$apartment->bedroom_count,
                'bathroom_count' => (int)$apartment->bathroom_count,
                'adults_count' => (int)$apartment->adults_count,
                'children_count' => (int)$apartment->children_count,
                'ordering' => (int)$apartment->ordering,
                'status' => (int)$apartment->status,
                'panorama_image' => $apartment->panorama_image
            )
        );

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function fullAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('striptags', 'trim', 'int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $params = array('conditions' => array('id' => $id));
        $cacheName = md5(serialize(array(
            'ApartmentController',
            'showAction',
            $id
        )));

        $apartment = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartment) {
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
            $apartment = $apartmentRepo->getFull($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $apartment);
            }
        }

        if (isset($apartment[0])) {
            $apartment = $apartment[0];
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại sản phẩm này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $apartmentModel = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $id)
            ));

            $apartmentModel->view_count = $apartmentModel->view_count + 1;
            $apartmentModel->save();
        }

        $agent = \ITECH\Data\Model\UserModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $apartment->user_id)
        ));

        $galleryUrl = array();
        $gallery = json_decode($apartment->gallery, true);
        if (count($gallery)) {
            foreach ($gallery as $g) {
                if ($g != '') {
                    $galleryUrl[] = $this->config->cdn->dir_upload . $g;
                }
            }
        }
        $response['result'] = $this->buildItemApartment($apartment, $agent);

        RETURN_RESPONSE:
            parent::outputJSON($response);

    }

    public function ceriterialAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $type = $this->request->getQuery('type', array('int'), '');
        $isHome = $this->request->getQuery('is_home', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array();
        $params['conditions'][] = 'status = :status:';
        $params['bind']['status'] = \ITECH\Data\Lib\Constant::CERITERIAL_STATUS_ACTIVE;

        if ($type != '') {
            $params['conditions'][] = 'type = :type:';
            $params['bind']['type'] = $type;
        }

        if ($isHome != '') {
            $params['conditions'][] = 'is_home = :is_home:';
            $params['bind']['is_home'] = $isHome;
        }

        $conditions = trim(implode(' AND ', $params['conditions']));
        $bind = $params['bind'];

        $cacheName = md5(serialize(array(
            'ApartmentController',
            'ceriterialAction',
            'ApartmentCeriterialModel',
            'find',
            $params
        )));

        $apartmentCeriterial = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartmentCeriterial) {
            $apartmentCeriterial = \ITECH\Data\Model\ApartmentCeriterialModel::find(array(
                'conditions' => $conditions,
                'bind' => $bind
            ));

            if ($cache == 'true') {
                $this->cache->save($cacheName, $apartmentCeriterial);
            }
        }

        if (count($apartmentCeriterial)) {
            foreach ($apartmentCeriterial as $item) {
                $_result = array();
                $this->ceriterialVi($item, $_result);
                $this->ceriterialEng($item, $_result);

                $response['result'][] = $_result;
            }
        }

        parent::outputJSON($response);
    }

    public function requestAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        $apartmentId = $this->request->getQuery('apartment_id', array('int'), '');

        $apartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $apartmentId,
                'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
            )
        ));

        if (!$apartment) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại sản phẩm này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($apartment->type == \ITECH\Data\Lib\Constant::APARTMENT_TYPE_RENT) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'sản phẩm này chỉ cho thuê.'
            );
            goto RETURN_RESPONSE;
        }

        if ($apartment->condition == \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'sản phẩm này đã được bán.'
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
                'message' => 'Yêu cầu nhập họ tên.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));

            $validator->add('phone', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập số điện thoại.'
            )));
            $validator->setFilters('phone', array('striptags', 'trim'));

            $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
                'message' => 'Email không hợp lệ.'
            )));
            $validator->setFilters('email', array('striptags', 'trim', 'lower'));

            $validator->setFilters('apartment_id', array('int'));
            $validator->setFilters('agent_id', array('int'));
            $validator->setFilters('description', array('striptags', 'trim'));
            $validator->setFilters('pay_method', array('int'));

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

            $user = \ITECH\Data\Model\UserModel::findFirst(array(
                'conditions' => 'email = :email: AND type = :type:',
                'bind' => array(
                    'email' => $validator->getValue('email'),
                    'type' => \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER
                )
            ));

            if (!$user) {
                $user = new \ITECH\Data\Model\UserModel();
                $user->username = $validator->getValue('email');
                $user->password = \ITECH\Data\Lib\Util::hashPassword('jinn123456');
                $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('name'));
                $user->slug = \ITECH\Data\Lib\Util::slug($user->name);
                $user->gender = \ITECH\Data\Lib\Constant::USER_GENDER_UNDEFINED;
                $user->email = $validator->getValue('email');
                $user->phone = $validator->getValue('phone');
                $user->type = \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER;
                $user->membership = \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_USER_USER;
                $user->status = \ITECH\Data\Lib\Constant::USER_STATUS_ACTIVE;
                $user->is_verified = \ITECH\Data\Lib\Constant::USER_IS_VERIFIED_YES;
                $user->created_at = date('Y-m-d H:i:s');
            } else {
                $user->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('name'));
                $user->slug = \ITECH\Data\Lib\Util::slug($user->name);
                $user->phone = $validator->getValue('phone');
                $user->updated_at = date('Y-m-d H:i:s');
            }

            if (!$user->save()) {
                $messages = $user->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể tạo thành viên.';

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $message
                );
                goto RETURN_RESPONSE;
            }

            $request = \ITECH\Data\Model\ApartmentRequestModel::findFirst(array(
                'conditions' => 'user_id = :user_id:
                    AND agent_id = :agent_id:
                    AND apartment_id = :apartment_id:
                    AND status <> :rejected_status:',
                'bind' => array(
                    'user_id' => $user->id,
                    'agent_id' => $validator->getValue('agent_id'),
                    'apartment_id' => $validator->getValue('apartment_id'),
                    'rejected_status' => \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_REJECTED
                )
            ));

            if (!$request) {
                $apartment->condition = \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD;
                $apartment->save();

                $request = new \ITECH\Data\Model\ApartmentRequestModel();
                $request->user_id = $user->id;
                $request->agent_id = $validator->getValue('agent_id');
                $request->apartment_id = $validator->getValue('apartment_id');
                $request->description = $validator->getValue('description');
                $request->pay_method = $validator->getValue('pay_method');
                $request->status = \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_WAITING;
                $request->created_at = date('Y-m-d H:i:s');

                if (!$request->save()) {
                    $messages = $request->getMessages();
                    $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể tạo request.';

                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $message
                    );
                    goto RETURN_RESPONSE;
                }
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => array('request_id' => (int)$request->id)
            );
            goto RETURN_RESPONSE;
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    private function ceriterialVi($item, &$_result)
    {
        $searchQuery = array();
        $searchQueryJson = array();

        $attType = array();
        $attView = array();
        $attTrend = array();
        $attUtility = array();
        $projectIds = array();

        // --------- Attribute type
        if ($item->attribute_type != '') {
            $attType = parent::getCeriterial($item->attribute_type);
        }
        if (count($attType)) {
            $searchQuery[] = http_build_query($attType);
        }
        // Attribute type ---------

        // --------- Attribute view
        if ($item->attribute_view != '') {
            $attView = parent::getCeriterial($item->attribute_view);
        }
        if (count($attView)) {
            $searchQuery[] = http_build_query($attView);
        }
        // Attribute view ---------

        // --------- Attribute trend
        if ($item->direction != '') {
            $array = array_filter(explode('-', $item->direction));
            if (count($array)) {
                $i = 0;

                foreach ($array as $at) {
                    $searchQueryJson['att_trend[' . $i . ']'] = $at;
                    $attTrend['att_trend[' . $i . ']'] = $at;
                    $i++;
                }
            }
        }

        if (count($attTrend)) {
            $searchQuery[] = http_build_query($attTrend);
        }
        // Attribute trend ---------

        // --------- Attribute utility
        if ($item->attribute_utility != '') {
            $attUtility = parent::getCeriterial($item->attribute_utility);
        }
        if (count($attUtility)) {
            $searchQuery[] = http_build_query($attUtility);
        }
        // Attribute utility ---------

        // --------- Type
        if ($item->type != '') {
            $searchQueryJson['type'] = $item->type;
            $searchQuery[] = http_build_query(array('type' => $item->type));
        }
        // Type ---------

        // --------- Project id
        if ($item->project_ids != '') {
            $array = array_filter(explode('-', $item->project_ids));
            if (count($array)) {
                $i = 0;

                foreach ($array as $at) {
                    $searchQueryJson['project_ids[' . $i . ']'] = $at;
                    $projectIds['project_ids[' . $i . ']'] = $at;
                    $i++;
                }
            }
        }

        if (count($projectIds)) {
            $searchQuery[] = http_build_query($projectIds);
        }
        // Project id ---------

        if ($item->bathroom_count > 0) {
            $searchQueryJson['bathroom_count'] = $item->bathroom_count;
            $searchQuery[] = http_build_query(array('bathroom_count' => $item->bathroom_count));
        }

        if ($item->bedroom_count > 0) {
            $searchQueryJson['bedroom_count'] = $item->bedroom_count;
            $searchQuery[] = http_build_query(array('bedroom_count' => $item->bedroom_count));
        }

        if ($item->total_area != '') {
            $searchQueryJson['total_area'] = $item->total_area;
            $searchQuery[] = http_build_query(array('total_area' => $item->total_area));
        }

        // --------- Price
        if ($item->price_min > 0) {
            $searchQueryJson['price_min'] = $item->price_min;
            $searchQuery[] = http_build_query(array('price_min' => $item->price_min));
        }

        if ($item->price_max > 0 && $item->price_max > $item->price_min) {
            $searchQueryJson['price_max'] = $item->price_max;
            $searchQuery[] = http_build_query(array('price_max' => $item->price_max));
        }
        // Price ---------
        $searchQueryJson['cid'] = $item->id;
        $searchQuery[] = http_build_query(array('cid' => $item->id));

        if (count($searchQuery)) {
            $searchQuery = implode('&', $searchQuery);
        }

        $_result['id'] = (int)$item->id;
        $_result['name'] = $item->name;
        $_result['attribute_type'] = $item->attribute_type;
        $_result['attribute_view'] = $item->attribute_view;
        $_result['attribute_utility'] = $item->attribute_utility;
        $_result['attribute_room_type'] = $item->attribute_room_type;
        $_result['attribute_best_for'] = $item->attribute_best_for;
        $_result['attribute_suitable_for'] = $item->attribute_suitable_for;
        $_result['project_ids'] = $item->project_ids;
        $_result['bathroom_count'] = $item->bathroom_count > 0 ? (int)$item->bathroom_count : null;
        $_result['bedroom_count'] = $item->bedroom_count > 0 ? (int)$item->bedroom_count : null;
        $_result['total_area'] = $item->total_area;
        $_result['price_min'] = $item->price_min > 0 ? (int)$item->price_min : null;
        $_result['price_max'] = $item->price_max > 0 ? (int)$item->price_max : null;
        $_result['direction'] = $item->direction;
        $_result['is_new'] = $item->is_new != '' ? (int)$item->is_new : null;
        $_result['is_home'] = $item->is_home != '' ? (int)$item->is_home : null;
        $_result['type'] = (int)$item->type;
        $_result['template'] = $item->template;
        $_result['ordering'] = (int)$item->ordering;
        $_result['search_query'] = $searchQuery;
        $_result['search_query_json'] = json_encode($searchQueryJson);

        return $_result;
    }

    private function ceriterialEng($item, &$_result)
    {
        $searchQueryEng = array();
        $searchQueryJsonEng = array();

        $attTypeEng = array();
        $attViewEng = array();
        $attTrendEng = array();
        $attUtilityEng = array();
        $projectIdsEng = array();

        // --------- Attribute type
        if ($item->attribute_type != '') {
            $attTypeEng = parent::getCeriterial($item->attribute_type, 2);
        }
        if (count($attTypeEng)) {
            $searchQueryEng[] = http_build_query($attTypeEng);
        }
        // Attribute type ---------

        // --------- Attribute view
        if ($item->attribute_view != '') {
            $attViewEng = parent::getCeriterial($item->attribute_view, 2);
        }
        if (count($attViewEng)) {
            $searchQueryEng[] = http_build_query($attViewEng);
        }
        // Attribute view ---------

        // --------- Attribute trend
        if ($item->direction != '') {
            $array = array_filter(explode('-', $item->direction));
            if (count($array)) {
                $i = 0;

                foreach ($array as $at) {
                    $searchQueryJsonEng['att_trend_eng[' . $i . ']'] = $at;
                    $attTrendEng['att_trend_eng[' . $i . ']'] = $at;
                    $i++;
                }
            }
        }

        if (count($attTrendEng)) {
            $searchQueryEng[] = http_build_query($attTrendEng);
        }
        // Attribute trend ---------

        // --------- Attribute utility
        if ($item->attribute_utility != '') {
            $attUtilityEng = parent::getCeriterial($item->attribute_utility, 2);
        }
        if (count($attUtilityEng)) {
            $searchQueryEng[] = http_build_query($attUtilityEng);
        }
        // Attribute utility ---------

        // --------- Type
        if ($item->type != '') {
            $searchQueryJsonEng['type'] = $item->type;
            $searchQueryEng[] = http_build_query(array('type' => $item->type));
        }
        // Type ---------

        // --------- Project id
        if ($item->project_ids != '') {
            $array = array_filter(explode('-', $item->project_ids));
            if (count($array)) {
                $i = 0;

                foreach ($array as $at) {
                    $searchQueryJsonEng['project_ids[' . $i . ']'] = $at;
                    $projectIdsEng['project_ids[' . $i . ']'] = $at;
                    $i++;
                }
            }
        }

        if (count($projectIdsEng)) {
            $searchQueryEng[] = http_build_query($projectIdsEng);
        }
        // Project id ---------

        if ($item->bathroom_count > 0) {
            $searchQueryJsonEng['bathroom_count'] = $item->bathroom_count;
            $searchQueryEng[] = http_build_query(array('bathroom_count' => $item->bathroom_count));
        }

        if ($item->bedroom_count > 0) {
            $searchQueryJsonEng['bedroom_count'] = $item->bedroom_count;
            $searchQueryEng[] = http_build_query(array('bedroom_count' => $item->bedroom_count));
        }

        if ($item->total_area != '') {
            $searchQueryJsonEng['total_area'] = $item->total_area;
            $searchQueryEng[] = http_build_query(array('total_area' => $item->total_area));
        }

        // --------- Price
        if ($item->price_min_eng > 0) {
            $searchQueryJsonEng['price_min_eng'] = $item->price_min_eng;
            $searchQueryEng[] = http_build_query(array('price_min_eng' => $item->price_min_eng));
        }

        if ($item->price_max_eng > 0 && $item->price_max_eng > $item->price_min_eng) {
            $searchQueryJsonEng['price_max'] = $item->price_max_eng;
            $searchQueryEng[] = http_build_query(array('price_max_eng' => $item->price_max_eng));
        }
        // Price ---------
        $searchQueryJsonEng['cid'] = $item->id;
        $searchQueryEng[] = http_build_query(array('cid' => $item->id));

        if (count($searchQueryEng)) {
            $searchQueryEng = implode('&', $searchQueryEng);
        }

        $_result['id'] = (int)$item->id;
        $_result['name_eng'] = $item->name_eng;
        $_result['attribute_type'] = $item->attribute_type;
        $_result['attribute_view'] = $item->attribute_view;
        $_result['attribute_utility'] = $item->attribute_utility;
        $_result['attribute_room_type'] = $item->attribute_room_type;
        $_result['attribute_best_for'] = $item->attribute_best_for;
        $_result['attribute_suitable_for'] = $item->attribute_suitable_for;
        $_result['project_ids'] = $item->project_ids;
        $_result['bathroom_count'] = $item->bathroom_count > 0 ? (int)$item->bathroom_count : null;
        $_result['bedroom_count'] = $item->bedroom_count > 0 ? (int)$item->bedroom_count : null;
        $_result['total_area'] = $item->total_area;
        $_result['price_min_eng'] = $item->price_min_eng > 0 ? (int)$item->price_min_eng : null;
        $_result['price_max_eng'] = $item->price_max_eng > 0 ? (int)$item->price_max_eng : null;
        $_result['direction'] = $item->direction;
        $_result['is_new'] = $item->is_new != '' ? (int)$item->is_new : null;
        $_result['is_home'] = $item->is_home != '' ? (int)$item->is_home : null;
        $_result['type'] = (int)$item->type;
        $_result['template'] = $item->template;
        $_result['ordering'] = (int)$item->ordering;
        $_result['search_query_eng'] = $searchQueryEng;
        $_result['search_query_json_eng'] = json_encode($searchQueryJsonEng);

        return $_result;
    }

    private function buildItemApartment($apartment, $agent = array(), $cache = 'true')
    {
        $typeTrend = \ITECH\Data\Lib\Constant::getDirection();
        $getCondition = \ITECH\Data\Lib\Constant::getApartmentCondition();

        $directionText = '';
        if (isset($apartment->direction) && ($apartment->direction > 0)) {
            $directionText = $typeTrend[(int)$apartment->direction];
        }

        $conditionText = '';
        $condition = '';
        if (isset($apartment->conditions) && $apartment->conditions > 0) {
            $conditionText = $getCondition[$apartment->conditions];
            $condition = $apartment->conditions;
        } else if (isset($apartment->condition) && $apartment->condition > 0){
            $conditionText = $getCondition[$apartment->condition];
            $condition = $apartment->condition;
        }

        $defaultImageUrl = parent::$noImageUrl;
        $default_thumbnail_url = parent::$noImageUrl;
        if ($apartment->default_image != '') {
            $defaultImageUrl = $this->config->cdn->dir_upload . $apartment->default_image;
            $default_thumbnail_url = $this->config->cdn->dir_upload . 'thumbnail/' . $apartment->default_image;
        }

        $position = json_decode($apartment->position);
        $position_image_url = parent::$noImageUrl;
        if (isset($position->image) && $position->image != '') {
            $position_image_url = $this->config->cdn->dir_upload . $position->image;
        }
        if (isset($position->description) && $position->description != '') {
            $position_description = $position->description;
        } else {
            $position_description = null;
        }

        $positionEng = json_decode($apartment->position_eng);
        if (isset($positionEng->description) && $positionEng->description != '') {
            $position_description_eng = $positionEng->description;
        } else {
            $position_description_eng = null;
        }

        $panorama_view_url = parent::$noImageUrl;
        if (isset($apartment->panorama_view) && $apartment->panorama_view != '') {
            $panorama_view_url = $this->config->cdn->dir_upload . $apartment->panorama_view;
        }

        $attributes = parent::getAttrApartment($apartment->id,  $cache);

        $_listGallery = \ITECH\Data\Model\MapImageModel::find(array(
            'conditions' => 'item_id = :item_id: AND module = :module:',
            'order' => 'id DESC',
            'bind' => array(
                'item_id'=> (int)$apartment->id,
                'module'=> \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT
            )
        ));

        $listGallery= array();
        if ($_listGallery) {
            foreach ($_listGallery as $item) {
                $listGallery[$item->type] = array(
                    'id' => (int)$item->id,
                    'image' => $item->image,
                    'name' => \ITECH\Data\Lib\Constant::getMapImageType()[$item->type],
                    'floor' => $item->floor,
                    'position' => $item->position,
                    'updated_at' => $item->updated_at
                );
            }
        }

        if (is_object($agent)) {
            $agent = (array)$agent;
        }

        $response = array(
            'id' => (int)$apartment->id,
            'name' => $apartment->name,
            'name_eng' => $apartment->name_eng,
            'slug' => $apartment->slug,
            'slug_eng' => $apartment->slug_eng,
            'default_image' => $apartment->default_image,
            'default_image_url' => $defaultImageUrl,
            'default_thumbnail_url' => $default_thumbnail_url,
            'images' => $listGallery,
            'user_id' => (int)$apartment->user_id,
            'block_id' => (int)$apartment->block_id,
            'block_name' => $apartment->block_name,
            'block_name_eng' => $apartment->block_name_eng,
            'block_slug' => \ITECH\Data\Lib\Util::slug($apartment->block_name),
            'block_slug_eng' => \ITECH\Data\Lib\Util::slug($apartment->block_name_eng),
            'project_id' => (int)$apartment->project_id,
            'project_name' => $apartment->project_name,
            'project_name_eng' => $apartment->project_name_eng,
            'project_slug' => \ITECH\Data\Lib\Util::slug($apartment->project_name),
            'project_slug_eng' => \ITECH\Data\Lib\Util::slug($apartment->project_name_eng),
            'condition' => $condition,
            'condition_text' => $conditionText,
            'type' => (int)$apartment->type,
            'description' => isset($apartment->description) ? $apartment->description : '',
            'description_eng' => isset($apartment->description_eng) ? $apartment->description_eng : '',
            'price' => $apartment->price,
            'price_eng' => $apartment->price_eng,
            'price_sale_off' => $apartment->price_sale_off,
            'price_sale_off_eng' => $apartment->price_sale_off_eng,
            'position_image_url' => $position_image_url,
            'position_description' => $position_description,
            'position' => array(
                'image' => $position_image_url,
                'description' => $position_description,
                'description_eng' => $position_description_eng,
            ),
            'attributes' => $attributes,
            'view_count' => (int)$apartment->view_count,
            'floor' => (int)$apartment->floor,
            'room_count' => (int)$apartment->room_count,
            'bedroom_count' => (int)$apartment->bedroom_count,
            'bathroom_count' => (int)$apartment->bathroom_count,
            'ordering' => isset($apartment->ordering) ? (int)$apartment->ordering : 0,
            'status' => (int)$apartment->status,
            'created_by' => (int)$apartment->created_by,
            'updated_by' => (int)$apartment->updated_by,
            'approved_by' => (int)$apartment->approved_by,
            'address' => $apartment->project_address,
            'project_apartment_count' => isset($apartment->project_apartment_count) ? (int)$apartment->project_apartment_count : -1,
            'project_block_count' => isset($apartment->project_block_count) ? (int)$apartment->project_block_count : -1,
            'panorama_view_url' => $panorama_view_url,
            'total_area' => $apartment->total_area,
            'green_area' => $apartment->green_area,
            'direction' => isset($apartment->trend) ? (int)$apartment->trend : null,
            'direction_text' => $directionText,
            'save_home' => isset($apartment->save_home) ? $apartment->save_home : '',
            'agent' => array(
                'id' => isset($agent['id']) ? (int)$agent['id'] : null,
                'username' => isset($agent['username']) ? $agent['username'] : '',
                'name' => isset($agent['name']) ? $agent['name'] : '',
                'email' => isset($agent['email']) ? $agent['email'] : '',
                'phone' => isset($agent['phone']) ? $agent['phone'] : '',
                'experience' => isset($agent['experience']) ? $agent['experience'] : '',
                'description' => isset($agent['description']) ? $agent['description'] : '',
                'avatar' => isset($agent['avatar']) ? $agent['avatar'] : ''
            ),
            'seo' => array(
                'meta_title' => $apartment->meta_title,
                'meta_title_eng' => $apartment->meta_title_eng,
                'meta_keywords' => $apartment->meta_keywords,
                'meta_keywords_eng' => $apartment->meta_keywords_eng,
                'meta_description' => $apartment->meta_description,
                'meta_description_eng' => $apartment->meta_description_eng
            )
        );

        return $response;
    }

    private function getAllAttributeApartment($item)
    {
        $cacheName = md5(serialize(array(
            'ApartmentControler',
            'getAllAttributeApartment',
            $item
        )));

        $attributes = $this->cache->get($cacheName);
        if (!$attributes) {
            $attributes = array();

            $apartmentModel = new \ITECH\Data\Model\ApartmentModel();
            $b = $apartmentModel->getModelsManager()->createBuilder();
            $b->columns(array(
                'ap1.id AS apartment_id',
                'at1.id AS attribute_id',
                'at1.name AS attribute_name',
                'at1.name_eng AS attribute_name_eng',
                'at1.type AS attribute_type'
            ));

            $b->from(array('ap1' => 'ITECH\Data\Model\ApartmentModel'));
            $b->innerJoin('ITECH\Data\Model\ApartmentAttributeModel', 'aa1.apartment_id = ap1.id', 'aa1');
            $b->innerJoin('ITECH\Data\Model\AttributeModel', 'at1.id = aa1.attribute_id', 'at1');

            $b->andWhere('ap1.id = :apartment_id:', array('apartment_id' => $item->id));
            $result = $b->getQuery()->execute();

            if (count($result)) {
                foreach ($result as $r) {
                    $attributes[] = array(
                        'attribute_id' => (int)$r['attribute_id'],
                        'name' => $r['attribute_name'],
                        'name_eng' => $r['attribute_name_eng'],
                        'type' => (int)$r['attribute_type']
                    );
                }
                $this->cache->save($cacheName, $attributes);
            }
        }
        return $attributes;
    }
}
