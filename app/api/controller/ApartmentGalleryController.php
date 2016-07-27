<?php
namespace ITECH\Api\Controller;

class ApartmentGalleryController extends \ITECH\Api\Controller\BaseController
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

        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $apartment_id = $this->request->getQuery('apartment_id', array('striptags', 'trim', 'int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array(
            'conditions' => array(
                'apartment_id' => $apartment_id
            ),
            'order' => 'ag1.name DESC',
            'page' => $page,
            'limit' => $limit
        );

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
            'ApartmentGalleryRepo',
            'getPaginationList',
            $params
        )));

        $gallery = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$gallery) {
            $apartmentGalleryRepo = new \ITECH\Data\Repo\ApartmentGalleryRepo();
            $gallery = $apartmentGalleryRepo->getPaginationList($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $gallery);
            }
        }

        foreach ($gallery->items as $item) {
            $response['result'][] = array(
                'id' => (int)$item['id'],
                'apartment_id' => $item['apartment_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'gallery' => $item['gallery']
            );
        }

        $response['total_items'] = $gallery->total_items;
        $response['total_pages'] = isset($gallery->total_pages) ? $gallery->total_pages : ceil($gallery->total_items / $limit);

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

        $apartmentId = $this->request->getQuery('apartment_id', array('int'), '');

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên bộ sưu tập.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));
            $validator->setFilters('name', array('striptags', 'trim'));
            $validator->setFilters('price', array('striptags', 'trim'));
            $validator->setFilters('gallery', array('striptags', 'trim'));

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

            $apartmentGallery = new \ITECH\Data\Model\ApartmentGalleryModel();
            $apartmentGallery->apartment_id = $apartmentId;
            $apartmentGallery->name = $validator->getValue('name');
            $apartmentGallery->price = $validator->getValue('price');
            $apartmentGallery->gallery = $validator->getValue('gallery');

            try {
                if (!$apartmentGallery->create()) {
                    $messages = $apartmentGallery->getMessages();
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
                        'id' => (int)$apartmentGallery->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[ApartmentGalleryController][addAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'MapModel',
            'findFirst',
            $id
        )));

        $gallery = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$gallery) {
            if ($id != '') {
                $gallery = \ITECH\Data\Model\ApartmentGalleryModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $id,
                    )
                ));
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $gallery);
            }
        }

        if (!$gallery) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, Không tồn tại bộ sưu tập này.'
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
                    'message' => 'Yêu cầu nhập tên bộ sưu tập.'
                )));
                $validator->setFilters('name', array('striptags', 'trim'));
            }

            if (isset($post->price)) {
                $validator->setFilters('price', array('striptags', 'trim'));
            }
            if (isset($post->gallery)) {
                $validator->setFilters('gallery', array('striptags', 'trim'));
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
                $gallery->name = \ITECH\Data\Lib\Util::upperFirstLetter($validator->getValue('name'));
            }

            if ($validator->getValue('price')) {
                $gallery->price = $validator->getValue('price');
            }

            if ($validator->getValue('gallery')) {
                $gallery->gallery = $validator->getValue('gallery');
            }

            try {
                if (!$gallery->update()) {
                    $messages = $gallery->getMessages();
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

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array(
                'id' => (int)$gallery->id,
                'apartment_id' => (int)$gallery->apartment_id,
                'name' => $gallery->name,
                'price' => $gallery->price,
                'gallery' => $gallery->gallery
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function listByProjectAction()
    {
        $response = array();

        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        //$project_id = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array(
            'conditions' => array(
                'type' => $type
            )
        );
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