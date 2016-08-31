<?php
namespace ITECH\Admin\Controller;

class MediaController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::authenticateUser();
    }

    public function  addAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'no post.'
        );

        if ($this->request->isPost()) {
            $response['message'] = 'is Post';

            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }
            $validator = new \Phalcon\Validation();

            $validator->setFilters('name', array('striptags', 'trim', 'lower'));
            $validator->setFilters('type', array('striptags', 'trim', 'lower'));
            $validator->setFilters('title', array('striptags', 'trim'));
            $validator->setFilters('category_id', array('int'));
            $validator->setFilters('link', array('striptags', 'trim'));
            $validator->setFilters('description', array('striptags', 'trim'));
            $validator->setFilters('relative_path', array('striptags', 'trim'));
            $validator->setFilters('size', array('int'));
            $validator->setFilters('width', array('int'));
            $validator->setFilters('height', array('int'));

            $messValidation = $validator->validate($post);
            if (count($messValidation)) {
                $result = array();
                foreach ($messValidation as $mess) {
                    $result[$mess->getFiled()] = $mess->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
            }

            if ($validator->getValue('name')) {
                $media = \ITECH\Data\Model\MediaModel::findFirst(array(
                        'conditions' => 'name = :name:',
                        'bind' => array('name' => $validator->getValue('name')))
                );

                $_attr = json_encode(array(
                    'title' => $validator->getValue('title') ? $validator->getValue('title') : $validator->getValue('name'),
                    'description' => $validator->getValue('description') ? $validator->getValue('description') : $validator->getValue('name'),
                    'link' => $validator->getValue('link') ? $validator->getValue('link') : '#',
                    'width' => $validator->getValue('width') ? $validator->getValue('width') : 0,
                    'height' => $validator->getValue('height') ? $validator->getValue('height') : 0
                ));
                if (!$media) {
                    $media = new \ITECH\Data\Model\MediaModel();
                    $media->name = $validator->getValue('name');
                    $media->type = $validator->getValue('type');
                    $media->category_id = $validator->getValue('category_id') ? $validator->getValue('category_id') : 1;
                    $media->relative_path = $validator->getValue('relative_path');
                    $media->size = $validator->getValue('size');
                    $media->attribute = $_attr;
                } else {
                    $media->attribute = $_attr;
                    if ($validator->getValue('name')) {
                        $media->name = $validator->getValue('name');
                    }
                    if ($validator->getValue('type')) {
                        $media->type = $validator->getValue('type');
                    }
                    if ($validator->getValue('category_id')) {
                        $media->category_id = $validator->getValue('category_id');
                    }
                    if ($validator->getValue('relative_path')) {
                        $media->relative_path = $validator->getValue('relative_path');
                    }
                    if ($validator->getValue('size')) {
                        $media->size = $validator->getValue('size');
                    }

                }

                try {
                    if (!$media->save()) {
                        $messages = $media->getMessages();
                        $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $m
                        );
                    } else {
                        $_attr = json_decode($media->attribute, true);
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                            'message' => 'Success.',
                            'result' => array(
                                'id' => $media->id,
                                'name' => $media->name,
                                'type' => $media->type,
                                'created_at' => $media->created_at,
                                'attribute' => $_attr,
                                'size' => $media->size,
                                'relative_path' => $media->relative_path,
                                'category_id' => $media->category_id,
                                'url' => $this->config->cdn->dir_upload . $media->relative_path . '/' . $media->name,
                                'thumbnail' => $this->config->cdn->dir_upload . 'thumbnail/' . $media->relative_path . '/' . $media->name
                            )
                        );
                    }
                } catch (\Exception $e) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $e->getMessage()
                    );
                }

            }
        }

        RETURN_RESPONSE:
        parent::outputJSON($response);
    }

    public function  addFolderAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'no post.'
        );

        if ($this->request->isPost()) {
            $response['message'] = 'is Post';

            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }
            $validator = new \Phalcon\Validation();

            $validator->setFilters('name', array('striptags', 'trim'));

            $messValidation = $validator->validate($post);
            if (count($messValidation)) {
                $result = array();
                foreach ($messValidation as $mess) {
                    $result[$mess->getFiled()] = $mess->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
            }

            if ($validator->getValue('name')) {
                $folder = \ITECH\Data\Model\MediaTermModel::findFirst(array(
                        'conditions' => 'name = :name:',
                        'bind' => array('name' => $validator->getValue('name')))
                );

                if (!$folder) {
                    $folder = new \ITECH\Data\Model\MediaTermModel();
                    $folder->name = $validator->getValue('name');
                } else {
                    if ($validator->getValue('name')) {
                        $folder->name = $validator->getValue('name');
                    }
                }

                try {
                    if (!$folder->save()) {
                        $messages = $folder->getMessages();
                        $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $m
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                            'message' => 'Success.',
                            'result' => array(
                                'category_id' => $folder->category_id,
                                'name' => $folder->name,
                                'counter_media' => $folder->counter_media
                            )
                        );
                    }
                } catch (\Exception $e) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $e->getMessage()
                    );
                }

            }
        }

        RETURN_RESPONSE:
        parent::outputJSON($response);
    }

    public function  deleteFolderAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'no post.'
        );

        if ($this->request->isPost()) {

            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }
            $validator = new \Phalcon\Validation();

            $validator->setFilters('category_id', array('int'));
            $messValidation = $validator->validate($post);

            if ($validator->getValue('category_id') == 1) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error. Không thể xóa chuyên mục mặc định',
                    'result' => ''
                );

                goto RETURN_RESPONSE;
            }

            if (count($messValidation)) {
                $result = array();
                foreach ($messValidation as $mess) {
                    $result[$mess->getFiled()] = $mess->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Error.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
            }

            if ($validator->getValue('category_id')) {
                $folder = \ITECH\Data\Model\MediaTermModel::findFirst(array(
                        'conditions' => 'category_id = :category_id:',
                        'bind' => array('category_id' => $validator->getValue('category_id')))
                );

                if (!$folder) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Không tìm thấy folder này.'
                    );

                    goto RETURN_RESPONSE;

                } else {
                    try {
                        if (!$folder->delete()) {
                            $messages = $folder->getMessages();
                            $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa folder.';
                            $response = array(
                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                'message' => $m
                            );
                        } else {
                            $response = array(
                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                'message' => 'Success.'
                            );
                        }
                    } catch (\Exception $e) {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $e->getMessage()
                        );

                        $media = new \UPLOAD\Data\Model\MediaModel();
                        $sqlQuery = "UPDATE `media` SET `category_id` = '1' WHERE `category_id` = '". $validator->getValue('category_id') . "'";
                        $updateMedia = $media->getWriteConnection()->query($sqlQuery);
                        if ($updateMedia) {
                            $response['result']['update_media'] = 'Cập nhật media thành công';
                        }
                    }
                }

            }
        }

        RETURN_RESPONSE:
        parent::outputJSON($response);
    }
}
