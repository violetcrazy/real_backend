<?php
namespace ITECH\Api\Controller;

class SearchController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function indexAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $userId = $this->request->getQuery('user_id', array('striptags', 'trim', 'int'), '');
        $blockId = $this->request->getQuery('block_id', array('striptags', 'trim', 'int'), '');

        $projectId = $this->request->getQuery('project_id', array('striptags', 'trim', 'int'), '');
        $projectIds = $this->request->getQuery('project_ids');

        $notId = $this->request->getQuery('not_id', array('int'), '');
        $price = $this->request->getQuery('price', array('int'), '');

        $priceMin = $this->request->getQuery('price_min', array('int'), '');
        $priceMax = $this->request->getQuery('price_max', array('int'), '');

        $direction = $this->request->getQuery('direction');
        $location = $this->request->getQuery('location', array('int'), '');
        $adults = $this->request->getQuery('adults', array('int'), '');
        $children = $this->request->getQuery('children', array('int'), '');

        $bathroomCount = $this->request->getQuery('bathroom_count', array('int'), '');
        $bedroomCount = $this->request->getQuery('bedroom_count', array('int'), '');
        $bedroomMin = $this->request->getQuery('bedroom_min', array('int'), '');
        $bedroomMax = $this->request->getQuery('bedroom_max', array('int'), '');

        $total_area = $this->request->getQuery('total_area', array('striptags', 'trim'), '');
        $floor = $this->request->getQuery('floor', array('int'), '');

        $attributes = $this->request->getQuery('attributes');
        $direction = $this->request->getQuery('trends');
        //$filter = $this->request->getQuery('filter');

        $params = array(
            'conditions' => array(),
            'order' => 'a1.price ASC',
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

        if ($location != '') {
            $params['conditions']['location'] = $location;
        }

        if ($type != '') {
            $params['conditions']['type'] = $type;
        }

        if ($blockId != '' && $blockId != '') {
            $params['conditions']['block_id'] = $blockId;
        }

        if ($projectId != '' && $projectId != '') {
            $params['conditions']['project_id'] = $projectId;
        }

        if (count($projectIds)) {
            $params['conditions']['project_ids'] = $projectIds;
        }

        if ($adults != '' && $adults != '') {
            $params['conditions']['adults'] = $adults;
        }

        if ($children != '' && $children != '') {
            $params['conditions']['children'] = $children;
        }

        if ($bedroomCount != '') {
            $params['conditions']['bedroom_count'] = $bedroomCount;
        }

        if ($bedroomMin != '') {
            $params['conditions']['bedroom_min'] = $bedroomMin;
        }

        if ($bedroomMax != '') {
            $params['conditions']['bedroom_max'] = $bedroomMax;
        }

        if ($bathroomCount != '') {
            $params['conditions']['bathroom_count'] = $bathroomCount;
        }

        if ($floor != '') {
            $params['conditions']['floor_count'] = $floor;
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

                case 'price':
                    $params['order'] = 'a1.price ' . $sortBy;
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
                $params['conditions']['trends_id'] = $directionId;
            }
        }

        /*
        if (count($filter)) {
            $attributes = array();
            foreach ($filter as $f) {
                $attributes[] = $f;
            }

            if (count($attributes)) {
                $params['conditions']['attributes'] = $attributes;
            }
        }
        */

        $cacheName = md5(serialize(array(
            'SearchController',
            'indexAction',
            'ApartmentRepo',
            'getPaginationList',
            $params
        )));

        $apartments = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartments) {
            $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();

            if (isset($params['conditions']['attributes_id'])) {
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

        $typeDirection = \ITECH\Data\Lib\Constant::getDirection();

        foreach ($apartments->items as $item) {
            if (is_object($item)) {
                $item = (array)$item;
            }

            if ($userId != '') {
                $saveHome = \ITECH\Data\Model\UserSaveModel::count(array(
                    'conditions' => 'user_id = :user_id: AND value = :value: AND key = :key:',
                    'bind' => array(
                        'user_id' => $userId,
                        'value' => $item->id,
                        'key' => \ITECH\Data\Lib\Constant::USER_SAVE_HOME
                    )
                ));

                if ($saveHome > 0) {
                    $item['save_home'] = 'true';
                } else {
                    $item['save_home'] = 'false';
                }
            } else {
                $item['save_home'] = 'false';
            }

            $default_image_url = parent::$noImageUrl;
            $default_thumbnail_url = parent::$noImageUrl;

            if ($item['default_image'] != '') {
                $default_image_url = $this->config->cdn->dir_upload . $item['default_image'];
                $default_thumbnail_url = $this->config->cdn->dir_upload . 'thumbnail/' . $item['default_image'];
            }

            $cacheName = md5(serialize(array(
                'SearchController',
                'indexAction',
                'ApartmentModel',
                'AttributeModel',
                'getList',
                $item['id']
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

                $b->andWhere('ap1.id = :apartment_id:', array('apartment_id' => $item['id']));
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

            $response['result'][] = array(
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'slug' => \ITECH\Data\Lib\Util::slug($item['name']),
                'user_id' => (int)$item['user_id'],
                'block' => [
                    'id' => (int)$item['block_id'],
                    'name' => $item['block_name'],
                    'slug' => \ITECH\Data\Lib\Util::slug($item['block_name'])
                ],
                'project' => [
                    'id' => (int)$item['project_id'],
                    'name' => $item['project_name'],
                    'slug' => \ITECH\Data\Lib\Util::slug($item['project_name'])
                ],
                'condition' => (int)$item['condition'],
                'type' => (int)$item['type'],
                'price' => (int)$item['price'],
                'price_sale_off' => (int)$item['price_sale_off'],
                'position' => $item['position'],
                'total_area' => $item['total_area'],
                'green_area' => $item['green_area'],
                'view_count' => (int)$item['view_count'],
                'default_image' => $item['default_image'],
                'default_image_url' => $default_image_url,
                'default_thumbnail_url' => $default_thumbnail_url,
                'gallery' => json_decode($item['gallery']),
                'floor' => (int)$item['floor'],
                'room_count' => (int)$item['room_count'],
                'bedroom_count' => (int)$item['bedroom_count'],
                'bathroom_count' => (int)$item['bathroom_count'],
                'status' => (int)$item['status'],
                'created_by' => (int)$item['created_by'],
                'updated_by' => (int)$item['updated_by'],
                'approved_by' => (int)$item['approved_by'],
                'address' => $item['project_address'],
                'save_home' => (bool)$item['save_home'],
                'direction' => (int)$item['direction'],
                'direction_text' => isset($typeDirection[$item['direction']]) ? $typeDirection[$item['direction']] : '',
                'attributes' => $attributes
            );
        }

        $response['total_items'] = $apartments->total_items;
        $response['total_pages'] = isset($apartments->total_pages) ? $apartments->total_pages : ceil($apartments->total_items / $limit);

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}