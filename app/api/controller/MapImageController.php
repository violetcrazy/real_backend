<?php
namespace ITECH\Api\Controller;

class MapImageController extends \ITECH\Api\Controller\BaseController
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
        $item_id = $this->request->getQuery('item_id', array('striptags', 'trim', 'int'), '');
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

        if ($item_id != '') {
            $params['conditions']['item_id'] = $item_id;
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
            'MapImageController',
            'listAction',
            'MapImageRepo',
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

    public function fullAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $item_id = $this->request->getQuery('item_id', array('striptags', 'trim', 'int'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        //$cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array();

        if ($module != '') {
            $params['conditions']['module'] = $module;
        }

        if ($item_id != '') {
            $params['conditions']['item_id'] = $item_id;
        }

        if ($type != '') {
            $params['conditions']['type'] = $type;
        }

        $mapImageRepo = new \ITECH\Data\Repo\MapImageRepo();
        $mapImages = $mapImageRepo->getAll($params);
        foreach ($mapImages->toArray() as $item) {
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

            return parent::outputJSON($response);
    }

    public function addAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Error.',
            'result' => array()
        );

        $item_id = $this->request->getQuery('item_id', array('int'), '');
        $module = $this->request->getQuery('module', array('int'), '');

        if (!in_array( $module, array(\ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT, \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK))) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, đăng hình hiển thị không đúng mục.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        switch($module) {
            case \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT:
                $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $item_id
                    )
                ));

                if (!$project) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không tồn tại dự án này..',
                        'result' => array()
                    );

                    goto RETURN_RESPONSE;
                }

            break;

            case \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK:
                $block = \ITECH\Data\Model\BlockModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $item_id
                    )
                ));

                if (!$block) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không tồn tại Block này.',
                        'result' => array()
                    );

                    goto RETURN_RESPONSE;
                }
            break;
        }

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->add('image', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'INVALID_IMAGE'
            )));

            $validator->setFilters('image', array('striptags', 'trim'));

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

            $mapImage = new \ITECH\Data\Model\MapImageModel();
            if ($module == \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT) {
                $mapImage->item_id = $project->id;
            } elseif ($module == \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK) {
                $mapImage->item_id = $block->id;
            }

            $mapImage->type = $post->type;
            $mapImage->image = $validator->getValue('image');
            $mapImage->created_by = $post->created_by;
            $mapImage->updated_by = $post->updated_by;
            $mapImage->floor = $post->floor;
            $mapImage->module = $module;
            $mapImage->created_at = date('Y-m-d H:i:s');

            try {
                if (!$mapImage->create()) {
                    $messages = $mapImage->getMessages();
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
                        'id' => (int)$mapImage->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[MapImageController][addAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
        $itemId = $this->request->getQuery('item_id', array('int'), '');
        $module = $this->request->getQuery('module', array('int'), '');
        //$type = $this->request->getQuery('type', array('striptags', 'trim', 'lower'), \ITECH\Data\Lib\Constant::USER_TYPE_MEMBER);
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');


        if (!in_array( $module, array(\ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT, \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK))) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, đăng hình hiển thị không đúng mục.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        switch($module) {
            case \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT:
                $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $itemId
                    )
                ));

                if (!$project) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không tồn tại dự án này..',
                        'result' => array()
                    );

                    goto RETURN_RESPONSE;
                }

            break;

            case \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK:
                $block = \ITECH\Data\Model\BlockModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array(
                        'id' => $itemId
                    )
                ));

                if (!$block) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không tồn tại Block này.',
                        'result' => array()
                    );

                    goto RETURN_RESPONSE;
                }
            break;
        }

        $cacheName = md5(serialize(array(
            'MapImageModel',
            'findFirst',
            $id
        )));

        $mapImage = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$mapImage) {
            if ($id != '') {
                $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array('id' => $id)
                ));
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $mapImage);
            }
        }

        if (!$mapImage) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại hình ảnh này.'
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

            if (isset($post->type)) {
                $validator->setFilters('type', array('striptags', 'trim'));
            }

            if (isset($post->floor)) {
                $validator->setFilters('floor', array('striptags', 'trim'));
            }

            if (isset($post->image)) {
                $validator->setFilters('image', array('striptags', 'trim'));
            }

            if (isset($post->updated_by)) {
                $validator->setFilters('updated_by', array('striptags', 'trim'));
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

            if (isset($post->type)) {
                $mapImage->type = $validator->getValue('type');
            }

            if (isset($post->floor)) {
                $mapImage->floor = $validator->getValue('floor');
            }

            if (isset($post->image)) {
                $mapImage->image = $validator->getValue('image');
            }

            if (isset($post->updated_by)) {
                $mapImage->updated_by = $validator->getValue('updated_by');
            }

            $mapImage->updated_at = date('Y-m-d H:i:s');

            try {
                if (!$mapImage->save()) {
                    $messages = $mapImage->getMessages();
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
                        'id' => (int)$mapImage->id
                    )
                );
            } catch (\Phalcon\Exception $e) {
                $this->logger->log('[MapImageController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
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
                'id' => (int)$mapImage->id,
                'item_id' => $mapImage->item_id,
                'type' => $mapImage->type,
                'floor' => $mapImage->floor,
                'image' => $mapImage->image,
                'module' => $mapImage->module,
                'created_at' => $mapImage->created_at,
                'updated_at' => $mapImage->updated_at
            )
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function detaiImageViewAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $itemId = $this->request->getQuery('item_id', array('int'), '');
        $module = $this->request->getQuery('module', array('int'), \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK);
        $type = $this->request->getQuery('type', array('int'), \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_IMAGE_VIEW);
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        //$cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $itemId
            )
        ));

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không tồn tại Block này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $mapRepo = new \ITECH\Data\Repo\MapRepo();
        $params = array();
        $params['conditions']['module'] = $module;
        $params['conditions']['type'] = $type;
        $params['conditions']['item_id'] = $itemId;

        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
            'conditions' => 'item_id = :item_id: AND type = :type: AND module = :module:',
            'bind' => array(
                'item_id' => $itemId,
                'type' => $type,
                'module' => $module
            )
        ));

        if ($mapImage) {
            $imageView = $mapRepo->getListByBlock($params);
            if ($imageView && count($imageView)) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'imageView' => $imageView->toArray(),
                        'image_default' => $mapImage->image
                    )
                );
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success.',
                    'result' => array(
                        'imageView' => array(),
                        'image_default' => $mapImage->image
                    )
                );
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => array(
                    'imageView' => array(),
                    'image_default' => ''
                )
            );
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function detaiPlanViewAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $item_id = $this->request->getQuery('item_id', array('striptags', 'trim', 'int'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        //$cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $item_id
            )
        ));

        if (!$block) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không tồn tại Block này.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        if ($module != '') {
            $params['conditions']['module'] = $module;
        }

        if ($item_id != '') {
            $params['conditions']['item_id'] = $item_id;
        }

        if ($type != '') {
            $params['conditions']['type'] = $type;
        }

        $params['order'] = 'mi1.floor ASC';
        $mapRepo = new \ITECH\Data\Repo\MapRepo();
        $mapImageRepo = new \ITECH\Data\Repo\MapImageRepo();
        $mapImages = $mapImageRepo->getAll($params);
        $planView = '';

        if ($mapImages) {
            foreach ($mapImages->toArray() as $key => $value) {
                $arr = array(
                    'conditions' => array('image_map_id' => $value['id'])
                );

                $arrMap = $mapRepo->getListByBlock($arr)->toArray();
                if (count($arrMap)) {
                    foreach ($arrMap as $key1 => $value1) {
                        $tmp[$key1]['id'] = $value1['id'];
                        $tmp[$key1]['image_map_id'] = $value1['image_map_id'];
                        $tmp[$key1]['apartment_name'] = $value1['apartment_name'];
                        $tmp[$key1]['apartment_id'] = $value1['apartment_id'];
                        $data = (array)json_decode($value1['map']);
                        $tmp[$key1]['coords'] = $data['coords'];
                        $tmp[$key]['strokeColor'] = $data['data-maphilight']->strokeColor;
                        $tmp[$key1]['fillColor'] = $data['data-maphilight']->fillColor;
                    }
                } else {
                    $tmp = array();
                }

                $planView[$value['floor']]['map'] = $tmp;
                $planView[$value['floor']]['image'] = $value['image'];
                $planView[$value['floor']]['floor'] = $value['floor'];
                $planView[$value['floor']]['image_map_id'] = $value['id'];
            }
        } else {
            $planView = '';
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $planView
            );
             goto RETURN_RESPONSE;
        }

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => $planView
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}