<?php
namespace ITECH\Api\Controller;

class AttributeController extends \ITECH\Api\Controller\BaseController
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
        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array(
            'conditions' => array(),
            'page' => $page,
            'limit' => $limit
        );

        if ($q != '') {
            $params['conditions']['q'] = $q;
        }

        if ($module != '') {
            $params['conditions']['module'] = $module;
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

        $cacheName = md5(serialize(array(
            'AttributeController',
            'listAction',
            'AttributeRepo',
            'getPaginationList',
            $params
        )));

        $attributes = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$attributes) {
            $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();
            $attributes = $attributeRepo->getPaginationList($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $attributes);
            }
        }

        foreach ($attributes->items as $item) {
            $image_one_url = $this->config->asset->icon_default;
            $image_two_url = $this->config->asset->icon_default;

            if ($item['image_one'] != '') {
                $image_one_url = $this->config->asset->frontend_url . 'upload/icon/black/' . $item['image_one'];
            }

            if ($item['image_two'] != '') {
                $image_two_url = $this->config->asset->frontend_url . 'upload/icon/white/' . $item['image_two'];
            }

            switch ($module) {
                case \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT:
                    $typeSelect = \ITECH\Data\Lib\Constant::getApartmentAttributeType();
                break;

                case \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK:
                    $typeSelect = \ITECH\Data\Lib\Constant::getBlockAttributeType();
                break;

                case \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT:
                    $typeSelect = \ITECH\Data\Lib\Constant::getProjectAttributeType();
                break;
            }

            $response['result'][] = array(
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'image_one' => $item['image_one'],
                'image_one_url' => $image_one_url,
                'image_two' => $item['image_two'],
                'image_two_url' => $image_two_url,
                'language' => (int)$item['language'],
                'type' => (int)$item['type'],
                'type_name' => isset($typeSelect[$item['type']]) ? $typeSelect[$item['type']] : null,
                'is_search' => (int)$item['is_search'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'status' => (int)$item['status']
            );
        }

        $response['total_items'] = $attributes->total_items;
        $response['total_pages'] = isset($attributes->total_pages) ? $attributes->total_pages : ceil($attributes->total_items / $limit);

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

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên thuộc tính.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));

            $validator->setFilters('module', array('striptags', 'trim', 'int'));
            $validator->setFilters('is_search', array('striptags', 'trim', 'int'));
            $validator->setFilters('status', array('striptags', 'trim', 'int'));
            $validator->setFilters('language', array('int'));
            $validator->setFilters('type', array('striptags', 'trim', 'int'));
            $validator->setFilters('image_one', array('striptags', 'trim'));
            $validator->setFilters('image_two', array('striptags', 'trim'));

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

            $slug = \ITECH\Data\Lib\Util::slug($validator->getValue('name'));
            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                'conditions' => 'slug = :slug: AND module = :module:',
                'bind' => array(
                    'slug' => $slug,
                    'module' => $validator->getValue('module')
                )
            ));

            if ($attribute) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Lỗi, thuộc tính đã tồn tại.'
                );
            } else {
                $attribute = new \ITECH\Data\Model\AttributeModel();
                $attribute->name = $validator->getValue('name');
                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                $attribute->image_one = $validator->getValue('image_one');
                $attribute->image_two = $validator->getValue('image_two');
                $attribute->language = $validator->getValue('language');
                $attribute->type = $validator->getValue('type');
                $attribute->is_search = $validator->getValue('is_search');
                $attribute->status = $validator->getValue('status');
                $attribute->module = $validator->getValue('module');
            }

            try {
                if (!$attribute->create()) {
                    $messages = $attribute->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo thuộc tính.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$attribute->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[MapController][addAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
        $module = $this->request->getQuery('module', array('int'), '');
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'AttributeController',
            'detailAction',
            'AttributeModel',
            'findFirst',
            $id
        )));

        $attribute = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$attribute) {
            if ($id != '') {
                $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                    'conditions' => 'id = :id: AND module = :module:',
                    'bind' => array(
                        'id' => $id,
                        'module' => $module
                    )
                ));
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $attribute);
            }
        }

        if (!$attribute) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại thuộc tính này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            if (isset($post->name)) {
                $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập tên thuộc tính.'
                )));
                $validator->setFilters('name', array('striptags', 'trim'));
            }

            if (isset($post->module)) {
                $validator->setFilters('module', array('int'));
            }

            if (isset($post->is_search)) {
                $validator->setFilters('is_search', array('int'));
            }

            if (isset($post->status)) {
                $validator->setFilters('status', array('int'));
            }

            if (isset($post->language)) {
                $validator->setFilters('language', array('int'));
            }

            if (isset($post->type)) {
                $validator->setFilters('type', array('int'));
            }

            if (isset($post->image_one)) {
                $validator->setFilters('image_one', array('striptags', 'trim'));
            }

            if (isset($post->image_two)) {
                $validator->setFilters('image_two', array('striptags', 'trim'));
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
                $attribute->name = $validator->getValue('name');
                $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
            }

            if ($validator->getValue('module')) {
                $attribute->module = $validator->getValue('module');
            }

            if ($validator->getValue('is_search')) {
                $attribute->is_search = $validator->getValue('is_search');
            }

            if ($validator->getValue('language')) {
                $attribute->language = $validator->getValue('language');
            }

            if ($validator->getValue('type')) {
                $attribute->type = $validator->getValue('type');
            }

            $attribute->image_one = $validator->getValue('image_one');
            $attribute->image_two = $validator->getValue('image_two');
            $attribute->updated_at = date('Y-m-d H:i:s');

            if ($validator->getValue('status')) {
                $attribute->status = $validator->getValue('status');
            }

            try {
                if (!$attribute->update()) {
                    $messages = $attribute->getMessages();
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
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[AttributeController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }
        }

        $image_one_url = $this->config->asset->icon_default;
        $image_two_url = $this->config->asset->icon_default;

        if ($attribute->image_one != '') {
            $image_one_url = $this->config->asset->frontend_url . 'upload/icon/black/' . $attribute->image_one;
        }

        if ($attribute->image_two != '') {
            $image_two_url = $this->config->asset->frontend_url . 'upload/icon/white/' . $attribute->image_two;
        }

        $typeSelect = \ITECH\Data\Lib\Constant::getProjectAttributeType();

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array(
                'id' => (int)$attribute->id,
                'name' => $attribute->name,
                'slug' => $attribute->slug,
                'image_one' => $attribute->image_one,
                'image_one_url' => $image_one_url,
                'image_two' => $attribute->image_two,
                'image_two_url' => $image_two_url,
                'language' => $attribute->language,
                'type' => $attribute->type,
                'type_name' => isset($typeSelect[$attribute->type]) ? $typeSelect[$attribute->type] : null,
                'is_search' => $attribute->is_search,
                'created_at' => $attribute->created_at,
                'updated_at' => $attribute->updated_at,
                'status' => $attribute->status
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function listByProjectAction()
    {
        $response = array();

        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        $project_id = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array(
            'conditions' => array('type' => $type)
        );

        if ($project_id != '') {
            $params['conditions']['project_id'] = $project_id;
        }

        $cacheName = md5(serialize(array(
            'AttributeController',
            'listByProjectAction',
            'AttributeRepo',
            'getListByProject',
            $params
        )));

        $attributes = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$attributes) {
            $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();
            $attributes = $attributeRepo->getListByProject($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $attributes);
            }
        }

        foreach ($attributes as $item) {
            $response[] = $item->name;
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}