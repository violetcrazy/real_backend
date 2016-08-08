<?php
namespace ITECH\Admin\Controller;

class SystemController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));
    }

    public function indexAction()
    {
        $params = array('conditions' => array('file_name' => 'system_config'));
        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $config = $loadComponent->getFileJson($params);

        $form = new \ITECH\Admin\Form\SystemConfigForm();
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $config = array(
                    'meta_title' => trim(strip_tags($this->request->getPost('meta_title'))),
                    'meta_description' => trim(strip_tags($this->request->getPost('meta_description'))),
                    'meta_keywords' => trim(strip_tags($this->request->getPost('meta_keywords')))
                );

                $fp = fopen(ROOT . '/cache/data/json/system_config.json', 'w+') or die('Error, opening output file.');
                fwrite($fp, json_encode($config, JSON_UNESCAPED_UNICODE));
                fclose($fp);

                $this->flashSession->success('Cập nhật thành công.');
                return $this->response->redirect(array('for' => 'system'));
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Cấu hình SEO',
                'url' => $this->url->get([
                    'for' => 'system_seo',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'config' => $config,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/system/index');
    }

    public function emailAction()
    {
        $params = array('conditions' => array('file_name' => 'system_email'));
        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $config = $loadComponent->getFileJson($params);

        $form = new \ITECH\Admin\Form\SystemEmailForm();
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $config = array(
                    'host' => trim(strip_tags($this->request->getPost('host'))),
                    'port' => trim(strip_tags($this->request->getPost('port'))),
                    'username' => trim(strip_tags($this->request->getPost('username'))),
                    'password' => trim(strip_tags($this->request->getPost('password')))
                );

                $fp = fopen(ROOT . '/cache/data/json/system_email.json', 'w+') or die('Error, opening output file.');
                fwrite($fp, json_encode($config, JSON_UNESCAPED_UNICODE));
                fclose($fp);

                $this->flashSession->success('Cập nhật thành công.');
                return $this->response->redirect(array('for' => 'system_email'));
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Cấu hình Email',
                'url' => $this->url->get([
                    'for' => 'system_email',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'config' => $config,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/system/email');
    }

    public function optionAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        if ($this->request->isPost()) {
            $options = $this->request->getPost();

            if (count($options)) {
                foreach ($options as $key => $value) {
                    $url = $this->config->application->api_url . 'option/add?authorized_token=' . $authorizedToken;
                    $params = array(
                        'key_option' => $key,
                        'value' => $value
                    );
                    \ITECH\Data\Lib\Util::curlPostJson($url, $params);
                }
            }

            $this->flashSession->success('Cập nhật thành công.');
            return $this->response->redirect(array('for' => 'system_option'));
        }

        $response = array();
        $url = $this->config->application->api_url . 'option/list?cache=false&authorized_token=' . $authorizedToken;
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);

        if (isset($r['status']) && $r['status'] == 200 && isset($r['result']) && count($r['result'])) {
            $response = $r['result'];
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Tùy chỉnh',
                'url' => $this->url->get([
                    'for' => 'system_option',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
           'response' => $response
        ));
        $this->view->pick(parent::$theme . '/system/option');
    }

    public function dataAction()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');
        $url = $this->config->application->api_url . 'project/add?authorized_token=' . $authorizedToken;
        $response = array();

        $project_id = $this->request->getPost('project_id', array('int'), 0);
        $block_id = $this->request->getPost('block_id', array('int'), 0);
        $reqType = $this->request->getPost('type',array('striptags', 'trim'), '');
        $types = array('project', 'block', 'apartment', 'update_apartment');
        $type = $reqType;

        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();

            if (count($files) > 0) {
                if (!in_array($reqType, $types)) {
                    throw new \Phalcon\Exception('Lỗi. Chỉ hỗ trợ nhập vào Dự Án, Block, sản phẩm.');
                } else {

                    switch ($reqType) {
                        case 'block':
                            if ($project_id == 0) {
                                throw new \Phalcon\Exception('Lỗi. Để import block vui lòng chọn dự án');
                            } else {
                                $url = $this->config->application->api_url . 'block/add?project_id=' . $project_id . '&authorized_token=' . $authorizedToken;
                            }
                            break;

                        case 'apartment':
                            if ($project_id == 0 || $block_id == 0) {
                                throw new \Phalcon\Exception('Lỗi. Để import sản phẩm vui lòng chọn dự án và block');
                            } else {
                                $url = $this->config->application->api_url . 'apartment/add?block_id=' . $block_id . '&authorized_token=' . $authorizedToken;
                            }
                            break;

                        case 'update_apartment':
                            $type = 'apartment';
                            break;

                        default:
                            break;
                    }

                    foreach ($files as $file) {
                        if (isset($file) && $file->getName() != '') {
                            $tmpFile = $file->getTempName();
                            $excel = new \ITECH\Data\Lib\Excel();

                            $posts = $excel->importData($tmpFile, $type);
                            if (count($posts) > 0) {
                                $i = 1;

                                foreach ($posts as $key => $dataPost) {
                                    $dataPost['created_by'] = $userSession['id'];
                                    $dataPost['status'] = \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE;

                                    if ($type == 'block') {
                                        $dataPost['project_id'] = $project_id;
                                    }

                                    $_id = isset($dataPost['id']) ? (int)$dataPost['id'] : '';
                                    if ($reqType == 'update_apartment' && $_id != '') {
                                        $url = $this->config->application->api_url . 'apartment/detail?id=' . $_id . '&authorized_token=' . $authorizedToken;
                                    }

                                    if ($reqType == 'apartment') {
                                        $dataPost['block_id'] = $block_id;
                                        unset($dataPost['id']);
                                    }

                                    $response[$i]['name'] = $dataPost['name'];
                                    $response[$i]['key'] = $key;
                                    $dataPost = json_encode($dataPost);

                                    $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $dataPost), true);

                                    $response[$i]['info'] = $r;
                                    $response[$i]['id'] = $_id;

                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
        }

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $url = $this->config->application->api_url . 'location/list?authorized_token=' . $authorizedToken;
        $cacheName = md5(serialize(array(
            'Location',
            'Api',
            $url
        )));

        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName, $r);
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Nhập/Xuất Excel',
                'url' => $this->url->get([
                    'for' => 'system_data',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'type' => $reqType,
            'project_id' => $project_id,
            'block_id' => $block_id,
            'response' => $response,
            'location' => $r,
            'projects' => $projects
        ));
        $this->view->pick(parent::$theme . '/system/data');
    }

    public function postImportProjectAction()
    {
        if ($this->request->isGet()) {
            $this->response->redirect($this->url->get(array('for'=> 'system_data')));
        }

        require_once ROOT . '/vendor/excel/PHPExcel.php';
        require_once ROOT . '/vendor/excel/PHPExcel/IOFactory.php';

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');
        $response = array('status' => '200');

        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            $response = array();
            foreach ($files as $file) {
                if (isset($file) && $file->getName() != '') {
                    $tmpFile = $file->getTempName();
                    $objPHPExcel = \PHPExcel_IOFactory::load($tmpFile);
                    $index = 0;
                    $keyMap = array(
                        '',
                        'name',
                        'name_eng',
                        'address',
                        'address_eng',
                        'province_id',
                        'district_id',
                        'description',
                        'description_eng',
                        'default_image',
                        'images',
                        'property_type',
                        'property_view',
                        'property_utility',
                        'direction',
                        'area', //Tổng diện tích
                        'space', //Diện tích cây xanh
                        'block_count',
                        'apartment_count',
                        'meta_title',
                        'meta_title_eng',
                        'meta_keywords',
                        'meta_keywords_eng',
                        'meta_description',
                        'meta_description_eng',
                    );

                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        if ($index != 0) {
                            break;
                        }
                        $index ++;

                        foreach ($worksheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            $i = 0;
                            if ($row->getRowIndex() >= 2){
                                $project = new \ITECH\Data\Model\ProjectModel();
                                $data = array();
                                foreach ($cellIterator as $cell) {
                                    if ($i >= count($keyMap)) {
                                        break;
                                    }

                                    if ($i == 0 && is_null($cell->getCalculatedValue())) {
                                        break;
                                    }

                                    if (!is_null($cell) && $i > 0) {
                                        $value = $cell->getCalculatedValue();
                                        if ($keyMap[$i] == 'images') {
                                            $data[$keyMap[$i]] = explode(',', $value);
                                        } else {
                                            $data[$keyMap[$i]] = $value;
                                        }
                                    }

                                    $i ++;
                                }

                                $projectDetail = \ITECH\Data\Model\ProjectModel::findFirst(array(
                                   'conditions' => 'name = :name: AND status = :status:',
                                    'bind' => array(
                                        'name' => $data['name'],
                                        'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
                                    )
                                ));
                                if ($projectDetail) {
                                    $response[$row->getRowIndex()] = array(
                                        'name' => $data['name'],
                                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                        'info' => array('Tên dự án đã tồn tại')
                                    );
                                } else {
                                    $project->name = $data['name'];
                                    $project->name_eng = $data['name_eng'];
                                    $project->address = $data['address'];
                                    $project->address_eng = $data['address_eng'];
                                    $project->province_id = $data['province_id'];
                                    $project->district_id = $data['district_id'];
                                    $project->description = $data['description'];
                                    $project->description_eng = $data['description_eng'];
                                    $project->default_image = $data['default_image'];
                                    $project->direction = $data['direction'];
                                    $project->total_area = $data['area'];
                                    $project->green_area = $data['space'];
                                    $project->block_count = $data['block_count'];
                                    $project->apartment_count = $data['apartment_count'];
                                    $project->meta_title = $data['meta_title'];
                                    $project->meta_title_eng = $data['meta_title_eng'];
                                    $project->meta_keywords = $data['meta_keywords'];
                                    $project->meta_keywords_eng = $data['meta_keywords_eng'];
                                    $project->meta_description = $data['meta_description'];
                                    $project->meta_description_eng = $data['meta_description_eng'];

                                    $project->created_by = $userSession['id'];
                                    $project->status = \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE;
                                    $project->updated_by = $userSession['id'];
                                    $project->updated_at = date('Y-m-d H:i:s');
                                    $project->created_at = date('Y-m-d H:i:s');
                                    $project->slug = \ITECH\Data\Lib\Util::slug($data['name']);
                                    $project->slug_eng = \ITECH\Data\Lib\Util::slug($data['name_eng']);

                                    if ($project->create()) {
                                        // IMAGE
                                        $messageImage = array();
                                        if ($project->id && isset($data['images'])) {
                                            $_images = array();
                                            foreach ($data['images'] as $_image) {
                                                $_images[] = array(
                                                    'image' => $_image,
                                                    'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_GALLERY,
                                                    'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                                );
                                            }
                                            $_images[] = array(
                                                'image' => $data['default_image'],
                                                'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_THUMBNAIL,
                                                'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                            );
                                            $messageImage = parent::saveMapImage(array(
                                                'images' => $_images,
                                                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
                                                'item_id' => (int)$project->id
                                            ));
                                        }
                                        // IMAGE

                                        //ATTR
                                        $attributes = array();

                                        $attributes = array_merge($attributes, parent::saveAttr(array(
                                            'value' => isset($data['property_type']) ? $data['property_type'] : '',
                                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                                        )));

                                        $attributes = array_merge($attributes, parent::saveAttr(array(
                                            'value' => isset($data['property_view']) ? $data['property_view'] : '',
                                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                                        )));

                                        $attributes = array_merge($attributes, parent::saveAttr(array(
                                            'value' => isset($data['property_utility']) ? $data['property_utility'] : '',
                                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                                        )));

                                        if (isset($attributes) && count($attributes)) {
                                            foreach ($attributes as $item) {
                                                $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                                                $projectAttribute->project_id = $project->id;
                                                $projectAttribute->attribute_id = $item;
                                                $projectAttribute->save();
                                            }
                                        }
                                        //ATTR

                                        $response[$row->getRowIndex()] = array(
                                            'name' => $project->name,
                                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                            'info' => $project,
                                        );
                                    } else {
                                        $messages = $project->getMessages();
                                        $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                                        $response[$row->getRowIndex()] = array(
                                            'name' => $project->name,
                                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                            'info' => $message
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $url = $this->config->application->api_url . 'location/list?authorized_token=' . $authorizedToken;
        $cacheName = md5(serialize(array(
            'Location',
            'Api',
            $url
        )));

        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName, $r);
        }

        $this->view->setVars(array(
            'responseProject' => $response,
            'location' => $r,
            'projects' => $projects
        ));
        $this->view->pick(parent::$theme . '/system/data');
    }

    public function postImportBlockAction()
    {
        if ($this->request->isGet()) {
            $this->response->redirect($this->url->get(array('for'=> 'system_data')));
        }

        require_once ROOT . '/vendor/excel/PHPExcel.php';
        require_once ROOT . '/vendor/excel/PHPExcel/IOFactory.php';

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');
        $response = array('status' => '200');
        $projectId = $this->request->getPost('project_id', array('trim', 'int', 'striptags'), 0);

        $projectDetail = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'status = :status: AND id = :id:',
            'bind' => array(
                'id' => $projectId,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        ));
        if (!$projectDetail) {
            throw new \Phalcon\Exception('Dự án không tồn tại');
        }

        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            $response = array();
            foreach ($files as $file) {
                if (isset($file) && $file->getName() != '') {
                    $tmpFile = $file->getTempName();
                    $objPHPExcel = \PHPExcel_IOFactory::load($tmpFile);
                    $index = 0;
                    $keyMap = array(
                        '',
                        'name',
                        'name_eng',
                        'price',
                        'price_eng',
                        'description',
                        'description_eng',
                        'shortname',
                        'floor_count',
                        'apartment_count',
                        'default_image',
                        'images',
                        'direction',
                        'total_area',
                        'green_area',
                        'property_type',
                        'property_view',
                        'property_utility',
                        'policy',
                        'policy_eng',
                        'meta_title',
                        'meta_title_eng',
                        'meta_keywords',
                        'meta_keywords_eng',
                        'meta_description',
                        'meta_description_eng',
                    );

                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        if ($index != 0) {
                            break;
                        }
                        $index ++;

                        foreach ($worksheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            $i = 0;
                            if ($row->getRowIndex() >= 2){
                                $block = new \ITECH\Data\Model\BlockModel();
                                $data = array();
                                foreach ($cellIterator as $cell) {
                                    if ($i >= count($keyMap)) {
                                        break;
                                    }

                                    if ($i == 0 && is_null($cell->getCalculatedValue())) {
                                        break;
                                    }

                                    if (!is_null($cell) && $i > 0) {
                                        $value = $cell->getCalculatedValue();
                                        if ($keyMap[$i] == 'images') {
                                            $data[$keyMap[$i]] = explode(',', $value);
                                        } else {
                                            $data[$keyMap[$i]] = $value;
                                        }
                                    }

                                    $i ++;
                                }


                                $blockDetail = \ITECH\Data\Model\BlockModel::findFirst(array(
                                   'conditions' => 'name = :name: AND status = :status: AND project_id = :project_id:',
                                    'bind' => array(
                                        'name' => $data['name'],
                                        'project_id' => $projectDetail->id,
                                        'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
                                    )
                                ));

                                if ($blockDetail) {
                                    $response[$row->getRowIndex()] = array(
                                        'name' => $data['name'],
                                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                        'info' => array('Tên dự án đã tồn tại')
                                    );
                                } else {
                                    $block->name = $data['name'];
                                    $block->name_eng = $data['name_eng'];
                                    $block->price = $data['price'];
                                    $block->price_eng = $data['price_eng'];
                                    $block->description = $data['description'];
                                    $block->description_eng = $data['description_eng'];
                                    $block->shortname = $data['shortname'];
                                    $block->floor_count = $data['floor_count'];
                                    $block->apartment_count = $data['apartment_count'];
                                    $block->default_image = $data['default_image'];
                                    $block->direction = $data['direction'];
                                    $block->total_area = $data['total_area'];
                                    $block->green_area = $data['green_area'];
                                    $block->policy = $data['policy'];
                                    $block->policy_eng = $data['policy_eng'];
                                    $block->meta_title = $data['meta_title'];
                                    $block->meta_title_eng = $data['meta_title_eng'];
                                    $block->meta_keywords = $data['meta_keywords'];
                                    $block->meta_keywords_eng = $data['meta_keywords_eng'];
                                    $block->meta_description = $data['meta_description'];
                                    $block->meta_description_eng = $data['meta_description_eng'];

                                    $block->created_by = $userSession['id'];
                                    $block->project_id = $projectDetail->id;
                                    $block->status = \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE;
                                    $block->updated_by = $userSession['id'];
                                    $block->updated_at = date('Y-m-d H:i:s');
                                    $block->created_at = date('Y-m-d H:i:s');
                                    $block->slug = \ITECH\Data\Lib\Util::slug($data['name']);
                                    $block->slug_eng = \ITECH\Data\Lib\Util::slug($data['name_eng']);

                                    if ($block->create()) {
                                        // IMAGE
                                        $messageImage = array();
                                        if ($block->id && isset($data['images'])) {
                                            $_images = array();
                                            foreach ($data['images'] as $_image) {
                                                $_images[] = array(
                                                    'image' => $_image,
                                                    'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_GALLERY,
                                                    'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                                );
                                            }
                                            $_images[] = array(
                                                'image' => $data['default_image'],
                                                'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_THUMBNAIL,
                                                'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                            );

                                            $messageImage = parent::saveMapImage(array(
                                                'images' => $_images,
                                                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
                                                'item_id' => (int)$block->id
                                            ));
                                        }
                                        // IMAGE

                                        //ATTR
                                        $attributes = array();

                                        $attributes = array_merge($attributes, parent::saveAttr(array(
                                            'value' => isset($data['property_type']) ? $data['property_type'] : '',
                                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
                                        )));

                                        $attributes = array_merge($attributes, parent::saveAttr(array(
                                            'value' => isset($data['property_view']) ? $data['property_view'] : '',
                                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
                                        )));

                                        $attributes = array_merge($attributes, parent::saveAttr(array(
                                            'value' => isset($data['property_utility']) ? $data['property_utility'] : '',
                                            'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
                                        )));

                                        if (isset($attributes) && count($attributes)) {
                                            foreach ($attributes as $item) {
                                                $projectAttribute = new \ITECH\Data\Model\BlockAttributeModel();
                                                $projectAttribute->block_id = $block->id;
                                                $projectAttribute->attribute_id = $item;
                                                $projectAttribute->save();
                                            }
                                        }
                                        //ATTR

                                        $response[$row->getRowIndex()] = array(
                                            'name' => $block->name,
                                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                            'info' => $block,
                                        );
                                    } else {
                                        $messages = $block->getMessages();
                                        $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                                        $response[$row->getRowIndex()] = array(
                                            'name' => $block->name,
                                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                            'info' => $message
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $url = $this->config->application->api_url . 'location/list?authorized_token=' . $authorizedToken;
        $cacheName = md5(serialize(array(
            'Location',
            'Api',
            $url
        )));

        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName, $r);
        }

        $this->view->setVars(array(
            'responseBlock' => $response,
            'location' => $r,
            'projects' => $projects
        ));
        $this->view->pick(parent::$theme . '/system/data');
    }

    public function postImportApartmentAction()
    {
        if ($this->request->isGet()) {
            $this->response->redirect($this->url->get(array('for'=> 'system_data')));
        }

        require_once ROOT . '/vendor/excel/PHPExcel.php';
        require_once ROOT . '/vendor/excel/PHPExcel/IOFactory.php';

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');
        $response = array('status' => '200');

        $projectId = $this->request->getPost('project_id', array('trim', 'int', 'striptags'), 0);
        $blockId = $this->request->getPost('block_id', array('trim', 'int', 'striptags'), 0);

        $projectDetail = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'status = :status: AND id = :id:',
            'bind' => array(
                'id' => $projectId,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        ));
        if (!$projectDetail) {
            throw new \Phalcon\Exception('Dự án không tồn tại');
        }

        $blockDetail = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'status = :status: AND id = :id:',
            'bind' => array(
                'id' => $blockId,
                'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
            )
        ));
        if (!$blockDetail) {
            throw new \Phalcon\Exception('Block không tồn tại');
        }

        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            $response = array();
            foreach ($files as $file) {
                if (isset($file) && $file->getName() != '') {
                    $tmpFile = $file->getTempName();
                    $objPHPExcel = \PHPExcel_IOFactory::load($tmpFile);
                    $index = 0;
                    $keyMap = array(
                        '',
                        'name',
                        'name_eng',
                        'price',
                        'price_eng',
                        'price_sale_off',
                        'price_sale_off_eng',
                        'condition',
                        'floor',
                        'room_count',
                        'user_id',
                        'rose',
                        'total_area',
                        'green_area',
                        'image_position',
                        'position_vi',
                        'position_en',
                        'description',
                        'description_eng',
                        'default_image',
                        'images',
                        'bedroom_count',
                        'bathroom_count',
                        'type',
                        'direction',
                        'adults_count',
                        'children_count',
                        'property_type',
                        'property_view',
                        'property_utility',
                        'meta_title',
                        'meta_title_eng',
                        'meta_keywords',
                        'meta_keywords_eng',
                        'meta_description',
                        'meta_description_eng'
                    );

                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        if ($index != 0) {
                            break;
                        }
                        $index ++;

                        foreach ($worksheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            $i = 0;
                            if ($row->getRowIndex() >= 2){
                                $apartment = new \ITECH\Data\Model\ApartmentModel();
                                $data = array();
                                foreach ($cellIterator as $cell) {
                                    if ($i >= count($keyMap)) {
                                        break;
                                    }

                                    if ($i == 0 && is_null($cell->getCalculatedValue())) {
                                        break;
                                    }

                                    if (!is_null($cell) && $i > 0) {
                                        $value = $cell->getCalculatedValue();
                                        if ($keyMap[$i] == 'images') {
                                            $data[$keyMap[$i]] = explode(',', $value);
                                        } else {
                                            $data[$keyMap[$i]] = $value;
                                        }
                                    }

                                    $i ++;
                                }


                                $apartmentDetail = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                                   'conditions' => 'name = :name: AND status = :status: AND block_id = :block_id:',
                                    'bind' => array(
                                        'name' => $data['name'],
                                        'block_id' => $blockDetail->id,
                                        'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                                    )
                                ));
                                if ($apartmentDetail) {
                                    $response[$row->getRowIndex()] = array(
                                        'name' => $data['name'],
                                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                        'info' => array('Tên dự án đã tồn tại')
                                    );
                                } else {
                                    $validator = new \Phalcon\Validation();

                                    $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                                        'message' => 'Yêu cầu nhập tên sản phẩm.'
                                    )));
                                    $validator->setFilters('name', array('striptags', 'trim'));

                                    $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                                        'message' => 'Yêu cầu nhập tên sản phẩm (Tiếng Anh).'
                                    )));
                                    $validator->setFilters('name_eng', array('striptags', 'trim', 'int'));

                                    $validator->add('condition', new \Phalcon\Validation\Validator\Between(array(
                                        'minimum' => 1,
                                        'maximum' => 3,
                                        'message' => 'Tình trạng chỉ nhận 1(Còn trống) - 2(Đang xử lý) - 3(Đã bán).'
                                    )));
                                    $validator->setFilters('condition', array('striptags', 'trim', 'int'));

                                    $validator->add('type', new \Phalcon\Validation\Validator\Between(array(
                                        'minimum' => 1,
                                        'maximum' => 2,
                                        'message' => 'Kiểu căn hộ chỉ nhận 1(Bán) - 2(Cho thuê).'
                                    )));
                                    $validator->setFilters('type', array('striptags', 'trim', 'int'));

                                    $validator->setFilters('name', array('striptags', 'trim'));
                                    $validator->setFilters('name_eng', array('striptags', 'trim'));
                                    $validator->setFilters('price', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('price_eng', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('price_sale_off', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('price_sale_off_eng', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('condition', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('floor', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('room_count', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('user_id', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('rose', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('total_area', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('green_area', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('image_position', array('striptags', 'trim',));
                                    $validator->setFilters('position_vi', array('striptags', 'trim'));
                                    $validator->setFilters('image_position', array('striptags', 'trim'));
                                    $validator->setFilters('position_en', array('striptags', 'trim'));
                                    $validator->setFilters('description', array('striptags', 'trim'));
                                    $validator->setFilters('description_eng', array('striptags', 'trim'));
                                    $validator->setFilters('default_image', array('striptags', 'trim'));
                                    $validator->setFilters('bedroom_count', array('striptags', 'trim'));
                                    $validator->setFilters('bathroom_count', array('striptags', 'trim'));
                                    $validator->setFilters('type', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('direction', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('adults_count', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('children_count', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('meta_title', array('striptags', 'trim'));
                                    $validator->setFilters('meta_title_eng', array('striptags', 'trim'));
                                    $validator->setFilters('meta_keywords', array('striptags', 'trim'));
                                    $validator->setFilters('meta_keywords_eng', array('striptags', 'trim'));
                                    $validator->setFilters('meta_description', array('striptags', 'trim'));
                                    $validator->setFilters('meta_description_eng', array('striptags', 'trim'));
                                    $validator->setFilters('images', array('striptags', 'trim'));
                                    $validator->setFilters('property_type', array('striptags', 'trim'));
                                    $validator->setFilters('property_view', array('striptags', 'trim'));
                                    $validator->setFilters('property_utility', array('striptags', 'trim'));

                                    $messages = $validator->validate($data);
                                    if (count($messages)) {
                                        $result = array();
                                        foreach ($messages as $message) {
                                            $result[$message->getField()] = $message->getMessage();
                                        }

                                        $response[$row->getRowIndex()] = array(
                                            'name' => $apartment->name,
                                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                            'info' => $result,
                                        );
                                    } else {
                                        $apartment->name = $validator->getValue('name');
                                        $apartment->name_eng = $validator->getValue('name_eng');
                                        $apartment->price = $validator->getValue('price');
                                        $apartment->price_eng = $validator->getValue('price_eng');
                                        $apartment->price_sale_off = $validator->getValue('price_sale_off');
                                        $apartment->price_sale_off_eng = $validator->getValue('price_sale_off_eng');
                                        $apartment->condition = $validator->getValue('condition');
                                        $apartment->floor = $validator->getValue('floor');
                                        $apartment->room_count = $validator->getValue('room_count');
                                        $apartment->user_id = $validator->getValue('user_id');
                                        $apartment->rose = $validator->getValue('rose');
                                        $apartment->total_area = $validator->getValue('total_area');
                                        $apartment->green_area = $validator->getValue('green_area');

                                        $apartment->position = json_encode(array(
                                            'image' => $validator->getValue('image_position'),
                                            'description' => $validator->getValue('position_vi')
                                        ));
                                        $apartment->position_eng = json_encode(array(
                                            'image' => $validator->getValue('image_position'),
                                            'description' => $validator->getValue('position_en')
                                        ));

                                        $apartment->description = $validator->getValue('description');
                                        $apartment->description_eng = $validator->getValue('description_eng');
                                        $apartment->default_image = $validator->getValue('default_image');
                                        $apartment->bedroom_count = $validator->getValue('bedroom_count');
                                        $apartment->bathroom_count = $validator->getValue('bathroom_count');
                                        $apartment->type = $validator->getValue('type');
                                        $apartment->direction = $validator->getValue('direction');
                                        $apartment->adults_count = $validator->getValue('adults_count');
                                        $apartment->children_count = $validator->getValue('children_count');
                                        $apartment->meta_title = $validator->getValue('meta_title');
                                        $apartment->meta_title_eng = $validator->getValue('meta_title_eng');
                                        $apartment->meta_keywords = $validator->getValue('meta_keywords');
                                        $apartment->meta_keywords_eng = $validator->getValue('meta_keywords_eng');
                                        $apartment->meta_description = $validator->getValue('meta_description');
                                        $apartment->meta_description_eng = $validator->getValue('meta_description_eng');


                                        $apartment->created_by = $userSession['id'];
                                        $apartment->block_id = $blockDetail->id;
                                        $apartment->status = \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE;
                                        $apartment->updated_by = $userSession['id'];
                                        $apartment->updated_at = date('Y-m-d H:i:s');
                                        $apartment->created_at = date('Y-m-d H:i:s');
                                        $apartment->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('name'));
                                        $apartment->slug_eng = \ITECH\Data\Lib\Util::slug($validator->getValue('name_eng'));

                                        if ($apartment->create()) {
                                            // IMAGE
                                            $messageImage = array();
                                            if ($apartment->id && $validator->getValue('images')) {
                                                $_images = array();
                                                foreach ($validator->getValue('images') as $_image) {
                                                    $_images[] = array(
                                                        'image' => $_image,
                                                        'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_GALLERY,
                                                        'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                                    );
                                                }
                                                $_images[] = array(
                                                    'image' => $validator->getValue('default_image'),
                                                    'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_THUMBNAIL,
                                                    'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                                );

                                                $messageImage = parent::saveMapImage(array(
                                                    'images' => $_images,
                                                    'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT,
                                                    'item_id' => (int)$apartment->id
                                                ));
                                            }
                                            // IMAGE

                                            //ATTR
                                            $attributes = array();

                                            $attributes = array_merge($attributes, parent::saveAttr(array(
                                                'value' => $validator->getValue('property_type') ? $validator->getValue('property_type') : '',
                                                'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                            )));

                                            $attributes = array_merge($attributes, parent::saveAttr(array(
                                                'value' => $validator->getValue('property_view') ? $validator->getValue('property_view') : '',
                                                'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                            )));

                                            $attributes = array_merge($attributes, parent::saveAttr(array(
                                                'value' => $validator->getValue('property_utility') ? $validator->getValue('property_utility') : '',
                                                'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                            )));
                                            if (isset($attributes) && count($attributes)) {
                                                foreach ($attributes as $item) {
                                                    $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                                    $apartmentAttribute->apartment_id = $apartment->id;
                                                    $apartmentAttribute->attribute_id = $item;
                                                    $apartmentAttribute->save();
                                                }
                                            }
                                            //ATTR

                                            $response[$row->getRowIndex()] = array(
                                                'name' => $apartment->name,
                                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                                'info' => $apartment,
                                            );
                                        } else {
                                            $messages = $apartment->getMessages();
                                            $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                                            $response[$row->getRowIndex()] = array(
                                                'name' => $apartment->name,
                                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                                'info' => $message
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $url = $this->config->application->api_url . 'location/list?authorized_token=' . $authorizedToken;
        $cacheName = md5(serialize(array(
            'Location',
            'Api',
            $url
        )));

        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName, $r);
        }

        $this->view->setVars(array(
            'responseApartment' => $response,
            'location' => $r,
            'projects' => $projects
        ));
        $this->view->pick(parent::$theme . '/system/data');
    }

    public function postImportUpdateApartmentAction()
    {
        if ($this->request->isGet()) {
            $this->response->redirect($this->url->get(array('for'=> 'system_data')));
        }

        require_once ROOT . '/vendor/excel/PHPExcel.php';
        require_once ROOT . '/vendor/excel/PHPExcel/IOFactory.php';

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $userSession = $this->session->get('USER');
        $response = array('status' => '200');

        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            $response = array();
            foreach ($files as $file) {
                if (isset($file) && $file->getName() != '') {
                    $tmpFile = $file->getTempName();
                    $objPHPExcel = \PHPExcel_IOFactory::load($tmpFile);
                    $index = 0;
                    $keyMap = array(
                        'id',
                        'name',
                        'name_eng',
                        'price',
                        'price_eng',
                        'price_sale_off',
                        'price_sale_off_eng',
                        'condition',
                        'floor',
                        'room_count',
                        'user_id',
                        'rose',
                        'total_area',
                        'green_area',
                        'image_position',
                        'position_vi',
                        'position_en',
                        'description',
                        'description_eng',
                        'default_image',
                        'images',
                        'bedroom_count',
                        'bathroom_count',
                        'type',
                        'direction',
                        'adults_count',
                        'children_count',
                        'property_type',
                        'property_view',
                        'property_utility',
                        'meta_title',
                        'meta_title_eng',
                        'meta_keywords',
                        'meta_keywords_eng',
                        'meta_description',
                        'meta_description_eng'
                    );

                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        if ($index != 0) {
                            break;
                        }
                        $index ++;

                        foreach ($worksheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            $i = 0;
                            if ($row->getRowIndex() >= 2){
                                $data = array();
                                foreach ($cellIterator as $cell) {
                                    if ($i >= count($keyMap)) {
                                        break;
                                    }

                                    if ($i == 0 && is_null($cell->getCalculatedValue())) {
                                        break;
                                    }

                                    if (!is_null($cell)) {
                                        $value = $cell->getCalculatedValue();
                                        if ($keyMap[$i] == 'images') {
                                            $data[$keyMap[$i]] = explode(',', $value);
                                        } else {
                                            $data[$keyMap[$i]] = $value;
                                        }
                                    }

                                    $i ++;
                                }
                                $apartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                                    'conditions' => 'id = :id: AND status = :status:',
                                    'bind' => array(
                                        'id' => $data['id'],
                                        'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                                    )
                                ));
                                if (!$apartment) {
                                    $response[$row->getRowIndex()] = array(
                                        'name' => $data['name'],
                                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                        'info' => array('Sản phẩm này không tồn tại')
                                    );
                                } else {
                                    $validator = new \Phalcon\Validation();

                                    $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                                        'message' => 'Yêu cầu nhập tên sản phẩm.'
                                    )));
                                    $validator->setFilters('name', array('striptags', 'trim'));

                                    $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                                        'message' => 'Yêu cầu nhập tên sản phẩm (Tiếng Anh).'
                                    )));
                                    $validator->setFilters('name_eng', array('striptags', 'trim'));

                                    $validator->add('condition', new \Phalcon\Validation\Validator\Between(array(
                                        'minimum' => 1,
                                        'maximum' => 3,
                                        'message' => 'Tình trạng chỉ nhận 1(Còn trống) - 2(Đang xử lý) - 3(Đã bán).'
                                    )));
                                    $validator->setFilters('condition', array('striptags', 'trim', 'int'));

                                    $validator->add('type', new \Phalcon\Validation\Validator\Between(array(
                                        'minimum' => 1,
                                        'maximum' => 2,
                                        'message' => 'Kiểu căn hộ chỉ nhận 1(Bán) - 2(Cho thuê).'
                                    )));
                                    $validator->setFilters('type', array('striptags', 'trim', 'int'));

                                    $validator->setFilters('name', array('striptags', 'trim'));
                                    $validator->setFilters('name_eng', array('striptags', 'trim'));
                                    $validator->setFilters('price', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('price_eng', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('price_sale_off', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('price_sale_off_eng', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('condition', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('floor', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('room_count', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('user_id', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('rose', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('total_area', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('green_area', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('image_position', array('striptags', 'trim',));
                                    $validator->setFilters('position_vi', array('striptags', 'trim'));
                                    $validator->setFilters('image_position', array('striptags', 'trim'));
                                    $validator->setFilters('position_en', array('striptags', 'trim'));
                                    $validator->setFilters('description', array('striptags', 'trim'));
                                    $validator->setFilters('description_eng', array('striptags', 'trim'));
                                    $validator->setFilters('default_image', array('striptags', 'trim'));
                                    $validator->setFilters('bedroom_count', array('striptags', 'trim'));
                                    $validator->setFilters('bathroom_count', array('striptags', 'trim'));
                                    $validator->setFilters('type', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('direction', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('adults_count', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('children_count', array('striptags', 'trim', 'int'));
                                    $validator->setFilters('meta_title', array('striptags', 'trim'));
                                    $validator->setFilters('meta_title_eng', array('striptags', 'trim'));
                                    $validator->setFilters('meta_keywords', array('striptags', 'trim'));
                                    $validator->setFilters('meta_keywords_eng', array('striptags', 'trim'));
                                    $validator->setFilters('meta_description', array('striptags', 'trim'));
                                    $validator->setFilters('meta_description_eng', array('striptags', 'trim'));
                                    $validator->setFilters('images', array('striptags', 'trim'));
                                    $validator->setFilters('property_type', array('striptags', 'trim'));
                                    $validator->setFilters('property_view', array('striptags', 'trim'));
                                    $validator->setFilters('property_utility', array('striptags', 'trim'));

                                    $messages = $validator->validate($data);
                                    if (count($messages)) {
                                        $result = array();
                                        foreach ($messages as $message) {
                                            $result[$message->getField()] = $message->getMessage();
                                        }

                                        $response[$row->getRowIndex()] = array(
                                            'name' => $apartment->name,
                                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                            'info' => $result,
                                        );
                                    } else {
                                        $apartment->name = $validator->getValue('name');
                                        $apartment->name_eng = $validator->getValue('name_eng');
                                        $apartment->price = $validator->getValue('price');
                                        $apartment->price_eng = $validator->getValue('price_eng');
                                        $apartment->price_sale_off = $validator->getValue('price_sale_off');
                                        $apartment->price_sale_off_eng = $validator->getValue('price_sale_off_eng');
                                        $apartment->condition = $validator->getValue('condition');
                                        $apartment->floor = $validator->getValue('floor');
                                        $apartment->room_count = $validator->getValue('room_count');
                                        $apartment->user_id = $validator->getValue('user_id');
                                        $apartment->rose = $validator->getValue('rose');
                                        $apartment->total_area = $validator->getValue('total_area');
                                        $apartment->green_area = $validator->getValue('green_area');

                                        $apartment->position = json_encode(array(
                                            'image' => $validator->getValue('image_position'),
                                            'description' => $validator->getValue('position_vi')
                                        ));
                                        $apartment->position_eng = json_encode(array(
                                            'image' => $validator->getValue('image_position'),
                                            'description' => $validator->getValue('position_en')
                                        ));

                                        $apartment->description = $validator->getValue('description');
                                        $apartment->description_eng = $validator->getValue('description_eng');
                                        $apartment->default_image = $validator->getValue('default_image');
                                        $apartment->bedroom_count = $validator->getValue('bedroom_count');
                                        $apartment->bathroom_count = $validator->getValue('bathroom_count');
                                        $apartment->type = $validator->getValue('type');
                                        $apartment->direction = $validator->getValue('direction');
                                        $apartment->adults_count = $validator->getValue('adults_count');
                                        $apartment->children_count = $validator->getValue('children_count');
                                        $apartment->meta_title = $validator->getValue('meta_title');
                                        $apartment->meta_title_eng = $validator->getValue('meta_title_eng');
                                        $apartment->meta_keywords = $validator->getValue('meta_keywords');
                                        $apartment->meta_keywords_eng = $validator->getValue('meta_keywords_eng');
                                        $apartment->meta_description = $validator->getValue('meta_description');
                                        $apartment->meta_description_eng = $validator->getValue('meta_description_eng');


                                        $apartment->updated_by = $userSession['id'];
                                        $apartment->updated_at = date('Y-m-d H:i:s');
                                        $apartment->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('name'));
                                        $apartment->slug_eng = \ITECH\Data\Lib\Util::slug($validator->getValue('name_eng'));

                                        if ($apartment->update()) {
                                            // IMAGE
                                            $messageImage = array();
                                            if ($apartment->id && isset($data['images'])) {
                                                $_images = array();
                                                foreach ($data['images'] as $_image) {
                                                    $_images[] = array(
                                                        'image' => $_image,
                                                        'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_GALLERY,
                                                        'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                                    );
                                                }
                                                $_images[] = array(
                                                    'image' => $data['default_image'],
                                                    'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_THUMBNAIL,
                                                    'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_IMAGE,
                                                );

                                                $messageImage = parent::saveMapImage(array(
                                                    'images' => $_images,
                                                    'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT,
                                                    'item_id' => (int)$apartment->id
                                                ));
                                            }
                                            // IMAGE

                                            //ATTR
                                            $attributes = array();

                                            $attributes = array_merge($attributes, parent::saveAttr(array(
                                                'value' => isset($data['property_type']) ? $data['property_type'] : '',
                                                'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                            )));

                                            $attributes = array_merge($attributes, parent::saveAttr(array(
                                                'value' => isset($data['property_view']) ? $data['property_view'] : '',
                                                'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                            )));

                                            $attributes = array_merge($attributes, parent::saveAttr(array(
                                                'value' => isset($data['property_utility']) ? $data['property_utility'] : '',
                                                'type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                            )));

                                            if ($apartment->id) {
                                                $apartmentAttributeModel = new \ITECH\Data\Model\ApartmentAttributeModel();

                                                $q = 'DELETE FROM `land_apartment_attribute` WHERE `apartment_id` = "' . $apartment->id . '"';
                                                $apartmentAttributeModel->getWriteConnection()->query($q);
                                            }

                                            if (isset($attributes) && count($attributes)) {
                                                foreach ($attributes as $item) {
                                                    $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                                    $apartmentAttribute->apartment_id = $apartment->id;
                                                    $apartmentAttribute->attribute_id = $item;
                                                    $apartmentAttribute->save();
                                                }
                                            }
                                            //ATTR

                                            $response[$row->getRowIndex()] = array(
                                                'name' => $apartment->name,
                                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                                'info' => $apartment,
                                            );
                                        } else {
                                            $messages = $apartment->getMessages();
                                            $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                                            $response[$row->getRowIndex()] = array(
                                                'name' => $apartment->name,
                                                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                                                'info' => $message
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $url = $this->config->application->api_url . 'location/list?authorized_token=' . $authorizedToken;
        $cacheName = md5(serialize(array(
            'Location',
            'Api',
            $url
        )));

        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName, $r);
        }

        $this->view->setVars(array(
            'responseApartmentUpdate' => $response,
            'location' => $r,
            'projects' => $projects
        ));
        $this->view->pick(parent::$theme . '/system/data');
    }

    public function exportApartmentAction()
    {
        if ($this->request->isPost()) {
            $projectId = $this->request->getPost('project_id');

            $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                'conditions' => 'id = :project_id:',
                'bind' => array('project_id' => $projectId)
            ));

            if (!$project) {
                throw new \Exception('Không tồn tại dự án này.');
            }

            $apartmentModel = new \ITECH\Data\Model\ApartmentModel();
            $b = $apartmentModel->getModelsManager()->createBuilder();
            $b->columns(array(
                'a1.id',
                'a1.name',
                'a1.name_eng',
                'a1.price',
                'a1.price_eng',
                'a1.price_sale_off',
                'a1.price_sale_off_eng',
                'a1.condition',
                'a1.floor',
                'a1.room_count',
                'a1.user_id',
                'a1.rose',
                'a1.position',
                'a1.position_eng',
                'a1.total_area',
                'a1.green_area',
                'a1.description',
                'a1.description_eng',
                'a1.default_image',
                'a1.bedroom_count',
                'a1.bathroom_count',
                'a1.type',
                'a1.direction',
                'a1.adults_count',
                'a1.children_count',
                'a1.meta_title',
                'a1.meta_title_eng',
                'a1.meta_keywords',
                'a1.meta_keywords_eng',
                'a1.meta_description',
                'a1.meta_description_eng',
            ));

            $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
            $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
            $b->leftJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

            $b->andWhere('a1.status = :status:', array('status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
            $b->andWhere('p1.id = :projectID:', array('projectID' => $projectId));

            $response = $b->getQuery()->execute();


            if (!$response) {
                throw new \Exception('Dự án này chưa có sản phẩm');
            }

            $output = array();
            foreach ($response as $apartment) {
                $position = json_decode($apartment->position, true);
                $apartment->position_vi = isset($position['description']) ? $position['description'] : '';

                $positionEng = json_decode($apartment->position_eng, true);
                $apartment->position_en = isset($positionEng['description']) ? $positionEng['description'] : '';
                $apartment->image_position = isset($position['image']) ? $position['image'] : '';

                // --------- Apartment Attributes
                $apartmentAttributeModel = new \ITECH\Data\Model\ApartmentAttributeModel();

                $b = $apartmentAttributeModel->getModelsManager()->createBuilder();
                $b->columns(array(
                    'a1.id AS apartment_id',
                    'a2.id AS attribute_id',
                    'a2.name AS attribute_name',
                    'a2.type AS attribute_type'
                ));

                $b->from(array('aa1' => 'ITECH\Data\Model\ApartmentAttributeModel'));
                $b->innerJoin('ITECH\Data\Model\AttributeModel', 'a2.id = aa1.attribute_id', 'a2');
                $b->innerJoin('ITECH\Data\Model\ApartmentModel', 'a1.id = aa1.apartment_id', 'a1');

                $b->andWhere('a2.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));
                $b->andWhere('a1.id = :apartment_id:', array('apartment_id' => $apartment->id));
                $b->orderBy('a1.id DESC');

                $result = $b->getQuery()->execute();
                $apartmentPropertyType = '';
                $apartmentPropertyView = '';
                $apartmentPropertyUtility = '';

                if (count($result)) {
                    $arrayTypeVie = array();
                    $arrayViewVie = array();
                    $arrayUtilityVie = array();

                    foreach ($result as $item) {
                        if ($item['attribute_type'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE) {
                            $arrayTypeVie[] = $item['attribute_name'];
                        }
                        if ($item['attribute_type'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW) {
                            $arrayViewVie[] = $item['attribute_name'];
                        }
                        if ($item['attribute_type'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY) {
                            $arrayUtilityVie[] = $item['attribute_name'];
                        }
                    }

                    if (count($arrayTypeVie)) {
                        $apartmentPropertyType = implode(', ', $arrayTypeVie);
                    }

                    if (count($arrayViewVie)) {
                        $apartmentPropertyView = implode(', ', $arrayViewVie);
                    }

                    if (count($arrayUtilityVie)) {
                        $apartmentPropertyUtility = implode(', ', $arrayUtilityVie);
                    }
                }
                // Apartment Attributes ---------
                $mapImage = parent::getMapImage(array(
                    'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT,
                    'item_id' => (int)$apartment->id
                ));
                $apartment->images = '';
                foreach ($mapImage as $item) {
                    $apartment->images .= $item->image . ', ';
                }

                $apartment->property_type = $apartmentPropertyType;
                $apartment->property_view = $apartmentPropertyView;
                $apartment->property_utility = $apartmentPropertyUtility;
                $output[] = $apartment;
            }

            $fileName = 'product_by_project_' . $project->slug . '.xlsx';
            $export = \ITECH\Data\Lib\Excel::exportDataApartment($output, $fileName);

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Success.',
                'result' => array()
            );

            if (!$export) {
                $response['status'] = \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR;
                $response['message'] = 'Error!';
            } else {
                $response['result'] = $this->config->asset->download . $fileName;
                $response['status'] = \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS;
                $response['message'] = 'Success.';
            }

            parent::outputJSON($response);

        } else {
            throw new \Phalcon\Exception('Lỗi. Để xuất ra danh sách sản phẩm vui lòng chọn dự án ');
        }
    }
}
