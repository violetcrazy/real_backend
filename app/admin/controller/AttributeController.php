<?php
namespace ITECH\Admin\Controller;

class AttributeController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
        ));
    }

    public function listAttributeAjaxAction()
    {
        $module = $this->dispatcher->getParam('module_attr', array('striptags', 'trim'), '');
        $params = array('module' => $module);
        $params['conditions']['status'] = 'all';
        $attr = new \ITECH\Data\Repo\AttributeRepo();
        $attributes = $attr->getList($params);

        $typeAttr = \ITECH\Data\Lib\Constant::getApartmentAttributeType();
        $statusAttr = \ITECH\Data\Lib\Constant::getAttributeStatus();

        $out = array();
        if (count($attributes) > 0) {
            foreach ($attributes as $attribute) {
                $out['data'][] = array(
                    $attribute->id,
                    '<a class="edit-link fancybox-run" href="'. $this->url->get(array("for" => "load_attribute_edit_ajax", "id" => $attribute->id, "query" => "?module_attr=" . $module )) .'">'. $attribute->name .'</a>',
                    $attribute->name_eng,
                    isset($typeAttr[$attribute->type]) ? $typeAttr[$attribute->type] : '',
                    $statusAttr[$attribute->status],
                    '<div class="text-center">
                        <a href="'. $this->url->get(array('for' => 'load_attribute_delete', 'query' => '?' . http_build_query(array('attribute_id' => $attribute['id'])))) .' " onclick="javascript:return confirm(\'Đồng ý ẩn?\');" class="btn btn-xs btn-link tooltips" data-placement="top" data-original-title="Xóa">
                        Tạm ẩn
                        </a>
                    </div>',
                    '<div class="text-center">
                        <a href="'. $this->url->get(array('for' => 'load_attribute_delete', 'query' => '?' . http_build_query(array('attribute_id' => $attribute['id'], 'type' => 'forever')))) .' " onclick="javascript:return confirm(\'Đồng ý xoá VĨNH VIỄN?\');" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Xóa VĨNH VIỄN thuộc tính">
                        <i class="fa fa-times fa fa-white"></i>
                        </a>
                    </div>',
                );
            }
        }

        parent::outputJSON($out);
    }

    public function addAttributeAjaxAction()
    {
        $this->view->pick(parent::$theme . '/load/attribute_edit');
    }

    public function saveAttrAjaxAction()
    {
        if ($this->request->isPost()) {

            $name = $this->request->getPost('name', array('trim', 'striptags'), '');
            $name_eng = $this->request->getPost('name_eng', array('trim', 'striptags'), '');
            $type = $this->request->getPost('type');
            $module = $this->request->getPost('module_attr');

            $attrModel = new \ITECH\Data\Model\AttributeModel();
            if ($this->request->has('id')){
                $id = $this->request->getPost('id', array('trim', 'striptags', 'int'), 0);
                $attrDetail = $attrModel->findFirst($id);
            } else {

                $checkAttr = \ITECH\Data\Model\AttributeModel::findFirst(array(
                    'conditions' => 'name = :attr_name:',
                    'bind' => array('attr_name'=> $name)
                ));

                if ($checkAttr) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Tên thuộc tính đã tồn tại'
                    );
                    parent::outputJSON($response);
                }

                $attrDetail = new $attrModel();
                $attrDetail->slug = \ITECH\Data\Lib\Util::slug($name);
                $attrDetail->module = $module;
                $attrDetail->type = $type;
                $attrDetail->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE;
            }

            if ($name != '') {
                $attrDetail->name = $name;
            }
            if ($name_eng != '') {
                $attrDetail->name_eng = $name_eng;
            }
            if ($type != '') {
                $attrDetail->type = $type;
            }

            try {
                if (!$attrDetail->save()) {
                    $messages = $attrDetail->getMessages();
                    $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $m
                    );
                } else {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Success.',
                        'result' => $attrDetail
                    );
                }
            } catch (\Exception $e) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
            }

            parent::outputJSON($response);
        }
    }

    public function editAttributeAjaxAction()
    {
        $id = $this->dispatcher->getParam('id');
        $attrModel = new \ITECH\Data\Model\AttributeModel();
        $attrDetail = $attrModel->findFirst($id);

        $this->view->setVars(array(
            'attrDetail' => $attrDetail
        ));
        $this->view->pick(parent::$theme . '/load/attribute_edit');
    }

    public function deleteAttributeAction()
    {
        $attributeId = $this->request->getQuery('attribute_id', array('int'), '');
        $type = $this->request->getQuery('type', array('trim', 'striptags'), '');

        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
            'conditions' => 'id = :attribute_id:',
            'bind' => array('attribute_id' => $attributeId)
        ));

        if (!$attribute) {
            throw new \Exception('Không tồn tại thuộc tính này.');
        }

        if ($type == 'forever') {
            $attribute->delete();
            $this->flashSession->success('Xóa VĨNH VIỄN thành công.');
            return $this->response->redirect($this->request->getHTTPReferer());
        }

        $attribute->status = \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_REMOVED;
        $attribute->updated_at = date('Y-m-d H:i:s');

        try {
            if (!$attribute->save()) {
                $messages = $attribute->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa.';
                $this->flashSession->error($message);
            } else {
                $this->flashSession->success('Tạm ẩn thành công thuộc tính.');
            }

            return $this->response->redirect($this->request->getHTTPReferer());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
