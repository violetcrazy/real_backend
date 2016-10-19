<?php
namespace ITECH\Api\Controller;

class BaseController extends \Phalcon\Mvc\Controller
{
    public static $noImageUrl;

    public function initialize()
    {
        self::$noImageUrl = $this->config->asset->frontend_url . 'img/noimage.jpg?' . $this->config->asset->version;

        $cacheName = md5(serialize(array(
            'BaseController',
            'initialize',
            'OptionModel',
            'findFirst',
            'key_option',
            'no_image'
        )));

        $config = $this->cache->get($cacheName);
        if (!$config) {
            $config = \ITECH\Data\Model\OptionModel::findFirst(array(
                'conditions' => 'key_option = :key_option:',
                'bind' => array('key_option' => 'no_image')
            ));
            $this->cache->save($cacheName, $config);
        }

        if ($config && isset($config->value) && $config->value != '') {
            self::$noImageUrl = $this->config->cdn->dir_upload . $config->value;
        }
    }

    public function outputJSON($response)
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
        exit;
    }

    public function checkAuthorizedToken()
    {
        $authorizedToken = $this->request->getQuery('authorized_token', array('striptags', 'trim'), '');

        $authorized = array(
            md5(\ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WEB . $this->config->application->secret),
            md5(\ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_ANDROID . $this->config->application->secret),
            md5(\ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_IOS . $this->config->application->secret),
            md5(\ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WINPHONE . $this->config->application->secret)
        );

        if (!in_array($authorizedToken, $authorized)) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Authorized token không hợp lệ.'
            );

            return $this->outputJSON($response);
        }
    }

    public function deleteImageFromLocal($dir, $file_name)
    {
        $response = array();

        if (is_dir($dir)) {
            $file = $dir . $file_name;

            if (file_exists($file)) {
                @chmod($file, 0777);

                if (@unlink($file)) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Xóa hình ảnh thành công.'
                    );
                } else {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không thể xóa hình ảnh.'
                    );
                }
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại hình ảnh.'
                );
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function uploadImageToCdn($local_dir, $remote_folder, $file_name)
    {
        $response = array();

        if (is_dir($local_dir)) {
            $file = $local_dir . $file_name;

            if (!file_exists($file)) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại hình ảnh.'
                );

                return $response;
            }

            $content = file_get_contents($file);

            $url = $this->config->cdn->upload_image_url;
            $post = array(
                'content' => $content,
                'folder' => $remote_folder,
                'filename' => $file_name
            );

            $r = \ITECH\Data\Lib\Util::curlPost($url, $post);
            $r = json_decode($r, true);

            if (!empty($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Upload thành công.'
                );
            } else {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Lỗi, không thể upload.'
                );
            }
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function deleteImageFromCdn($remote_folder, $file_name)
    {
        $response = array();

        $url = $this->config->cdn->delete_image_url;
        $get = array(
            'folder' => $remote_folder,
            'filename' => $file_name
        );

        $r = \ITECH\Data\Lib\Util::curlGet($url, $get);
        $r = json_decode($r, true);

        if (!empty($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Xóa thành công.'
            );
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không thể xóa.'
            );
        }

        return $response;
    }

    public function getAttrProject($type, $projectID, $cache = 'true')
    {
        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        $params = array(
            'conditions' => array(
                'type'       => $type,
                'project_id' => $projectID
            )
        );

        $cacheName = md5(serialize(array(
            'BaseController',
            'getAttrProject',
            'attributeRepo',
            'getListByProject',
            $params
        )));

        $attributeResult = ($cache == 'true') ? $this->cache->get($cacheName) : null;

        if (!$attributeResult) {
            $attributeResult = $attributeRepo->getListByProject($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $attributeResult);
            }
        }

        $attributeOutput = array();

        if (count($attributeResult)) {
            foreach ($attributeResult as $a) {
                $attributeOutput[] = array(
                    'id'       => (int)$a['id'],
                    'name'     => $a['name'],
                    'name_eng' => $a['name_eng']
                );
            }
        }

        return $attributeOutput;
    }

    public function getAttrBlock($type, $blockID, $cache = 'true')
    {
        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        $params = array(
            'conditions' => array(
                'type' => $type,
                'block_id' => $blockID,
            )
        );

        $cacheName = md5(serialize(array(
            'BaseController',
            'getAttrBlock',
            'attributeRepo',
            'getListByBlock',
            $params
        )));

        $attributeResult = false;
        if ($cache != 'false') {
            $attributeResult = $this->cache->get($cacheName);
        }

        if (!$attributeResult) {
            $attributeResult = $attributeRepo->getListByBlock($params);

            if ($cache != 'false') {
                $this->cache->save($cacheName, $attributeResult);
            }
        }

        $attributeOutput = array();
        if (count($attributeResult)) {
            foreach ($attributeResult as $a) {

                $attributeOutput[] = array(
                    'id' => (int)$a['id'],
                    'name' => $a['name'],
                    'name_eng' => $a['name_eng']
                );
            }
        }

        return $attributeOutput;
    }

    public function getAttrApartment($apartmentId, $cache = 'true')
    {
        $cacheName = md5(serialize([
            'BaseController',
            'getAttrApartment',
            'ApartmentAttributeModel',
            'execute',
            \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
            $apartmentId
        ]));

        $result = ($cache == 'true') ? $this->cache->get($cacheName) : null;

        if (!$result) {
            $apartmentAttributeModel = new \ITECH\Data\Model\ApartmentAttributeModel();

            $b = $apartmentAttributeModel->getModelsManager()->createBuilder();
            $b->columns(array(
                'a2.id AS attribute_id',
                'a2.name AS attribute_name',
                'a2.name_eng AS attribute_name_eng',
                'a2.type AS attribute_type'
            ));

            $b->from(array('aa1' => 'ITECH\Data\Model\ApartmentAttributeModel'));
            $b->innerJoin('ITECH\Data\Model\AttributeModel', 'a2.id = aa1.attribute_id', 'a2');
            $b->innerJoin('ITECH\Data\Model\ApartmentModel', 'a1.id = aa1.apartment_id', 'a1');

            $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));
            $b->andWhere('a1.id = :apartment_id:', array('apartment_id' => $apartmentId));

            $result = $b->getQuery()->execute();

            if ($cache == 'true') {
                $this->cache->save($cacheName, $result);
            }
        }

        $arrayType = array();
        $arrayView = array();
        $arrayUtility = array();
        $typeAttr = \ITECH\Data\Lib\Constant::getApartmentAttributeType();

        if (count($result)) {
            foreach ($result as $item) {
                if ($item['attribute_type'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE) {
                    $item->attribute_type_text = $typeAttr[$item->attribute_type];
                    $arrayType[] = array(
                        'id'       => $item->attribute_id,
                        'name'     => $item->attribute_name,
                        'name_eng' => $item->attribute_name_eng
                    );
                }

                if ($item['attribute_type'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW) {
                    $item->attribute_type_text = $typeAttr[$item->attribute_type];
                    $arrayView[] = array(
                        'id'       => $item->attribute_id,
                        'name'     => $item->attribute_name,
                        'name_eng' => $item->attribute_name_eng
                    );
                }

                if ($item['attribute_type'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY) {
                    $item->attribute_type_text = $typeAttr[$item->attribute_type];
                    $arrayUtility[] = array(
                        'id'       => $item->attribute_id,
                        'name'     => $item->attribute_name,
                        'name_eng' => $item->attribute_name_eng
                    );
                }
            }
        }

        return array(
            'type' => $arrayType,
            'view' => $arrayView,
            'utility' => $arrayUtility
        );
    }

    public function getAttrApartmentFull($type, $apartmentId, $language = 1, $cache = 'true')
    {
        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();
        $params = array(
            'conditions' => array(
                'type' => $type,
                'apartment_id' => $apartmentId,
                'language' => $language
            )
        );

        $cacheName = md5(serialize(array(
            'BaseController',
            'getAttrApartmentFull',
            $params
        )));

        $attributeResult = $cache == 'true' ? $this->cache->get($cacheName) : null;

        if (!$attributeResult) {
            $attributeResult = $attributeRepo->getListByApartment($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $attributeResult);
            }
        }

        $attributeOutput = array();
        if (count($attributeResult)) {
            foreach($attributeResult as $item) {
                $attributeOutput[] = array(
                    'name' => $item->name
                );
            }
        }
        return $attributeOutput;
    }

    public function setAttrBlock($attrType, $block, $type)
    {
        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        if ($attrType != '') {
            $params = array(
                'conditions' => array(
                    'type' => $type,
                    'block_id' => $block->id
                )
            );

            $attributeType = $attributeRepo->getListByBlock($params);

            $_type = array();
            if (count($attributeType)) {
                foreach ($attributeType as $item) {
                    $_type[] = $item->id;
                }
            }

            $propertyTypes = explode(',', $attrType);
            if (count($propertyTypes)) {
                foreach ($propertyTypes as $item) {
                    if ($item != '') {
                        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                            'conditions' => 'name = :name:
                                AND type = :type:
                                AND (module = :module:
                                OR module = :module1:)',
                            'bind' => array(
                                'name' => $item,
                                'type' => $type,
                                'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
                                'module1' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                            )
                        ));
                        if ($attribute) {
                            if (!in_array($attribute->id, $_type)) {
                                $blockAttribute = new \ITECH\Data\Model\BlockAttributeModel();
                                $blockAttribute->block_id = $block->id;
                                $blockAttribute->attribute_id = $attribute->id;
                                $blockAttribute->save();
                            } else {
                                if (count($_type)) {
                                    foreach ($_type as $k => $v) {
                                        if ($v == $attribute->id) {
                                            unset($_type[$k]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (count($_type)) {
                    $blockAttribute = new \ITECH\Data\Model\BlockAttributeModel();

                    $q = 'DELETE FROM `land_block_attribute`
                        WHERE `attribute_id` IN (' . implode(',', $_type) . ')';
                    $blockAttribute->getWriteConnection()->query($q);
                }
            }
        } else {
            $blockAttribute = new \ITECH\Data\Model\BlockAttributeModel();

            $q = 'DELETE `ba1`
                FROM `land_block_attribute` AS `ba1`
                INNER JOIN `land_attribute` AS `a1` ON `a1`.`id` = `ba1`.`attribute_id`
                WHERE
                    `ba1`.`block_id` = "' . $block->id . '"
                    AND `a1`.`type` = "' . $type . '"';
            $blockAttribute->getWriteConnection()->query($q);
        }
    }

    public function setAttrApartment($attrType, $apartment, $type, $language = 1)
    {
        $attributeRepo = new \ITECH\Data\Repo\AttributeRepo();

        $params = array(
            'conditions' => array(
                'type' => $type,
                'language' => $language,
                'apartment_id' => $apartment->id
            )
        );

        $attributeType = $attributeRepo->getListByApartment($params);
        $_type = array();
        if (count($attributeType)) {
            foreach ($attributeType as $item) {
                $_type[] = $item->id;
            }
        }

        $propertyTypes = array_filter(explode(',', $attrType));
        if (count($propertyTypes)) {
            foreach ($propertyTypes as $item) {
                $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                    'conditions' => 'slug = :slug:
                        AND language = :language:
                        AND type = :type:
                        AND module = :module:',
                    'bind' => array(
                        'slug' => \ITECH\Data\Lib\Util::slug($item),
                        'language' => $language,
                        'type' => $type,
                        'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
                    )
                ));

                if ($attribute) {
                    if (!in_array($attribute->id, $_type)) {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                        $apartmentAttribute->apartment_id = $apartment->id;
                        $apartmentAttribute->attribute_id = $attribute->id;
                        $apartmentAttribute->create();
                    } else {
                        foreach ($_type as $k => $v) {
                            if ($v == $attribute->id) {
                                unset($_type[$k]);
                            }
                        }
                    }
                } else {
                    $attribute = new \ITECH\Data\Model\AttributeModel();
                    $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                    $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                    $attribute->type = $type;
                    $attribute->language = $language;
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

            if (count($_type)) {
                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                $q = 'DELETE FROM `land_apartment_attribute`
                    WHERE `attribute_id` IN (' . implode(',', $_type) . ')';
                $apartmentAttribute->getWriteConnection()->query($q);
            }
        }
    }

    public function getCountApartmentByBlock($block, $conditions)
    {
        $cacheName = md5(serialize(array(
            'BaseController',
            'getCountApartmentByBlock',
            'count',
            $block->id,
            $conditions
        )));

        $output = $this->cache->get($cacheName);
        if (!$output) {
            $output = \ITECH\Data\Model\ApartmentModel::count(array(
                'conditions' => 'block_id = :block_id: AND condition = :condition: AND status = :status:',
                'bind' => array(
                    'block_id' => $block->id,
                    'condition' => $conditions,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));

            $this->cache->save($cacheName, $output);
        }

        return $output;
    }

    public function getCeriterial($ceriterial, $language = 1)
    {
        $prefix = '';
        if ($language == 2) {
            $prefix = '_eng';
        }
        $array = array_filter(explode('-', $ceriterial));
        $output = array();
        if (count($array)) {
            $i = 0;

            foreach ($array as $at) {
                $cacheName = md5(serialize(array(
                    'ApartmentController',
                    'ceriterialAction',
                    'AttributeModel',
                    'findFirst',
                    $at,
                    $prefix
                )));

                $attribute = $this->cache->get($cacheName);
                if (!$attribute) {
                    $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $at)
                    ));

                    $this->cache->save($cacheName, $attribute);
                }

                if ($attribute) {
                    $output['att_type'. $prefix .'[' . $i . ']'] = $attribute->id;
                    $i++;
                }
            }
        }

        return $output;
    }

    public function saveAttrProject($attr, $id, $type, $language = 1)
    {
        $attr = explode(',', $attr);

        if (count($attr)) {
            foreach($attr as $item) {
                $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                    'conditions' => 'slug = :slug:
                        AND language = :language:
                        AND type = :type:
                        AND module = :module:',
                    'bind' => array(
                        'slug' => \ITECH\Data\Lib\Util::slug($item),
                        'language' => $language,
                        'type' => $type,
                        'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                    )
                ));

                if ($attribute) {
                    $projectAttribute = \ITECH\Data\Model\ProjectAttributeModel::findFirst(array(
                        'conditions' => 'project_id = :project_id: AND attribute_id = :attribute_id:',
                        'bind' => array(
                            'project_id' => $id,
                            'attribute_id' => $attribute->id
                        )
                    ));

                    if (!$projectAttribute) {
                        $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                        $projectAttribute->project_id = $id;
                        $projectAttribute->attribute_id = $attribute->id;
                        $projectAttribute->save();
                    }
                } else {
                    $attribute = new \ITECH\Data\Model\AttributeModel();
                    $attribute->name = \ITECH\Data\Lib\Util::removeJunkSpace($item);
                    $attribute->slug = \ITECH\Data\Lib\Util::slug($attribute->name);
                    $attribute->type = $type;
                    $attribute->language = $language;
                    $attribute->is_search = \ITECH\Data\Lib\Constant::ATTRIBUTE_IS_SEARCH_YES;
                    $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
                    $attribute->module = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT;

                    if ($attribute->save()) {
                        $projectAttribute = \ITECH\Data\Model\ProjectAttributeModel::findFirst(array(
                            'conditions' => 'project_id = :project_id: AND attribute_id = :attribute_id:',
                            'bind' => array(
                                'project_id' => $id,
                                'attribute_id' => $attribute->id
                            )
                        ));

                        if (!$projectAttribute) {
                            $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                            $projectAttribute->project_id = $id;
                            $projectAttribute->attribute_id = $attribute->id;
                            $projectAttribute->save();
                        }
                    }
                }
            }
        }
    }
}
