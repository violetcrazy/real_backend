<?php
namespace ITECH\Api\Controller;

class HomeController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function locationListAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $cacheName = md5(serialize(array(
            'HomeController',
            'locationListAction',
            'Province',
            'find'
        )));

        $provinces = $this->cache->get($cacheName);
        if (!$provinces) {
            $provinces = \ITECH\Data\Model\LocationModel::find(array(
                'conditions' => 'parent_id = :parent_id:',
                'bind' => array('parent_id' =>(int)0),
                'order' => 'ordering ASC, id ASC'
            ));
            $this->cache->save($cacheName, $provinces);
        }

        foreach ($provinces as $p) {
            $cacheName = md5(serialize(array(
                'HomeController',
                'provinceListAction',
                'District',
                'find',
                $p->id
            )));

            $district_array = array();
            $districts = $this->cache->get($cacheName);
            if (!$districts) {
                $districts = \ITECH\Data\Model\LocationModel::find(array(
                    'conditions' => 'parent_id = :parent_id:',
                    'bind' => array('parent_id' => $p->id),
                    'order' => 'ordering ASC, id ASC'
                ));
                $this->cache->save($cacheName, $districts);
            }

            if ($districts) {
                foreach ($districts as $d) {
                    $district_array[] = array(
                        'id' => (int)$d->id,
                        'name' => $d->name,
                        'slug' => $d->slug,
                        'project_count' => (int)$d->project_count
                    );
                }
            }

            $response['result'][] = array(
                'id' => (int)$p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'project_count' => (int)$p->project_count,
                'district' => $district_array,
                'total_districts' => count($district_array)
            );
        }

        $response['total_items'] = count($provinces);

        return parent::outputJSON($response);
    }

    public function categoryListAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $parent_id = $this->request->getQuery('parent_id', array('striptags', 'trim', 'int'), '');
        $status = $this->request->getQuery('status', array('striptags', 'trim', 'int'), \ITECH\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE);
        $not_id = $this->request->getQuery('no_id', array('striptags', 'trim', 'int'), '');
        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $params = array();

        if ($parent_id != '') {
            $params['conditions']['parent_id'] = $parent_id;
        }

        if ($not_id != '') {
            $params['conditions']['not_id'] = $not_id;
        }

        if ($module != '') {
            $params['conditions']['module'] = $module;
        }

        if ($status != '') {
            $params['conditions']['status'] = $status;
        }

        $cacheName = md5(serialize(array(
            'HomeController',
            'categoryList',
            'CategoryRepo',
            $params
        )));

        $categories = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$categories) {
            $categoryRepo = new \ITECH\Data\Repo\CategoryRepo();
            $categories = $categoryRepo->getList($params);
            
            if ($cache == 'true') {
                $this->cache->save($cacheName, $categories);
            }
        }

        if (count($categories)) {
            foreach ($categories as $item) {
                $default_image_url = parent::$noImageUrl;
                $image_icon_url = parent::$noImageUrl;

                if ($item['image'] != '') {
                    $default_image_url = $this->config->asset->frontend_url . 'upload/category/' . $item['image'];
                    $image_icon_url = $this->config->asset->frontend_url . 'upload/category/' . $item['icon'];
                }

                $response['result'][] = array(
                    'id' => (int)$item->id,
                    'name' => $item->name,
                    'middle_name' => $item->middle_name,
                    'slug' => $item->slug,
                    'article_count' => $item->article_count,
                    'default_image_url' => $default_image_url,
                    'image_icon_url' => $image_icon_url,
                    'status' => (int)$item->status,
                );
            }
        }

        parent::outputJSON($response);
    }

    public function categoryDetailAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('striptags', 'trim', 'int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'HomeController',
            'categoryDetailAction',
            'CategoryModel',
            'findFirst',
            $id
        )));

        $category = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$category) {
            $category = \ITECH\Data\Model\CategoryModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array(
                    'id' => $id
                )
            ));

            if ($cache == 'true') {
                $this->cache->save($cacheName, $category);
            }
        }

        if (!$category) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Error.',
                'result' => array()
            );

            goto RETURN_RESPONSE;
        }

        $response['result'] = array(
            'id' => (int)$category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'article_count' => $category->article_count
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function checkPhoneExistsAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => false
        );

        $search = $this->request->getQuery('search', array('trim', 'striptags'), '');
        $username = \ITECH\Data\Lib\Util::numberOnly($search);

        if ($username == '') {
            $response['result'] = false;
            goto RETURN_RESPONSE;
        }

        $has = \ITECH\Data\Model\UserModel::count(array(
            'conditions' => 'username = :username:',
            'bind' => array('username' => $username)
        ));

        if ($has > 0) {
            $response['result'] = true;
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function checkEmailExistsAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => false
        );

        $search = $this->request->getQuery('search', array('trim', 'striptags', 'lower'), '');

        if ($search == '') {
            $response['result'] = false;
            goto RETURN_RESPONSE;
        }

        $has = \ITECH\Data\Model\UserModel::count(array(
            'conditions' => 'email = :email:',
            'bind' => array('email' => $search)
        ));

        if ($has > 0) {
            $response['result'] = true;
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function fileJsonAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => false
        );

        $file_name = $this->request->getQuery('file_name', array('trim', 'striptags', 'lower'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        if ($file_name == '') {
            $response['result'] = false;
            goto RETURN_RESPONSE;
        } else {
            $file = ROOT . '/cache/data/json/' . $file_name . '.json';
            $cache_name = md5(serialize(array(
                'HomeController',
                'getFileJsonAction',
                $file
            )));

            $json = $cache == 'true' ? $this->cache->get($cache_name) : null;
            if (!$json) {
                $json = json_decode(file_get_contents($file), true);

                if ($cache == 'true') {
                    $this->cache->save($cache_name, $json);
                }
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $json
            );

            goto RETURN_RESPONSE;
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function attributeListAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $module = $this->request->getQuery('module', array('int'), '');
        $type = $this->request->getQuery('type', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'false');

        $attributes = array();

        $cacheName = md5(serialize(array(
            'HomeController',
            'attributeList',
            'AttributeModel',
            'find',
            $module,
            $type
        )));

        switch ($module) {
            case \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT:
                $attributes = $cache == 'true' ? $this->cache->get($cacheName) : null;
                if (!$attributes) {
                    $attributes = \ITECH\Data\Model\AttributeModel::find(array(
                        'conditions' => 'status = :status: AND type = :type:',
                        'bind' => array(
                            'status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
                            'type' => $type
                        ),
                        'order' => 'id ASC'
                    ));
                    
                    if ($cache == 'true') {
                        $this->cache->save($cacheName, $attributes);
                    }
                }
            break;

            case \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK:
                $attributes = $cache == 'true' ? $this->cache->get($cacheName) : null;
                if (!$attributes) {
                    $attributes = \ITECH\Data\Model\AttributeModel::find(array(
                        'conditions' => '(module = :module: OR module = :module1:)  AND status = :status: AND type = :type:',
                        'bind' => array(
                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
                            'module1' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                            'status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
                            'type' => $type,
                        ),
                        'order' => 'id ASC'
                    ));

                    if ($cache == 'true') {
                        $this->cache->save($cacheName, $attributes);
                    }
                }
            break;

            case \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT:
                $attributes = $cache == 'true' ? $this->cache->get($cacheName) : null;
                if (!$attributes) {
                    $attributes = \ITECH\Data\Model\AttributeModel::find(array(
                        'conditions' => 'module = :module: AND status = :status: AND type = :type:',
                        'bind' => array(
                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                            'status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
                            'type' => $type
                        ),
                        'order' => 'id ASC'
                    ));

                    if ($cache == 'true') {
                        $this->cache->save($cacheName, $attributes);
                    }
                }
            break;
        }

        if (count($attributes)) {
            foreach ($attributes as $item) {
                $response['result'][] = array(
                    'id' => (int)$item->id,
                    'name' => $item->name,
                );
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function apartmentValueAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $cacheName = md5(serialize(array(
            'HomeController',
            'apartmentValueAction',
            'ApartmentValueModel',
            'find',
            $id
        )));

        $apartmentValue = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$apartmentValue) {
            $apartmentValue = \ITECH\Data\Model\ApartmentValueModel::find(array(
                'conditions' => 'apartment_id = :apartment_id:',
                'bind' => array(
                    'apartment_id' => $id
                )
            ));

            if ($cache == 'true') {
                $this->cache->save($cacheName, $apartmentValue);
            }
        }

        if (!count($apartmentValue)) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Error.',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => array()
            );

            if (count($apartmentValue)) {
                foreach($apartmentValue as $item) {
                    $response['result'][] = array(
                        'apartment_id' => $item->apartment_id,
                        'attribute_id' => $item->attribute_id,
                        'value' => $item->value
                    );
                }
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}