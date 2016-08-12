<?php
namespace ITECH\Admin\Controller;

class BlockController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();

        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SEO,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_MARKETING
        ));
    }

    public function indexAction()
    {
        $projectIds = parent::getPermissionProjects();

        $params = [];

        if (is_array($projectIds)) {
            $params['conditions']['projectIdsString'] = $projectIds['projectIdsString'];
        }

        $params['conditions']['status'] = \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE;

        $blockRepo = new \ITECH\Data\Repo\BlockRepo();
        $blocks = $blockRepo->getList($params);

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách Block/Khu',
                'url'    => $this->url->get(['for' => 'block_list']),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'blocks'      => $blocks
        ));
        $this->view->pick(parent::$theme . '/block/index');
    }

    public function listBlockAction()
    {
        $projectId = $this->request->getQuery('project_id', array('trim', 'int'), 0);

        $params = array();

        if ($projectId > 0) {
            $params = array('conditions' => array('project_id' => $projectId));
        }

        $projectIds = parent::getPermissionProjects();

        if (is_array($projectIds)) {
            $params['conditions']['projectIdsString'] = $projectIds['projectIdsString'];
        }

        $params['conditions']['status'] = \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE;

        $blockRepo = new \ITECH\Data\Repo\BlockRepo();
        $blocks    = $blockRepo->getList($params);

        $out         = array();
        $out['data'] = array();

        foreach ($blocks as $block) {
            $projectUrl = '<a href="' . $this->url->get(array('for' => 'project_edit', 'query' => '?' . http_build_query(array('id' => $block->project_id) ))) . '">' . $block->project_name . '</a>';
            $blockUrl   = '<a href="' . $this->url->get(array('for' => 'block_edit', 'query' => '?' . http_build_query(array('id' => $block->id, 'project_id' => $block->project_id) ))) . '">' . $block->name . '</a>';
            $deleteUrl  = '<div class="text-center"><a href="' . $this->url->get(array("for" => "block_delete", "query" => "?" . http_build_query(array("id" => $block->id)))) .'" onclick="return confirm(\'Đồng ý xoá?\');" class="btn btn-xs btn-bricky"><i class="fa fa-times fa fa-white"></i></a></div>';

            $_blockRepo = new \ITECH\Data\Repo\BlockRepo();
            $mapLink    = $_blockRepo->checkMapLink($block->id);

            $map = false;

            foreach ($mapLink as $link) {
                $map .= $link->id . ',';
            }

            if ($map) {
                $map = '<span class="check-map">Ok</span>';
            } else {
                $map = '';
            }

            $apartmentCount = \ITECH\Data\Model\ApartmentModel::find(array(
                'conditions' => 'block_id = :block_id:',
                'bind'       => array('block_id' => $block->id)
            ));
            $apartmentCount = $apartmentCount->count();

            $apartmentSoldCount = \ITECH\Data\Model\ApartmentModel::find(array(
                'conditions' => '
                    block_id      = :block_id: 
                    AND condition = :condition:
                ',
                'bind' => array(
                    'block_id'  => $block->id,
                    'condition' => \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD
                )
            ));
            $apartmentSoldCount = $apartmentSoldCount->count();

            $getStatus = \ITECH\Data\Lib\Constant::getBlockStatus();

            $out['data'][] = array(
                $block->id,
                $blockUrl,
                $projectUrl,
                $block->floor_count,
                $map,
                '<div class="text-center"><span class="icon-status-' . $block->status . '"></span><span class="text-status-' . $block->status . '">' . $getStatus[$block->status] . '</span></span></div>',
                $apartmentCount,
                $apartmentSoldCount,
                $deleteUrl
            );
        }

        parent::outputJSON($out);
    }

    public function addAction()
    {
        $userSession     = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $project_id = $this->request->getQuery('project_id', array('int'), -1);
        $page       = $this->request->getQuery('page', array('int'), 1);
        $from       = $this->request->getQuery('from', array('striptags', 'trim', 'lower'), '');

        $project = [];

        // Get project ---------
        if ($this->request->hasQuery('project_id')) {
            $project = array();
            $url = $this->config->application->api_url . 'project/detail';
            $get = array(
                'authorized_token' => $authorizedToken,
                'id'               => $project_id,
                'cache'            => 'false'
            );
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

            if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                $project = $r['result'];
            }
        }
        // --------- Get project

        $block = new \ITECH\Data\Model\BlockModel();
        $form  = new \ITECH\Admin\Form\BlockForm($block, ['userSession' => $userSession]);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $block);

            // Get project ---------
            if ($this->request->hasPost('project_id')) {
                $project_id = $this->request->getPost('project_id');
                $project = array();
                $url = $this->config->application->api_url . 'project/detail';
                $get = array(
                    'authorized_token' => $authorizedToken,
                    'id' => $project_id,
                    'cache' => 'false'
                );
                $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

                if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $project = $r['result'];
                } else {
                    throw new \Phalcon\Exception('Không tồn tại dự án này.');
                }
            }
            // --------- Get project

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $propertyTrend = $this->request->getPost('direction');

                $floorNameList = '';
                $apartmentNameList = '';

                if ($this->request->getPost('floor_name_list')) {
                    $floorNameList = trim(strip_tags($this->request->getPost('floor_name_list')));
                    if ($floorNameList == '') {
                        $floorNameList = '';
                    } else {
                        $array = json_decode($floorNameList, true);
                        if (!is_array($array) || (is_array($array) && !count($array))) {
                            $floorNameList = '';
                        }
                    }
                }

                if ($this->request->getPost('apartment_name_list')) {
                    $apartmentNameList = trim(strip_tags($this->request->getPost('apartment_name_list')));
                    if ($apartmentNameList == '') {
                        $apartmentNameList = '';
                    } else {
                        $array = json_decode($apartmentNameList, true);
                        if (!is_array($array) || (is_array($array) && !count($array))) {
                            $apartmentNameList = '';
                        }
                    }
                }

                $gallery = json_encode(array());
                if ($this->request->getPost('gallery')) {
                    $galleryPost = array_unique($this->request->getPost('gallery'));
                    $gallery = json_encode($galleryPost);
                }

                $url = $this->config->application->api_url . 'block/add?authorized_token=' . $authorizedToken . '&project_id=' . $project['id'];
                $post = array(
                    'name' => trim(strip_tags($this->request->getPost('name'))),
                    'name_eng' => trim(strip_tags($this->request->getPost('name_eng'))),
                    'shortname' => $this->request->getPost('shortname'),
                    'floor_name_list' => $floorNameList,
                    'apartment_name_list' => $apartmentNameList,
                    'floor_count' => $this->request->getPost('floor_count'),
                    'apartment_count' => $this->request->getPost('apartment_count'),
                    'direction' => $propertyTrend,
                    'gallery' => $gallery,
                    'price' => $this->request->getPost('price'),
                    'price_eng' => $this->request->getPost('price_eng'),
                    'status' => $this->request->getPost('status'),
                    'created_by' => $userSession['id'],
                    'updated_by' => $userSession['id'],
                    'approved_by' => $userSession['id'],
                    'user_id' => $userSession['id'],
                    'description_eng' => $this->request->getPost('description_eng'),
                    'description' => $this->request->getPost('description'),
                    'policy' => $this->request->getPost('policy'),
                    'policy_eng' => $this->request->getPost('policy_eng'),
                    'area' => $this->request->getPost('area'),
                    'space' => $this->request->getPost('space'),
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getClientAddress()
                );

                $defaultImage = $this->request->getPost('default_image');
                if ($defaultImage != '') {
                    $post['default_image'] = $defaultImage;
                }

                $post['attribute_type'] = $this->request->getPost('attribute_type');
                $post['attribute_view'] = $this->request->getPost('attribute_view');
                $post['attribute_utility'] = $this->request->getPost('attribute_utility');

                $post['attribute_type_eng'] = $this->request->getPost('attribute_type_eng');
                $post['attribute_view_eng'] = $this->request->getPost('attribute_view_eng');
                $post['attribute_utility_eng'] = $this->request->getPost('attribute_utility_eng');

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Tạo block thành công.');
                    $query = array(
                        'id' => $r['result']['id'],
                        'project_id' => $project['id']
                    );

                    // IMAGE
                    $messageImage = array();
                    if ($r['result']['id'] && $this->request->hasPost('galleries')) {
                        $listImage = $this->request->getPost('galleries');
                        parent::saveMapImage(array(
                            'images' => $listImage,
                            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
                            'item_id' => (int)$r['result']['id']
                        ));
                    }
                    // IMAGE

                    return $this->response->redirect(array('for' => 'block_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, tạo block không thành công.');
                    }
                }
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => isset($project) ? $project['name'] : '',
                'url' => $this->url->get([
                    'for' => 'project_list'
                ]),
                'active' => false
            ],
            [
                'title' => 'Danh sách Block',
                'url' => $this->url->get([
                    'for' => 'block_list',
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm block',
                'url' => $this->url->get([
                    'for' => 'block_add'
                ]),
                'active' => true
            ]
        ];

        if (!isset($project['id'])) {
            $breadcrumbs[1] = [
                'title' => 'Danh sách dự án',
                'url' => $this->url->get([
                    'for' => 'project_list'
                ]),
                'active' => false
            ];
        }

        $dataAttributeType = parent::getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE);
        $dataAttributeView = parent::getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW);
        $dataAttributeUtility = parent::getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY);

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'project' => $project,
            'form' => $form,
            'data_attribute_type' => json_encode($dataAttributeType),
            'data_attribute_view' => json_encode($dataAttributeView),
            'data_attribute_utility' => json_encode($dataAttributeUtility),
            'from' => $from,
            'page' => $page
        ));
        $this->view->pick(parent::$theme . '/block/edit');
    }

    public function editAction()
    {
        $userSession     = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $projectId = $this->request->getPost('project_id', array('int'), '');
        $id        = $this->request->getQuery('id', array('int'), '');
        $page      = $this->request->getQuery('page', array('int'), 1);
        $from      = $this->request->getQuery('from', array('striptags', 'trim'), '');

        // Get block ---------
        $url = $this->config->application->api_url . 'block/detail?id=' . $id . '&cache=false&type=' . \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR . '&authorized_token=' . $authorizedToken;

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $block = new \ITECH\Data\Model\BlockModel();

        if (isset($r['result']) && count($r['result']) && isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $attributeType = array();
            foreach ($r['result']['attribute']['type'] as $item) {
                $attributeType[] = $item['name'];
            }

            $attributeTypeEng = array();
            foreach ($r['result']['attribute']['type_eng'] as $item) {
                $attributeTypeEng[] = $item['name'];
            }

            $attributeView = array();
            foreach ($r['result']['attribute']['view'] as $item) {
                $attributeView[] = $item['name'];
            }

            $attributeViewEng = array();
            foreach ($r['result']['attribute']['view_eng'] as $item) {
                $attributeViewEng[] = $item['name'];
            }

            $attributeUtility = array();
            foreach ($r['result']['attribute']['utility'] as $item) {
                $attributeUtility[] = $item['name'];
            }

            $attributeUtilityEng = array();
            foreach ($r['result']['attribute']['utility_eng'] as $item) {
                $attributeUtilityEng[] = $item['name'];
            }

            $block->id                    = $r['result']['id'];
            $block->name                  = $r['result']['name'];
            $block->name_eng              = $r['result']['name_eng'];
            $block->shortname             = $r['result']['shortname'];
            $block->floor_name_list       = $r['result']['floor_name_list'];
            $block->apartment_name_list   = $r['result']['apartment_name_list'];
            $block->default_image         = $r['result']['default_image'];
            $block->floor_count           = $r['result']['floor_count'];
            $block->apartment_count       = $r['result']['apartment_count'];
            $block->price                 = $r['result']['price'];
            $block->price_eng             = $r['result']['price_eng'];
            $block->gallery               = json_decode($r['result']['gallery']);
            $block->direction             = $r['result']['direction'];
            $block->attribute_type        = implode(',', $attributeType);
            $block->attribute_type_eng    = implode(',', $attributeTypeEng);
            $block->attribute_view        = implode(',', $attributeView);
            $block->attribute_view_eng    = implode(',', $attributeViewEng);
            $block->attribute_utility     = implode(',', $attributeUtility);
            $block->attribute_utility_eng = implode(',', $attributeUtilityEng);
            $block->description           = $r['result']['description'];
            $block->description_eng       = $r['result']['description_eng'];
            $block->policy                = $r['result']['policy'];
            $block->policy_eng            = $r['result']['policy_eng'];
            $block->total_area            = $r['result']['total_area'];
            $block->green_area            = $r['result']['green_area'];
            $block->status                = $r['result']['status'];
            $block->project_id            = $r['result']['project']['id'];
            $block->project_name          = $r['result']['project']['name'];
            $block->meta_title            = $r['result']['meta_title'];
            $block->meta_title_eng        = $r['result']['meta_title_eng'];
            $block->meta_keywords         = $r['result']['meta_keywords'];
            $block->meta_keywords_eng     = $r['result']['meta_keywords_eng'];
            $block->meta_description      = $r['result']['meta_description'];
            $block->meta_description_eng  = $r['result']['meta_description_eng'];
        } else {
            if (isset($r['message'])) {
                throw new \Phalcon\Exception($r['message']);
            } else {
                throw new \Phalcon\Exception('Lỗi, không tồn tại Block này');
            }
        }
        // --------- Get block

        $form = new \ITECH\Admin\Form\BlockForm($block, ['userSession' => $userSession]);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $block);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $type = \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR;

                $floorNameList     = '';
                $apartmentNameList = '';

                if ($this->request->getPost('floor_name_list')) {
                    $floorNameList = trim(strip_tags($this->request->getPost('floor_name_list')));
                    if ($floorNameList == '') {
                        $floorNameList = '';
                    } else {
                        $array = json_decode($floorNameList, true);
                        if (!is_array($array) || (is_array($array) && !count($array))) {
                            $floorNameList = '';
                        }
                    }
                }

                if ($this->request->getPost('apartment_name_list')) {
                    $apartmentNameList = trim(strip_tags($this->request->getPost('apartment_name_list')));
                    if ($apartmentNameList == '') {
                        $apartmentNameList = '';
                    } else {
                        $array = json_decode($apartmentNameList, true);
                        if (!is_array($array) || (is_array($array) && !count($array))) {
                            $apartmentNameList = '';
                        }
                    }
                }

                $gallery = json_encode(array());
                if ($this->request->getPost('gallery')) {
                    $galleryPost = array_unique($this->request->getPost('gallery'));
                    $galleryPost = array_map('trim', $galleryPost);
                    $gallery     = json_encode($galleryPost);
                }

                $url = $this->config->application->api_url . 'block/detail?id=' . $block->id . '&cache=false&authorized_token=' . $authorizedToken . '&type=' . $type;
                $post = array(
                    'name' => trim(strip_tags($this->request->getPost('name'))),
                    'name_eng' => trim(strip_tags($this->request->getPost('name_eng'))),
                    'shortname' => $this->request->getPost('shortname'),
                    'project_id' => $this->request->getPost('project_id'),
                    'floor_name_list' => $floorNameList,
                    'apartment_name_list' => $apartmentNameList,
                    'direction' => $this->request->getPost('direction'),
                    'status' => $this->request->getPost('status'),
                    'apartment_count' => $this->request->getPost('apartment_count'),
                    'floor_count' => $this->request->getPost('floor_count'),
                    'description' => $this->request->getPost('description'),
                    'description_eng' => $this->request->getPost('description_eng'),
                    'policy' => $this->request->getPost('policy'),
                    'policy_eng' => $this->request->getPost('policy_eng'),
                    'gallery' => $gallery,
                    'price' => $this->request->getPost('price'),
                    'price_eng' => $this->request->getPost('price_eng'),
                    'total_area' => $this->request->getPost('total_area'),
                    'green_area' => $this->request->getPost('green_area'),
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getClientAddress(),
                    'user_id' => $userSession['id'],
                    'meta_title' => $this->request->getPost('meta_title', array('trim', 'striptags'), ''),
                    'meta_title_eng' => $this->request->getPost('meta_title_eng', array('trim', 'striptags'), ''),
                    'meta_keywords' => $this->request->getPost('meta_keywords', array('trim', 'striptags'), ''),
                    'meta_keywords_eng' => $this->request->getPost('meta_keywords_eng', array('trim', 'striptags'), ''),
                    'meta_description' => $this->request->getPost('meta_description', array('trim', 'striptags'), ''),
                    'meta_description_eng' => $this->request->getPost('meta_description_eng', array('trim', 'striptags'), '')
                );

                $defaultImage = $this->request->getPost('default_image');
                $post['default_image'] = $defaultImage;

                $post['attribute_type'] = $this->request->getPost('attribute_type');
                $post['attribute_view'] = $this->request->getPost('attribute_view');
                $post['attribute_utility'] = $this->request->getPost('attribute_utility');

                $r = json_decode(\ITECH\Data\Lib\Util::curlPostJson($url, $post), true);
                if (isset($r['result']) && count($r['result']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                    $this->flashSession->success('Cập nhật block thành công.');
                    $query = array('id' => $r['result']['id']);

                    // IMAGE
                    $messageImage = array();
                    if ($r['result']['id'] && $this->request->hasPost('galleries')) {
                        $listImage = $this->request->getPost('galleries');
                        parent::saveMapImage(array(
                            'images' => $listImage,
                            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
                            'item_id' => (int)$r['result']['id']
                        ));
                    }
                    // IMAGE

                    return $this->response->redirect(array('for' => 'block_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    if (isset($r['message'])) {
                        $this->flashSession->success($r['message']);
                    } else {
                        $this->flashSession->success('Lỗi, không thể cập nhật block.');
                    }
                }
            }
        }

        $dataAttributeType = parent::getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_TYPE);
        $dataAttributeView = parent::getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_VIEW);
        $dataAttributeUtility = parent::getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK, \ITECH\Data\Lib\Constant::BLOCK_ATTRIBUTE_TYPE_UTILITY);

        $project = array(
            'id' => $block->project_id,
            'name' => $block->project_name,
        );

        $mapImage = parent::getMapImage(array(
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK,
            'item_id' => (int)$block->id
        ));

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => $project['name'],
                'url' => $this->url->get([
                    'for' => 'project_list'
                ]),
                'active' => false
            ],
            [
                'title' => 'Danh sách Block',
                'url' => $this->url->get([
                    'for' => 'block_list',
                ]),
                'active' => false
            ],
            [
                'title' => $block->name,
                'url' => $this->url->get([
                    'for' => 'block_edit',
                    'id' => $block->id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'project' => $project,
            'result' => $block,
            'form' => $form,
            'data_attribute_type' => json_encode($dataAttributeType),
            'data_attribute_view' => json_encode($dataAttributeView),
            'data_attribute_utility' => json_encode($dataAttributeUtility),
            'projectId' => $projectId,
            'page' => $page,
            'from' => $from,
            'mapImage' => $mapImage,
            'breadcrumbs' => $breadcrumbs
        ));
        $this->view->pick(parent::$theme . '/block/edit');
    }

    public function listAttributeAction()
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $aParams = array();
        $aParams['page'] = $page;
        $aParams['limit'] = $limit;
        $aParams['module'] = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK;
        $aParams['cache'] = 'false';
        $aParams['authorized_token'] = $authorizedToken;

        $query = array();
        $query['page'] = $page;

        $attributes = array();
        $url = $this->config->application->api_url . 'attribute/list';
        $url = $url . '?' . http_build_query($aParams);

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $attributes = $r;
        }

        $url = $this->url->get(array('for' => 'block_list_attribute'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($attributes['total_pages']) ? $attributes['total_pages'] : 0,
            'page' => $page,
            'pages_display' => 3
        );
        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách thuộc tính',
                'url' => $this->url->get([
                    'for' => 'block_list_attribute'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'attributes' => $attributes
        ));
        $this->view->pick(parent::$theme . '/block/list_attribute');
    }

    public function deleteAction()
    {
        //$userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$block) {
            throw new \Phalcon\Exception('Không tồn tại sản phẩm này.');
        }

        $block->status = \ITECH\Data\Lib\Constant::CATEGORY_STATUS_REMOVED;
        $this->db->begin();
        try {
            if (!$block->update()) {
                $messages = $block->getMessages();
                if (isset($messages[0])) {
                    $this->flashSession->error($messages[0]->getMessage());
                }
                $this->db->rollback;
            } else {
                $this->db->commit();
                $results = \ITECH\Data\Model\ApartmentModel::find(array(
                    'conditions' => 'block_id = :block_id: AND status = :status:',
                    'bind' => array(
                        'block_id' => $block->id,
                        'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                    )
                ));

                if (count($results)) {
                    foreach ($results as $item) {
                        $apartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array(
                                'id' => $item->id
                            )
                        ));

                        if ($apartment) {
                            $apartment->status = \ITECH\Data\Lib\Constant::APARTMENT_STATUS_REMOVED;
                            if (!$apartment->update()) {
                                $messages = $apartment->getMessages();
                                if (isset($messages[0])) {
                                    $this->logger->log('[BlockController][deleteAction]: ' . $messages[0]->getMessage(), \Phalcon\Logger::ERROR);
                                }

                                $this->logger->log('[BlockController][deleteAction]: Lỗi xóa sản phẩm ' . $apartment->id . 'từ ' . $block->id, \Phalcon\Logger::ERROR);
                            }
                        }

                    }
                }

                $this->flashSession->success('Xóa thành công Block này và những sản phẩm cùng Block thành công.');
            }
        } catch (\Phalcon\Exception $e) {
            $this->db->rollback;
            $this->logger->log('[CategoryController][deleteAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
        }

        return $this->response->redirect(array('for' => 'block_list'));
    }

    public function mapImageCloneAction()
    {
        $userSession = $this->session->get('USER');
        $mapImageId = $this->request->getQuery('map_image_id', array('int'), '');
        $blockId = $this->request->getQuery('block_id', array('int'), '');
        $floorNumber = $this->request->getQuery('floor_number', array('int'), '');

        $mapImage = \ITECH\Data\Model\MapImageModel::findFirst(array(
            'conditions' => 'id = :id:
                AND item_id = :item_id:
                AND position = :position:
                AND module = :module:',
            'bind' => array(
                'id' => $mapImageId,
                'item_id' => $blockId,
                'position' => \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_MAP,
                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK
            )
        ));

        if (!$mapImage) {
            throw new \Exception('Không tồn tại hình ảnh chi tiết này.');
        }

        $block = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $blockId,
                'status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE
            )
        ));
        if (!$block) {
            throw new \Exception('Không tồn tại block này.');
        }

        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => array(
                'id' => $block->project_id,
                'status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE
            )
        ));
        if (!$project) {
            throw new \Exception('Không tồn tại dự án này.');
        }

        // --------- Floor list
        $maps = \ITECH\Data\Model\MapImageModel::find(array(
            'conditions' => 'item_id = :item_id:
                AND type = :type:
                AND module = :module:',
            'bind' => array(
                'item_id' => $blockId,
                'type' => \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_MAP_VIEW,
                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_BLOCK
            )
        ));

        $currentFloor = $mapImage->floor;

        if ($floorNumber == $currentFloor) {
            $floorNumber = '';
        }

        $usedFloors = array();
        $floorSelect = array();

        if (count($maps)) {
            foreach ($maps as $item) {
                $usedFloors[] = $item->floor;
            }
        }

        $floors = json_decode($block->floor_name_list, true);

        if (count($floors)) {
            foreach ($floors as $key => $value) {
                if ($currentFloor == $key) {
                    $currentFloor = $value;
                }

                if (!in_array($key, array_values($usedFloors))) {
                    $floorSelect[$key] = $value;
                }
            }
        } else {
            for ($i = 1; $i <= $block->floor_count; $i++) {
                if (!in_array($i, array_values($usedFloors))) {
                    $floorSelect[$i] = $i;
                }
            }
        }

        foreach ($floorSelect as $key => $value) {
            if ($value) {}

            $hasApartment = \ITECH\Data\Model\ApartmentModel::count(array(
                'conditions' => 'block_id = :block_id:
                    AND floor = :floor:
                    AND status = :status:',
                'bind' => array(
                    'block_id' => $blockId,
                    'floor' => $key,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));
            if ($hasApartment == 0) {
                unset($floorSelect[$key]);
            }
        }
        // Floor list ---------

        $apartments = \ITECH\Data\Model\ApartmentModel::find(array(
            'conditions' => 'block_id = :block_id:
                AND floor = :floor_count:
                AND status = :status:',
            'bind' => array(
                'block_id' => $blockId,
                'floor_count' => $mapImage->floor,
                'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
            )
        ));

        $newApartments = array();
        if ($floorNumber != '') {
            $newApartments = \ITECH\Data\Model\ApartmentModel::find(array(
                'conditions' => 'block_id = :block_id:
                    AND floor = :floor_count:
                    AND status = :status:',
                'bind' => array(
                    'block_id' => $blockId,
                    'floor_count' => $floorNumber,
                    'status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE
                )
            ));
        }

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (!isset($post['map_item_id'])) {
                $this->flashSession->error('Thông tin không hợp lệ. Vui lòng chọn các sản phẩm');
            } else {
                $mapItemId = $post['map_item_id'];

                $invalid = false;
                foreach ($post['map_item_id'] as $item) {
                    if ($item == '') {
                        $invalid = true;
                        break;
                    }
                }

                if ($invalid) {
                    $this->flashSession->error('Thông tin không hợp lệ. Vui lòng chọn tất cả các sản phẩm tương ứng');
                } else {
                    if (isset($post['floor'])) {
                        if ($post['floor'] == '') {
                            $this->flashSession->error('Thông tin không hợp lệ. Chưa cung cấp tầng');
                        } else {
                            $newFloor = (int)$post['floor'];

                            if (!in_array($newFloor, array_values($usedFloors))) {
                                $newMapImage = new \ITECH\Data\Model\MapImageModel();
                                $newMapImage->item_id = $mapImage->item_id;
                                $newMapImage->type = \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_FLOOR;
                                $newMapImage->position = \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_MAP;
                                $newMapImage->floor = $newFloor;
                                $newMapImage->image = $mapImage->image;
                                $newMapImage->module = $mapImage->module;
                                $newMapImage->created_by = $userSession['id'];
                                $newMapImage->created_at = date('Y-m-d H:i:s');

                                if (!$newMapImage->save()) {
                                    $messages = $newMapImage->getMessages();
                                    if (isset($messages[0])) {
                                        $this->flashSession->error($messages[0]->getMessage());
                                    }
                                }

                                $imageMapId = $newMapImage->id;

                                $maps = \ITECH\Data\Model\MapModel::find(array(
                                    'conditions' => 'map_image_id = :map_image_id:',
                                    'bind' => array('map_image_id' => $mapImage->id)
                                ));
                                if (count($maps)) {
                                    foreach ($maps as $item) {
                                        if (isset($mapItemId[$item->item_id])) {
                                            $mapModel = new \ITECH\Data\Model\MapModel();
                                            $mapModel->image_map_id = $imageMapId;
                                            $mapModel->item_id = $mapItemId[$item->item_id];
                                            $mapModel->map = $item->map;
                                            $newMapImage->type = \ITECH\Data\Lib\Constant::MAP_IMAGE_TYPE_FLOOR;
                                            $newMapImage->position = \ITECH\Data\Lib\Constant::MAP_IMAGE_POSITION_MAP;
                                            $mapModel->save();
                                        }
                                    }
                                }

                                $this->flashSession->success('Sao chép thành công.');
                                return $this->response->redirect(array('for' => 'block_edit', 'query' => '?' . http_build_query(array('id' => $blockId, 'project_id' => $block->project_id))));
                            }
                        }
                    }
                }
            }
        }

        $this->view->setVars(array(
            'mapImage' => $mapImage,
            'blockModel' => $block,
            'project' => $project,
            'currentFloor' => $currentFloor,
            'floorSelect' => $floorSelect,
            'floorNumber' => $floorNumber,
            'apartments' => $apartments,
            'newApartments' => $newApartments
        ));
        $this->view->pick(parent::$theme . '/block/map_image_clone');
    }

}
