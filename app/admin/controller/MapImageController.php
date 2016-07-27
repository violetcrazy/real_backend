<?php
namespace ITECH\Admin\Controller;

class MapImageController extends \ITECH\Admin\Controller\BaseController
{
    public function indexAction()
    {
        $mapImageId = $this->request->getQuery('map_image_id', array('striptags', 'trim', 'int'), 0);
        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst($mapImageId);

        if (!$mapImage) {
            throw new \Phalcon\Exception('Không tồn tại hình này.');
        }

        $subObj= array();
        $obj = array();
        $breadcrumbs = [];

        if ($mapImage->module == \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT) {
            $obj = \ITECH\Data\Model\ProjectModel::findFirst($mapImage->item_id);
            if (!$obj) {
                throw new \Phalcon\Exception('Không tồn tại Dự án này.');
            }

            $subObj = \ITECH\Data\Model\BlockModel::find(array(
                'conditions' => 'project_id = :project_id: AND status = :status:',
                'bind' => array(
                    'project_id' => $obj->id,
                    'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                )
            ));

            $breadcrumbs = [
                [
                    'title' => 'Dashboard',
                    'url' => $this->config->application->base_url,
                    'active' => false
                ],
                [
                    'title' => 'Danh sách dự án',
                    'url' => $this->url->get([
                        'for' => 'project_list'
                    ]),
                    'active' => false
                ],
                [
                    'title' => $obj->name,
                    'url' => $this->url->get([
                        'for' => 'block_edit',
                        'query' => '?id=' . $obj->id
                    ]),
                    'active' => false
                ],
                [
                    'title' => 'Vẽ map link',
                    'url' => '',
                    'active' => true
                ]
            ];
        } elseif ($mapImage->module == \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK) {
            $obj = \ITECH\Data\Model\BlockModel::findFirst($mapImage->item_id);
            if (!$obj) {
                throw new \Phalcon\Exception('Không tồn tại Block/Khu này.');
            }

            if (isset($mapImage->floor)) {
                $subObj = \ITECH\Data\Model\ApartmentModel::find(array(
                    'conditions' => 'block_id = :block_id: AND floor = :floor:',
                    'bind' => array(
                        'block_id' => $obj->id,
                        'floor' => $mapImage->floor
                    )
                ));
            }

            $project = \ITECH\Data\Model\ProjectModel::findFirst($obj->project_id);
            $breadcrumbs = [
                [
                    'title' => 'Dashboard',
                    'url' => $this->config->application->base_url,
                    'active' => false
                ],
                [
                    'title' => 'Danh sách dự án',
                    'url' => $this->url->get([
                        'for' => 'project_list'
                    ]),
                    'active' => false
                ],
                [
                    'title' => $project->name,
                    'url' => $this->url->get([
                        'for' => 'project_edit',
                        'query' => '?id=' . $project->id
                    ]),
                    'active' => false
                ],
                [
                    'title' => $obj->name,
                    'url' => $this->url->get([
                        'for' => 'block_edit',
                        'query' => '?id=' . $obj->id
                    ]),
                    'active' => false
                ],
                [
                    'title' => 'Vẽ map link',
                    'url' => '',
                    'active' => true
                ]
            ];
        }

        $mapPoint = \ITECH\Data\Model\MapModel::find(array(
            'conditions' => 'map_image_id = :map_image_id:',
            'bind' => array(
                'map_image_id' => $mapImage->id
            ),
            'order' => 'id DESC'
        ));

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'mapImage' => $mapImage,
            'mapPoint' => $mapPoint,
            'object' => $obj,
            'subObject' => $subObj
        ));

        $this->view->pick(parent::$theme . '/map_image/index');
    }

    public function addAjaxAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Chỉ phương thức POST',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            $validator = new \Phalcon\Validation();

            $validator->add('point', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu vẽ map link.'
            )));
            $validator->add('map_image_id', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập Id hình ảnh không để trống.'
            )));

            $messages = $validator->validate($post);
            if (count($messages)) {
                $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Vui lòng điền đầy đủ thông tin còn thiếu.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
            }

            $mapLink = new \ITECH\Data\Model\MapModel();
            $mapLink->point = $validator->getValue('point');
            $mapLink->map_image_id = $validator->getValue('map_image_id');
            if ($this->request->hasPost('item_id')) {
                $mapLink->item_id = $this->request->getPost('item_id');
            }

            try {
                if (!$mapLink->create()) {
                    $messages = $mapLink->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể tạo được Map link.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }
            } catch (\Phalcon\Exception $e) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
                goto RETURN_RESPONSE;
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'success',
                'result' => $mapLink
            );
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function updateAjaxAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Chỉ phương thức POST',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $id = $this->request->getPost('id', array('int', 'trim', 'striptags'), 0);
            $itemId = $this->request->getPost('item_id', array('int', 'trim', 'striptags'), 0);

            $mapLink = \ITECH\Data\Model\MapModel::findFirst($id);
            if (!$mapLink) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại Map link này: ' . $id,
                    'result' => array()
                );
                goto RETURN_RESPONSE;
            }

            $mapLink->item_id = $this->request->getPost('item_id');

            try {
                if (!$mapLink->update()) {
                    $messages = $mapLink->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không cập nhật được Map link.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }
            } catch (\Phalcon\Exception $e) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
                goto RETURN_RESPONSE;
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Cập nhật thành công.',
                'result' => $mapLink
            );
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function deleteAjaxAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Chỉ phương thức POST',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $id = $this->request->getPost('id', array('int', 'trim', 'striptags'), 0);

            $mapLink = \ITECH\Data\Model\MapModel::findFirst($id);
            if (!$mapLink) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tồn tại Map link này: ' . $id,
                    'result' => array()
                );
                goto RETURN_RESPONSE;
            }

            try {
                if (!$mapLink->delete()) {
                    $messages = $mapLink->getMessages();
                    if (isset($messages[0])) {
                        $error_message = $messages[0]->getMessage();
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $error_message
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, xóa được Map link.'
                        );
                    }

                    goto RETURN_RESPONSE;
                }
            } catch (\Phalcon\Exception $e) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
                goto RETURN_RESPONSE;
            }

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Xóa thành công.',
                'result' => $mapLink
            );
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }
}
