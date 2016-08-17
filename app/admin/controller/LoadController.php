<?php
namespace ITECH\Admin\Controller;

class LoadController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();
    }

    public function blockAllAction()
    {
        $project_id = $this->request->getQuery('project_id', array('int'), 0);

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $url = $this->config->application->api_url . 'block/all';
        $get = array(
            'authorized_token' => $authorizedToken,
            'project_id' => $project_id
        );

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);
        parent::outputJSON($r);
    }
    public function districtAjaxAction()
    {
        $provinceId = $this->request->getQuery('province_id', array('int'), '');
        $districtId = $this->request->getQuery('district_id', array('int'), '');

        $districts = array();

        if ($provinceId != '') {
            $province = \ITECH\Data\Model\LocationModel::findFirst(array(
                'conditions' => 'id = :location_id: AND parent_id = :parent_id:',
                'bind' => array(
                    'location_id' => $provinceId,
                    'parent_id' => 0
                )
            ));

            if ($province) {
                $districtModel = \ITECH\Data\Model\LocationModel::find(array(
                    'conditions' => 'parent_id = :parent_id:',
                    'bind' => array('parent_id' => $province->id),
                    'order' => 'ordering ASC'
                ));

                foreach ($districtModel as $item) {
                    $districts[$item->id] = $item->name;
                }
            }
        }

        $this->view->setVars(array(
            'districts' => $districts,
            'districtId' => $districtId
        ));
        $this->view->pick(parent::$theme . '/load/district_ajax');
    }

    public function uploadImageAjaxAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        $typeUpload = $this->request->getQuery('type_upload', array('striptags', 'trim'), '');
        $typeUploadDefault = $this->request->getQuery('type_upload_default', array('striptags', 'trim'), '');

        if ($this->request->isPost()) {
            if ($typeUpload == '') {
                $typeUpload = $this->request->getPost('type_upload');
            }

            if ($typeUploadDefault == '') {
                $typeUploadDefault = $this->request->getPost('type_upload_default');
            }

            switch ($typeUpload) {
                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_DEFAULT:
                    $folder = 'project';
                    $size = 940;

                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_GALLERY:
                    $folder = 'project';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_MAP_IMAGE:
                    $folder = 'project';
                    $size = 940;

                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_ATTRIBUTE:
                    $folder = 'attribute';
                    $size = 20;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_CATEGORY:
                    $folder = 'category';
                    $size = 20;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_DEFAULT:
                    $folder = 'block';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_GALLERY:
                    $folder = 'block';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_MAP_IMAGE:
                    $folder = 'block';
                    $size = 940;
                break;
                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_ATTRIBUTE:
                    $folder = 'attribute';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_DEFAULT:
                    $folder = 'apartment';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_GALLERY:
                    $folder = 'apartment';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_ATTRIBUTE:
                    $folder = 'apartment';
                    $size = 940;
                break;

                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_ARTICLE_DEFAULT:
                    $folder = 'article';
                    $size = 120;
                break;
            }

            // Add for one site other ---------
            switch ($typeUploadDefault) {
                case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_CATEGORY_DEFAULT:
                    $folder = 'category';
                    $sizeDefault = 350;
                break;
            }

            // --------- for one site other
            if ($this->request->hasFiles()) {
                $file = $this->request->getUploadedFiles();

                if (isset($file[0])) {
                    $resource = array(
                        'name' => $file[0]->getName(),
                        'type' => $file[0]->getType(),
                        'tmp_name' => $file[0]->getTempName(),
                        'error' => $file[0]->getError(),
                        'size' => $file[0]->getSize()
                    );

                    $w = 0;
                    $h = 0;
                    $type = null;
                    $attr = null;
                    list($w, $h, $type, $attr) = getimagesize($file[0]->getTempName());

                    switch ($typeUpload) {
                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_DEFAULT:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }

                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_GALLERY:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_MAP_IMAGE:
                            $width = 940;

                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_PROJECT_ATTRIBUTE:
                            $width = 20;
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_CATEGORY:
                            $width = 20;
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_DEFAULT:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_GALLERY:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_MAP_IMAGE:
                            $width = 940;
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_BLOCK_ATTRIBUTE:
                            $width = 20;
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_DEFAULT:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_GALLERY:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_APARTMENT_ATTRIBUTE:
                            $width = 20;
                        break;

                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_ARTICLE_DEFAULT:
                            if (isset($w) && $w >= $size) {
                                $width = $size;
                            }
                        break;
                    }

                    // Add for one site other ---------
                    switch ($typeUploadDefault) {
                        case \ITECH\Data\Lib\Constant::TYPE_UPLOAD_IMAGE_CATEGORY_DEFAULT:
                            if (isset($w) && $w >= $sizeDefault) {
                                $width = $sizeDefault;
                            }
                        break;
                    }
                    // --------- Add for one site other

                    if (!isset($width)) {
                        $width = $w;
                    }

                    $imageFilename = '';
                    $r = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', $width, $resource);
                    if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                        $imageFilename = $r['result'];
                    }

                    parent::uploadImageToCdn(ROOT . '/web/admin/asset/upload/', $folder, $imageFilename);
                    parent::deleteImageFromLocal(ROOT . '/web/admin/asset/upload/', $imageFilename);

                    $file_name = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $imageFilename;
                    $image_url = $this->config->asset->frontend_url . 'upload/' . $folder . '/' . $file_name;
                    $default_thumbnail_url = $this->config->asset->frontend_url . 'upload/' . $folder . '/thumbnail/' . $file_name;
                    $image = $file_name;

                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Success.',
                        'result' => array(
                            'image_url' => $image_url,
                            'default_thumbnail_url' => $default_thumbnail_url,
                            'image' => $image
                        )
                    );
                }
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function deleteImageAjaxAction()
    {
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $image = $this->request->getQuery('image', array('striptags', 'trim'), '');
        $folder = $this->request->getQuery('folder', array('striptags', 'trim'), '');

        $get = array(
            'user_id' => $userSession['id'],
            'image' => $image,
            'authorized_token' => $authorizedToken,
            'folder' => $folder,
            'user_agent' => $this->request->getUserAgent(),
            'ip' => $this->request->getClientAddress()
        );

        $url = $this->config->application->api_url . 'user/delete-image';
        $response = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        return parent::outputJSON($response);
    }

    public function uploadImageEditorAjaxAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $file_url = $this->request->getPost('file_url', array('striptags', 'trim'), '');
                $imageFilename = $this->request->getPost('file_name_new', array('striptags', 'trim'), '');
                parent::uploadImageToCdn(ROOT . '/web/admin/' . $file_url, 'article', $imageFilename);
                parent::deleteImageFromLocal(ROOT . '/web/admin/' . $file_url, $imageFilename);
            }
        }

        parent::outputJSON($response);
    }

    public function linkSortAjaxAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $links = $this->request->getPost('link', null);

                foreach ($links as $key => $item) {
                    $link = \ITECH\Data\Model\LinkModel::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $item['id'])
                    ));

                    $link->ordering = $key;
                    $link->parent_id = 0;
                    if (!$link->update()) {
                        $message = $link->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể sắp xếp.');
                        }
                        break;
                    }

                    if (isset($item['children'])) {
                        $this->updateSort($item['children'], $item['id']);
                    }
                }
            }
        }

        $this->view->disable();
    }

    private function updateSort($links, $parent)
    {
        foreach ($links as $key => $item) {
            $link = \ITECH\Data\Model\LinkModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $item['id'])
            ));
            $link->ordering = $key;
            $link->parent_id = $parent;
            if (!$link->update()) {
                $message = $link->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể thêm.');
                }
            }
            if (isset($item['children'])) {
                $this->updateSort($item['children']);
            }
        }

        return true;
    }

    public function uploadImageUserAjaxAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        $typeUpload = $this->request->getQuery('type_upload', array('striptags', 'trim'), '');
        $user_id = $this->request->getQuery('user_id', array('striptags', 'trim'), '');

        if ($this->request->isPost()) {

            $folder = 'avatar';
            $size = (int)940;
            if ($this->request->hasFiles()) {
                $file = $this->request->getUploadedFiles();
                if (isset($file[0]))     {
                    $resource = array(
                        'name' => $file[0]->getName(),
                        'type' => $file[0]->getType(),
                        'tmp_name' => $file[0]->getTempName(),
                        'error' => $file[0]->getError(),
                        'size' => $file[0]->getSize()
                    );
                    list($width, $height, $type, $attr) = getimagesize($file[0]->getTempName());

                    $width = 200;
                    $imageFilename = '';
                    $r = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', $width, $resource);
                    if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                        $imageFilename = $r['result'];
                    }

                    $r = parent::uploadImageToCdn(ROOT . '/web/admin/asset/upload/', $folder, $imageFilename);
                    parent::deleteImageFromLocal(ROOT . '/web/admin/asset/upload/', $imageFilename);
                    $file_name = $imageFilename;
                    $image_url = $this->config->asset->frontend_url . 'upload/' . $folder . '/' . $file_name;
                    $default_thumbnail_url = $this->config->asset->frontend_url . 'upload/' . $folder . '/thumbnail/' . $file_name;
                    $image = $file_name;
                    $url = $this->config->application->api_url . 'user/upload-avatar?authorized_token=' . $authorizedToken;
                    $post = array(
                        'id' => $user_id,
                        'avatar' => $image_url
                    );
                    $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);

                    if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                            'message' => 'Success.',
                            'result' => array(
                                'image_url' => $image_url,
                                'default_thumbnail_url' => $default_thumbnail_url,
                                'image' => $image
                            )
                        );
                        $user = $r['result'];
                        parent::setUserSession($user);
                    } else {
                        $response = array(
                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                'message' => 'loi.',
                        );
                    }
                }
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}