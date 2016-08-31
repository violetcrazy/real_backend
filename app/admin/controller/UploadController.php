<?php
namespace ITECH\Admin\Controller;

class UploadController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::authenticateUser();
    }

    public function changeFolderUploadAction()
    {
        $category_id = $this->request->getPost('category_id', array('int'), 1);
        if ($category_id != '') {
            $this->session->set('CATEGORY_UPLOAD', $category_id);
        } else {
            $this->session->set('CATEGORY_UPLOAD', 1);
        }
        die;
    }

    public function indexAction()
    {
        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            if (count($files) > 0) {
                foreach ($files as $file) {
                    if (isset($file) && $file->getName() != '') {

                        if ($file->getError()) {
                            $response = array(
                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                'message' => 'Có lỗi từ file tải lên'
                            );
                            parent::outputJSON($response);
                        }

                        $resource = array(
                            'name' => $file->getName(),
                            'type' => $file->getType(),
                            'tmp_name' => $file->getTempName(),
                            'error' => $file->getError(),
                            'size' => $file->getSize(),
                            'extension' => $file->getExtension()
                        );
                        $response = $this->uploadMediaToCdn($resource);

                        if ($response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                            $category_id = ($this->session->has('CATEGORY_UPLOAD') ? $this->session->get('CATEGORY_UPLOAD') : 1);
                            $dataSave = array(
                                'name'          => $response['file_name'],
                                'type'          => $resource['type'],
                                'title'         => $response['file_name'],
                                'description'   => $response['file_name'],
                                'alt'           => $response['file_name'],
                                'size'          => $resource['size'],
                                'relative_path' => '' . date('Y') . '/' . date('m'),
                                'category_id'   => $category_id,
                            );
                            $url = $this->url->get(array('for' => 'add_media'));

                            $r = \ITECH\Data\Lib\Util::curlPost($url, $dataSave);
                            $responseSaveMedia = json_decode($r, true);
                            if ($responseSaveMedia['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR) {
                                $response['status'] = \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR;
                                $response['message'] = 'Lỗi khi lưu Media';
                                $response['r'] = $r;
                                $response['category_id'] = $category_id;
                            } else {
                                $response['result'] = $responseSaveMedia['result'];
                            }
                        }
                        parent::outputJSON($response);
                    }
                }
            }
        }
    }

    public function uploadMediaToCdn($resource)
    {
        $url = $this->config->cdn->upload_media_url;
        $content = file_get_contents($resource['tmp_name']);
        $post['resource'] = $resource;
        $post['content'] = $content;
        $_r = \ITECH\Data\Lib\Util::curlPost($url, $post);
        $r = json_decode($_r, true);

        if (!empty($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Upload thành công.',
                'file_name' => $r['file_name'],
                'r' => $_r
            );
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Lỗi, không thể upload.',
                'r' => $_r
            );
        }

        return $response;
    }
}
