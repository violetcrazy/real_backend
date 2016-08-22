<?php
namespace ITECH\Api\Controller;

class MapController extends \ITECH\Api\Controller\BaseController
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
        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $params = array(
            'conditions' => array(
            ),
            'order' => 'mi1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        if ($module != '') {
            $params['conditions']['module'] = $module;
        }

        if ($sortField != '' && $sortBy != '') {
            if (!in_array($sortBy, array('ASC', 'DESC'))) {
                $sortBy = 'DESC';
            }

            switch ($sortField) {
                case 'updated_at':
                    $params['order'] = 'mi1.updated_at ' . $sortField;
                break;

                case 'id':
                    $params['order'] = 'mi1.id ' . $sortBy;
                break;
            }
        }

        $cacheName = md5(serialize(array(
            'MapController',
            'listAction',
            'MapRepo',
            'getPaginationList',
            $params
        )));

        $mapImages = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$mapImages) {
            $mapImageRepo = new \ITECH\Data\Repo\MapImageRepo();
            $mapImages = $mapImageRepo->getPaginationList($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $mapImages);
            }
        }

        foreach ($mapImages->items as $item) {
            $response['result'][] = array(
                'id' => (int)$item['id'],
                'item_id' => $item['item_id'],
                'type' => $item['type'],
                'floor' => $item['floor'],
                'image' => $item['image'],
                'module' => $item['module'],
                'updated_by' => $item['updated_by'],
                'updated_at' => $item['updated_at']
            );
        }

        $response['total_items'] = $mapImages->total_items;
        $response['total_pages'] = isset($mapImages->total_pages) ? $mapImages->total_pages : ceil($mapImages->total_items / $limit);

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function listByMapImageAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $mapImageId = $this->request->getQuery('image_map_id', array('striptags', 'trim', 'int'), '');
        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');

        if ($module == '') {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Error.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $params = array(
            'conditions' => array(
                'module' => $module,
                'image_map_id' => $mapImageId
            ),
            'order' => 'mi1.id DESC'
        );

        switch($module) {
            case \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK:
                $mapRepo = new \ITECH\Data\Repo\MapRepo();
                $mapImages = $mapRepo->getListByBlock($params);

                foreach ($mapImages as $item) {
                    $maps = json_decode($item['map']);
                    $viewMap = array();
                    foreach ($maps as $k => $v) {
                        if (is_object($v)) {
                            $viewMap['data-maphilight'] = json_encode($v);
                        } else {
                            $viewMap[$k] = $v;
                        }

                    }

                    $response['result'][] = array(
                        'id' => (int)$item['id'],
                        'image_map_id' => $item['image_map_id'],
                        'map_image_floor' => $item['map_image_floor'],
                        'view_map' => $viewMap,
                        'apartment_name' => $item['apartment_name'],
                        'apartment_id' => $item['apartment_id']
                    );
                }
            break;

            case \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT:
                $mapRepo = new \ITECH\Data\Repo\MapRepo();
                $mapImages = $mapRepo->getListByProject($params);
                foreach ($mapImages as $item) {
                    $maps = json_decode($item['map']);
                    $viewMap = array();
                    foreach ($maps as $k => $v) {
                        if (is_object($v)) {
                            $viewMap['data-maphilight'] = json_encode($v);
                        } else {
                            $viewMap[$k] = $v;
                        }

                    }

                    $response['result'][] = array(
                        'id' => (int)$item['id'],
                        'image_map_id' => $item['image_map_id'],
                        'map_image_floor' => $item['map_image_floor'],
                        'view_map' => $viewMap,
                        'block_name' => $item['block_name'],
                        'block_id' => $item['block_id']
                    );
                }
            break;
        }

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

            $validator->add('item_id', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_ITEM_ID'
            )));

            $validator->add('image_map_id', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_MAP_ID'
            )));

            $validator->add('map', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_MAP'
            )));

            $validator->setFilters('item_id', array('striptags', 'trim', 'int'));
            $validator->setFilters('image_map_id', array('striptags', 'trim', 'int'));
            $validator->setFilters('map', array('striptags', 'trim'));

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

            $map = new \ITECH\Data\Model\MapModel();
            $map->image_map_id = $validator->getValue('image_map_id');
            $map->item_id = $validator->getValue('item_id');
            $map->map = $validator->getValue('map');

            try {
                if (!$map->create()) {
                    $messages = $map->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo hình hiển thị.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$map->id
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
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'MapModel',
            'findFirst',
            $id
        )));

        $map = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$map) {
            if ($id != '') {
                $map = \ITECH\Data\Model\MapModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array('id' => $id)
                ));
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $map);
            }
        }

        if (!$map) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tọa độ này.'
            );
            goto RETURN_RESPONSE;
        }

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

            if (isset($post->map)) {
                $validator->setFilters('map', array('striptags', 'trim'));
            }

            if (isset($post->item_id)) {
                $validator->setFilters('item_id', array('striptags', 'trim', 'int'));
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

            if ($validator->getValue('map')) {
                $map->map = $validator->getValue('map');
            }

            if ($validator->getValue('item_id')) {
                $map->item_id = $validator->getValue('item_id');
            }

            try {
                if (!$map->update()) {
                    $messages = $map->getMessages();
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

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'id' => (int)$map->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[MapController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
                'id' => (int)$map->id,
                'image_map_id' => $map->image_map_id,
                'map' => $map->map,
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}