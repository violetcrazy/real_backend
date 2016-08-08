<?php
namespace ITECH\Admin\Controller;

class ApartmentController extends \ITECH\Admin\Controller\BaseController
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
        $projects = \ITECH\Data\Model\ProjectModel::find(array(
            'conditions' => 'status = :status:',
            'bind' => array('status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE)
        ));

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách sản phẩm',
                'url' => $this->url->get([
                    'for' => 'apartment_list'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'projects' => $projects,
        ));
        $this->view->pick(parent::$theme . '/apartment/index');
    }

    public function listApartmentAction()
    {
        if ($this->request->get('project_id')){
            $params = array(
                "conditions" => array(
                    "project_id" => $this->request->get('project_id')
                )
            );
        } else {
            $params = array(
                "limit" => 5000
            );
        }

        $apartmentRepo = new \ITECH\Data\Repo\ApartmentRepo();
        $apartments = $apartmentRepo->getList($params);
        $out = array();
        if (count($apartments) > 0) {
            foreach ($apartments as $apartment) {
                $urlEditApartment = '<a role="menuitem" href="'. $this->url->get(array('for' => 'apartment_edit', 'query' => '?' .  http_build_query(array('block_id' => $apartment->block_id, 'id' => $apartment->id )) )) .'">'. $apartment->name .'</a>';
                $urlDeleteApartment = '<div class="text-center"><a href="'. $this->url->get(array('for' => 'apartment_delete', 'query' => '?' . http_build_query(array('id' => $apartment->id)) )) .'" onclick="return confirm(\'Đồng ý xoá.?\');" class="btn btn-xs btn-bricky"><i class="fa fa-times fa fa-white"></i></a></div>';
                $urlEditBlock = '<a href="'. $this->url->get(array("for" => "block_edit", "query" => "?" . http_build_query(array("id" => $apartment->block_id)) )) .'">'. $apartment->block_name .'</a>';
                $urlEditProject = '<a href="'. $this->url->get(array("for" => "project_edit", "query" => "?" . http_build_query(array("id" => $apartment->project_id)) )) .'">'. $apartment->project_name .'</a>';

                $getCondition = \ITECH\Data\Lib\Constant::getApartmentCondition();
                $condition = '<div class="text-center"><span class="icon-condition-0"></span><span class="hidden">Không xác định</span></div>';
                if ($apartment->condition > 0) {
                    $condition = '<div class="text-center"><span class="icon-condition-'.$apartment->condition.'"></span><span class="hidden">' . $getCondition[$apartment->condition] . '</span></div>';
                }

                $getStatus = \ITECH\Data\Lib\Constant::getApartmentStatus();

                $out['data'][] = array(
                    $apartment->id,
                    $urlEditApartment,
                    $urlEditBlock,
                    $urlEditProject,
                    $apartment->condition == \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE ? $condition : '',
                    $apartment->condition == \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_HOLD ? $condition : '',
                    $apartment->condition == \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD ? $condition : '',
                    $apartment->condition == 0 || $apartment->condition == \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_OTHER ? $condition : '',
                    '<div class="text-center"><span class="icon-status-'.$apartment->status.'"></span><span class="text-status-'.$apartment->status.'">'. $getStatus[$apartment->status] .'</span></span></div>',
                    $urlDeleteApartment
                );
            }
        } else {
            $out['data'][] = array('','','','','','','','','','');
        }
        parent::outputJSON($out);
    }

    public function addAction()
    {
        $userSession = $this->session->get('USER');
        $blockId = $this->request->getQuery('block_id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $from = $this->request->getQuery('from', array('striptags', 'trim', 'lower'), '');
        if ($this->request->hasPost('block_id')) {
            $blockId = $this->request->getPost('block_id', array('int'), -1);
        }
        // Get block ---------
        $block = \ITECH\Data\Model\BlockModel::findFirst($blockId);
//        if (!$block) {
//            throw new \Phalcon\Exception('Không tồn tại block này.');
//        }
        // --------- Get block

        $apartment = new \ITECH\Data\Model\ApartmentModel();
        $form = new \ITECH\Admin\Form\ApartmentForm();

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $apartment);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {

                $checkApartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
                    'conditions' => 'name = :apartment_name: AND block_id = :block_id:',
                    'bind' => array(
                        'apartment_name'=> $this->request->getPost('name'),
                        'block_id' => $block->id
                    )
                ));
                if ($checkApartment) {
                    throw new \Phalcon\Exception('Tên sản phẩm đã tồn tại.');
                }

                $position = array(
                    'image' => '',
                    'description' => ''
                );
                $position['image'] = $this->request->getPost('position_image');
                $position['description'] = $this->request->getPost('position');
                $position = json_encode($position);

                $positionEng['image'] = $this->request->getPost('position_image');
                $positionEng['description'] = $this->request->getPost('position_eng');
                $positionEng = json_encode($positionEng);

                $apartment->block_id = $block->id;
                $apartment->name = $this->request->getPost('name');
                $apartment->name_eng = $this->request->getPost('name_eng');
                $apartment->slug = \ITECH\Data\Lib\Util::slug($this->request->getPost('name'));
                $apartment->slug_eng = \ITECH\Data\Lib\Util::slug($this->request->getPost('name_eng'));
                $apartment->user_id = $this->request->getPost('user_id');
                $apartment->type = $this->request->getPost('type');
                $apartment->price = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price'));
                $apartment->price_eng = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price_eng'));
                $apartment->price_sale_off = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price_sale_off'));
                $apartment->price_sale_off_eng = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price_sale_off_eng'));
                $apartment->description = $this->request->getPost('description');
                $apartment->description_eng = $this->request->getPost('description_eng');
                $apartment->total_area = $this->request->getPost('total_area');
                $apartment->green_area = $this->request->getPost('green_area');
                $apartment->rose = $this->request->getPost('rose');
                $apartment->floor = $this->request->getPost('floor');
                $apartment->bedroom_count = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('bedroom_count'));
                $apartment->bathroom_count = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('bathroom_count'));
                $apartment->adults_count = $this->request->getPost('adults_count');
                $apartment->children_count = $this->request->getPost('children_count');
                $apartment->ordering = $this->request->getPost('ordering');
                $apartment->status = $this->request->getPost('status');
                $apartment->panorama_image = $this->request->getPost('panorama_image');
                $apartment->position = $position;
                $apartment->position_eng = $positionEng;
                $apartment->default_image = $this->request->getPost('default_image');
                $apartment->direction = $this->request->getPost('direction');
                $apartment->updated_at = date('Y-m-d H:i:s');
                $apartment->created_at = date('Y-m-d H:i:s');
                $apartment->created_by = $userSession['id'];

                $apartment->meta_title = $this->request->getPost('meta_title', array('trim', 'striptags'), '');
                $apartment->meta_title_eng = $this->request->getPost('meta_title_eng', array('trim', 'striptags'), '');
                $apartment->meta_keywords = $this->request->getPost('meta_keywords', array('trim', 'striptags'), '');
                $apartment->meta_keywords_eng = $this->request->getPost('meta_keywords_eng', array('trim', 'striptags'), '');
                $apartment->meta_description = $this->request->getPost('meta_description', array('trim', 'striptags'), '');
                $apartment->meta_description_eng = $this->request->getPost('meta_description_eng', array('trim', 'striptags'), '');

                $apartment->furniture_id = ($this->request->getPost('furniture_id') != "") ? $this->request->getPost('furniture_id') : NULL;

                $attributes = array();
                if ($this->request->getPost('attribute_type') != '') {
                    $array = array_filter(array_unique(explode(',', $this->request->getPost('attribute_type'))));
                    foreach ($array as $item) {
                        $item = trim($item);

                        if ($item != '') {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'name = :attribute_name:
                                    AND type = :attribute_type:',
                                'bind' => array(
                                    'attribute_name' => $item,
                                    'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                )
                            ));

                            if ($attribute) {
                                $attributes[] = $attribute->id;
                            }
                        }
                    }
                }

                if ($this->request->getPost('attribute_view') != '') {
                    $array = array_filter(array_unique(explode(',', $this->request->getPost('attribute_view'))));
                    foreach ($array as $item) {
                        $item = trim($item);

                        if ($item != '') {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' =>
                                    'name = :attribute_name:
                                    AND type = :attribute_type:',
                                'bind' => array(
                                    'attribute_name' => $item,
                                    'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                )
                            ));

                            if ($attribute) {
                                $attributes[] = $attribute->id;
                            }


                        }
                    }
                }

                if ($this->request->getPost('attribute_utility') != '') {
                    $array = array_filter(array_unique(explode(',', $this->request->getPost('attribute_utility'))));
                    foreach ($array as $item) {
                        $item = trim($item);

                        if ($item != '') {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' =>
                                    'name = :attribute_name:
                                    AND type = :attribute_type:',
                                'bind' => array(
                                    'attribute_name' => $item,
                                    'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                )
                            ));

                            if ($attribute) {
                                $attributes[] = $attribute->id;
                            }
                        }
                    }
                }

                try{
                    if ($apartment->save()) {
                        $this->flashSession->success('Thêm sản phẩm thành công.');
                        // IMAGE
                        if ($apartment->id && $this->request->hasPost('galleries')) {
                            $listImage = $this->request->getPost('galleries');
                            parent::saveMapImage(array(
                                'images' => $listImage,
                                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT,
                                'item_id' => (int)$apartment->id
                            ));
                        }
                        // IMAGE
                        $query = array(
                            'id' => $apartment->id,
                            'block_id' => $block->id
                        );

                        if (count($attributes)) {
                            foreach ($attributes as $item) {
                                $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                                $apartmentAttribute->apartment_id = $apartment->id;
                                $apartmentAttribute->attribute_id = $item;
                                $apartmentAttribute->save();
                            }
                        }

                        return $this->response->redirect(array('for' => 'apartment_edit', 'query' => '?' . http_build_query($query)));
                    } else {
                        $this->flashSession->error(implode(', ', $messages = $apartment->getMessages()));
                    }
                } catch (\Phalcon\Exception $e) {
                    $this->logger->log('[ApartmentController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                    $this->flashSession->error($e->getMessage());
                }
            }
        }

        // Get Attr
        $attribute = \ITECH\Data\Model\AttributeModel::find(array(
            'conditions' => 'status = :attribute_status: ',
            'bind' => array(
                'attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
            )
        ));

        $propertyTypeVie = '';
        $propertyViewVie = '';
        $propertyUtilityVie = '';

        if (count($attribute)) {
            $arrayTypeVie = array();
            $arrayViewVie = array();
            $arrayUtilityVie = array();

            foreach ($attribute as $item) {
                if ($item->type == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE) {
                    $arrayTypeVie[] = '"' . addslashes($item->name) . '"';
                }

                if ($item->type == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW) {
                    $arrayViewVie[] = '"' . addslashes($item->name) . '"';
                }

                if ($item->type == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY) {
                    $arrayUtilityVie[] = '"' . addslashes($item->name) . '"';
                }
            }

            if (count($arrayTypeVie)) {
                $propertyTypeVie = implode(',', $arrayTypeVie);
            }
            if (count($arrayViewVie)) {
                $propertyViewVie = implode(',', $arrayViewVie);
            }
            if (count($arrayUtilityVie)) {
                $propertyUtilityVie = implode(',', $arrayUtilityVie);
            }
        }
        // Get Attr

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => $block->project->name,
                'url' => $this->url->get([
                    'for' => 'project_edit',
                    'query' => '?id=' . $block->project->id
                ]),
                'active' => false
            ],
            [
                'title' => $block->name,
                'url' => $this->url->get([
                    'for' => 'block_edit',
                    'query' => '?id=' . $block->id
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm sản phẩm',
                'url' => $this->url->get([
                    'for' => 'apartment_add'
                ]),
                'active' => true
            ]
        ];

        if (!isset($block['id'])) {
            $breadcrumbs[1] = [
                'title' => 'Danh sách dự án',
                'url' => $this->url->get([
                    'for' => 'project_list'
                ]),
                'active' => false
            ];
            $breadcrumbs[2] = [
                'title' => 'Danh sách Block/Khu',
                'url' => $this->url->get([
                    'for' => 'block_list'
                ]),
                'active' => false
            ];
        }

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'blocks' => $block,
            'block_detail' => $block,
            'from' => $from,
            'form' => $form,
            'page' => $page,
            'projects' => $projects,
            'propertyTypeVie' => $propertyTypeVie,
            'propertyViewVie' => $propertyViewVie,
            'propertyUtilityVie' => $propertyUtilityVie,
        ));
        $this->view->pick(parent::$theme . '/apartment/edit');
    }

    public function editAction()
    {
        $projectId = $this->request->getQuery('project_id', array('int'), '');
        $blockId = $this->request->getQuery('block_id', array('int'), '');
        $id = $this->request->getQuery('id', array('int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $from = $this->request->getQuery('from', array('striptags', 'trim', 'lower'), '');

        if ($this->request->hasPost('block_id')) {
            $blockId = $this->request->getPost('block_id', array('int'), -1);
        }
        // Get block ---------
        $block = \ITECH\Data\Model\BlockModel::findFirst($blockId);
        if (!$block) {
            throw new \Phalcon\Exception('Không tồn tại block này.');
        }
        // --------- Get block

        $apartment = \ITECH\Data\Model\ApartmentModel::findFirst($id);
        if ($apartment) {
            $position = $apartment->position;
            if ($position) {
                $position = json_decode($position, true);
                $apartment->position = $position['description'];
                $apartment->position_image = $position['image'];
            }
            $positionEng = $apartment->position_eng;
            if ($positionEng) {
                $positionEng = json_decode($positionEng, true);
                $apartment->position_eng = $positionEng['description'];
            }

        } else {
            throw new \Phalcon\Exception('Lỗi, không tồn tại sản phẩm này');
        }

        // Get Attr
        $attribute = \ITECH\Data\Model\AttributeModel::find(array(
            'conditions' => 'status = :attribute_status: ',
            'bind' => array(
                'attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
            )
        ));

        $propertyTypeVie = '';
        $propertyViewVie = '';
        $propertyUtilityVie = '';

        if (count($attribute)) {
            $arrayTypeVie = array();
            $arrayViewVie = array();
            $arrayUtilityVie = array();

            foreach ($attribute as $item) {
                if ($item->type == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE) {
                    $arrayTypeVie[] = '"' . addslashes($item->name) . '"';
                }

                if ($item->type == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW) {
                    $arrayViewVie[] = '"' . addslashes($item->name) . '"';
                }

                if ($item->type == \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY) {
                    $arrayUtilityVie[] = '"' . addslashes($item->name) . '"';
                }
            }

            if (count($arrayTypeVie)) {
                $propertyTypeVie = implode(',', $arrayTypeVie);
            }
            if (count($arrayViewVie)) {
                $propertyViewVie = implode(',', $arrayViewVie);
            }
            if (count($arrayUtilityVie)) {
                $propertyUtilityVie = implode(',', $arrayUtilityVie);
            }
        }
        // Get Attr

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

        $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));
        $b->andWhere('a1.id = :apartment_id:', array('apartment_id' => $apartment->id));

        $result = $b->getQuery()->execute();
        $apartmentPropertyTypeVie = '';
        $apartmentPropertyViewVie = '';
        $apartmentPropertyUtilityVie = '';

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
                $apartmentPropertyTypeVie = implode(', ', $arrayTypeVie);
            }

            if (count($arrayViewVie)) {
                $apartmentPropertyViewVie = implode(', ', $arrayViewVie);
            }

            if (count($arrayUtilityVie)) {
                $apartmentPropertyUtilityVie = implode(', ', $arrayUtilityVie);
            }
        }
        // Apartment Attributes ---------

        $requestInfo = array();
        if ($apartment->condition == \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD) {
            $apartmentRequestModel = new \ITECH\Data\Model\ApartmentRequestModel();

            $b = $apartmentRequestModel->getModelsManager()->createBuilder();
            $b->columns(array(
                'ar1.id AS apartment_request_id',
                'u1.id AS user_id',
                'u1.name AS user_name',
                'u1.email AS user_email',
                'u1.phone AS user_phone'
            ));

            $b->from(array('ar1' => 'ITECH\Data\Model\ApartmentRequestModel'));
            $b->innerJoin('ITECH\Data\Model\UserModel', 'u1.id = ar1.user_id', 'u1');
            $b->innerJoin('ITECH\Data\Model\ApartmentModel', 'a1.id = ar1.apartment_id', 'a1');

            $b->andWhere('a1.id = :apartment_id:', array('apartment_id' => $apartment->id));
            $b->andWhere('ar1.status = :apartment_request_approved:', array('apartment_request_approved' => \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_APPROVED));

            $result = $b->getQuery()->execute();
            if (isset($result[0])) {
                $requestInfo = $result[0];
            }
        }

        $form = new \ITECH\Admin\Form\ApartmentForm($apartment);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $apartment);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $position = array(
                    'image' => '',
                    'description' => ''
                );
                $position['image'] = $this->request->getPost('position_image');
                $position['description'] = $this->request->getPost('position');
                $position = json_encode($position);

                $positionEng = array(
                    'image' => '',
                    'description' => ''
                );
                $positionEng['image'] = $this->request->getPost('position_image');
                $positionEng['description'] = $this->request->getPost('position_eng');
                $positionEng = json_encode($positionEng);

                $apartment->block_id = $this->request->getPost('block_id');
                $apartment->name = $this->request->getPost('name');
                $apartment->name_eng = $this->request->getPost('name_eng');
                $apartment->user_id = $this->request->getPost('user_id');
                $apartment->type = $this->request->getPost('type');
                $apartment->price = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price'));
                $apartment->price_eng = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price_eng'));
                $apartment->price_sale_off = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price_sale_off'));
                $apartment->price_sale_off_eng = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('price_sale_off_eng'));
                $apartment->description = $this->request->getPost('description');
                $apartment->description_eng = $this->request->getPost('description_eng');
                $apartment->total_area = $this->request->getPost('total_area');
                $apartment->green_area = $this->request->getPost('green_area');
                $apartment->rose = $this->request->getPost('rose');
                $apartment->floor = $this->request->getPost('floor');
                $apartment->bedroom_count = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('bedroom_count'));
                $apartment->bathroom_count = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('bathroom_count'));
                $apartment->adults_count = $this->request->getPost('adults_count');
                $apartment->children_count = $this->request->getPost('children_count');
                $apartment->ordering = $this->request->getPost('ordering');
                $apartment->status = $this->request->getPost('status');
                $apartment->panorama_image = $this->request->getPost('panorama_image');
                $apartment->position = $position;
                $apartment->position_eng = $positionEng;
                $apartment->direction = $this->request->getPost('direction');
                $apartment->furniture_id = ($this->request->getPost('furniture_id') != "") ? $this->request->getPost('furniture_id') : NULL;

                $apartment->meta_title = $this->request->getPost('meta_title', array('trim', 'striptags'), '');
                $apartment->meta_title_eng = $this->request->getPost('meta_title_eng', array('trim', 'striptags'), '');
                $apartment->meta_keywords = $this->request->getPost('meta_keywords', array('trim', 'striptags'), '');
                $apartment->meta_keywords_eng = $this->request->getPost('meta_keywords_eng', array('trim', 'striptags'), '');
                $apartment->meta_description = $this->request->getPost('meta_description', array('trim', 'striptags'), '');
                $apartment->meta_description_eng = $this->request->getPost('meta_description_eng', array('trim', 'striptags'), '');

                $attributes = array();
                if ($this->request->getPost('attribute_type') != '') {
                    $array = array_filter(array_unique(explode(',', $this->request->getPost('attribute_type'))));
                    foreach ($array as $item) {
                        $item = trim($item);

                        if ($item != '') {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' => 'name = :attribute_name:
                                    AND type = :attribute_type:',
                                'bind' => array(
                                    'attribute_name' => $item,
                                    'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                )
                            ));

                            if ($attribute) {
                                $attributes[] = $attribute->id;
                            }
                        }
                    }
                }

                if ($this->request->getPost('attribute_view') != '') {
                    $array = array_filter(array_unique(explode(',', $this->request->getPost('attribute_view'))));
                    foreach ($array as $item) {
                        $item = trim($item);

                        if ($item != '') {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' =>
                                    'name = :attribute_name:
                                    AND type = :attribute_type:',
                                'bind' => array(
                                    'attribute_name' => $item,
                                    'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                )
                            ));

                            if ($attribute) {
                                $attributes[] = $attribute->id;
                            }


                        }
                    }
                }

                if ($this->request->getPost('attribute_utility') != '') {
                    $array = array_filter(array_unique(explode(',', $this->request->getPost('attribute_utility'))));
                    foreach ($array as $item) {
                        $item = trim($item);

                        if ($item != '') {
                            $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                                'conditions' =>
                                    'name = :attribute_name:
                                    AND type = :attribute_type:',
                                'bind' => array(
                                    'attribute_name' => $item,
                                    'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                )
                            ));

                            if ($attribute) {
                                $attributes[] = $attribute->id;
                            }
                        }
                    }
                }

                if ($apartment->id) {
                    $apartmentAttributeModel = new \ITECH\Data\Model\ApartmentAttributeModel();

                    $q = 'DELETE FROM `land_apartment_attribute` WHERE `apartment_id` = "' . $apartment->id . '"';
                    $apartmentAttributeModel->getWriteConnection()->query($q);
                }

                if (count($attributes)) {
                    foreach ($attributes as $item) {
                        $apartmentAttribute = new \ITECH\Data\Model\ApartmentAttributeModel();
                        $apartmentAttribute->apartment_id = $apartment->id;
                        $apartmentAttribute->attribute_id = $item;
                        $apartmentAttribute->save();
                    }
                }

                try{
                    if ($apartment->update()) {
                        $this->flashSession->success('Cập nhật sản phẩm thành công.');
                        $query = array(
                            'id' => $apartment->id,
                            'block_id' => $block->id
                        );

                        // IMAGE
                        if ($apartment->id && $this->request->hasPost('galleries')) {
                            $listImage = $this->request->getPost('galleries');
                            parent::saveMapImage(array(
                                'images' => $listImage,
                                'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT,
                                'item_id' => (int)$apartment->id
                            ));
                        }
                        // IMAGE

                        return $this->response->redirect(array('for' => 'apartment_edit', 'query' => '?' . http_build_query($query)));
                    } else {
                        $this->flashSession->error(implode(', ', $messages = $apartment->getMessages()));
                    }
                } catch (\Phalcon\Exception $e) {
                    $this->logger->log('[ApartmentController][DetailAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
                    $this->flashSession->error($e->getMessage());
                }
            }
        }

        $mapImage = parent::getMapImage(array(
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_APARTMENT,
            'item_id' => (int)$apartment->id
        ));
        //var_dump($block->project->id); die;

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => $block->project->name,
                'url' => $this->url->get([
                    'for' => 'project_edit',
                    'query' => '?id='. $block->project->id
                ]),
                'active' => false
            ],
            [
                'title' => $block->name,
                'url' => $this->url->get([
                    'for' => 'block_edit',
                    'query' => '?id='. $block->id
                ]),
                'active' => false
            ],
            [
                'title' => $apartment->name,
                'url' => $this->url->get([
                    'for' => 'apartment_edit',
                    'id' => $apartment->id,
                    'block_id' => $block->id
                ]),
                'active' => true
            ]
        ];

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'blocks' => $block,
            'projects' => $projects,
            'block_detail' => $block,
            'apartment' => $apartment,
            'requestInfo' => $requestInfo,
            'form' => $form,
            'mapImage' => $mapImage,
            'projectId' => $projectId,
            'page' => $page,
            'apartmentPropertyType' => $apartmentPropertyTypeVie,
            'apartmentPropertyView' => $apartmentPropertyViewVie,
            'apartmentPropertyUtility' => $apartmentPropertyUtilityVie,
            'from' => $from,
            'propertyTypeVie' => $propertyTypeVie,
            'propertyViewVie' => $propertyViewVie,
            'propertyUtilityVie' => $propertyUtilityVie,
        ));
        $this->view->pick(parent::$theme . '/apartment/edit');
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
        $aParams['module'] = \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT;
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

        $url = $this->url->get(array('for' => 'apartment_list_attribute'));
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
                    'for' => 'apartment_list_attribute'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'attributes' => $attributes
        ));
        $this->view->pick(parent::$theme . '/apartment/list_attribute');
    }

    public function deleteAction()
    {
        //$userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');
        $projectId = $this->request->getQuery('project_id', array('int'), '');
        $from = $this->request->getQuery('from', array('striptags', 'trim'), '');

        $apartment = \ITECH\Data\Model\ApartmentModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$apartment) {
            throw new \Phalcon\Exception('Không tồn tại sản phẩm này.');
        }

        $apartment->status = \ITECH\Data\Lib\Constant::APARTMENT_STATUS_REMOVED;
        $this->db->begin();

        try {
            if (!$apartment->update()) {
                $messages = $apartment->getMessages();
                if (isset($messages[0])) {
                    $this->flashSession->error($messages[0]->getMessage());
                }

                $this->db->rollback;
            } else {
                $this->db->commit();
                $this->flashSession->success('Xóa thành công.');
            }

            if ($from != 'list-by-block') {
                return $this->response->redirect(array('for' => 'apartment_list'));
            } elseif ($from == 'list-by-project') {
                return $this->response->redirect(array('for' => 'apartment_list_by_project', 'query' => '?' . http_build_query(array('project_id' => $projectId))));
            } else {
                return $this->response->redirect(array('for' => 'apartment_list_by_block', 'query' => '?' . http_build_query(array('block_id' => $apartment->block_id))));
            }
        } catch (\Phalcon\Exception $e) {
            $this->db->rollback;
            $this->logger->log('[ApartmentController][deleteAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
        }
    }

    public function requestListAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $filter = $this->request->getQuery('filter', array('int'), 2);
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array('apartment_request_status' => \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_WAITING),
            'page' => $page,
            'limit' => $limit
        );

        $query = array('page' => $page);

        if ($filter != '') {
            $params['conditions']['apartment_request_status'] = $filter;
            $query['filter'] = $filter;
        }

        $apartmentRequestRepo = new \ITECH\Data\Repo\ApartmentRequestRepo();
        $requests = $apartmentRequestRepo->getPagination($params);

        $url = $this->url->get(array('for' => 'apartment_request_list'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($requests->total_pages) ? $requests->total_pages : 0,
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
                'title' => 'Danh sách yêu cầu',
                'url' => $this->url->get([
                    'for' => 'apartment_request_list'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'filter' => $filter,
            'requests' => $requests,
            'paginationLayout' => $paginationLayout
        ));
        $this->view->pick(parent::$theme . '/apartment/request_list');
    }

    public function requestEditAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $id = $this->request->getQuery('id', array('int'), '');

        $apartmentRequest = \ITECH\Data\Model\ApartmentRequestModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$apartmentRequest) {
            throw new \Exception('Không tồn tại yêu cầu này.');
        }

        $user = $apartmentRequest->getUser();
        if (!$user) {
            throw new \Exception('Không tồn tại thành viên này.');
        }

        $agent = $apartmentRequest->getAgent();
        if (!$agent) {
            throw new \Exception('Không tồn tại đại lý này.');
        }

        $apartment = $apartmentRequest->getApartment();
        if (!$apartment) {
            throw new \Exception('Không tồn tại sản phẩm này.');
        }

        $apartmentBlock = \ITECH\Data\Model\BlockModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $apartment->block_id)
        ));
        if (!$apartmentBlock) {
            throw new \Exception('Không tồn tại block này.');
        }

        $project = $apartmentBlock->getProject();
        if (!$project) {
            throw new \Exception('Không tồn tại dự án này.');
        }

        $this->view->setVars(array(
            'apartmentRequest' => $apartmentRequest,
            'user' => $user,
            'agent' => $agent,
            'apartment' => $apartment,
            'apartmentBlock' => $apartmentBlock,
            'project' => $project
        ));
        $this->view->pick(parent::$theme . '/apartment/request_edit');
    }

    public function requestApproveAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $apartmentRequest = \ITECH\Data\Model\ApartmentRequestModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$apartmentRequest) {
            throw new \Exception('Không tồn tại yêu cầu này.');
        }

        $apartment = $apartmentRequest->getApartment();
        if (!$apartment) {
            throw new \Exception('Không tồn tại sản phẩm này.');
        }

        $count = \ITECH\Data\Model\ApartmentRequestModel::count(array(
            'conditions' => 'id <> :id: AND apartment_id = :apartment_id: AND status = :approved_status:',
            'bind' => array(
                'id' => $apartmentRequest->id,
                'apartment_id' => $apartment->id,
                'approved_status' => \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_APPROVED
            )
        ));

        if ($count > 0) {
            $this->flashSession->error('Yêu cầu này không được chấp nhận.');
        } else {
            $apartment->condition = \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_SOLD;
            $apartment->updated_at = date('Y-m-d H:i:s');
            $apartment->save();

            $apartmentRequest->status = \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_APPROVED;
            $apartmentRequest->approved_by = $userSession['id'];
            $apartmentRequest->updated_at = date('Y-m-d H:i:s');

            if (!$apartmentRequest->save()) {
                $messages = $apartmentRequest->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật trạng thái yêu cầu.';
                throw new \Exception($message);
            }

            $apartmentRequestModel = new \ITECH\Data\Model\ApartmentRequestModel();

            $q = 'UPDATE land_apartment_request
                SET status = "' . \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_REJECTED . '",
                    approved_by = "' . $userSession['id'] . '",
                    updated_at = "' . date('Y-m-d H:i:s') . '"
                WHERE apartment_id = "' . $apartment->id . '"
                    AND id <> "' . $apartmentRequest->id . '"';
            $apartmentRequestModel->getWriteConnection()->query($q);

            $this->flashSession->success('Cập nhật trạng thái yêu cầu thành công.');
        }

        return $this->response->redirect(array('for' => 'apartment_request_list'));
    }

    public function requestRejectAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));

        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $apartmentRequest = \ITECH\Data\Model\ApartmentRequestModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$apartmentRequest) {
            throw new \Exception('Không tồn tại yêu cầu này.');
        }

        $apartment = $apartmentRequest->getApartment();
        if (!$apartment) {
            throw new \Exception('Không tồn tại sản phẩm này.');
        }

        $count = \ITECH\Data\Model\ApartmentRequestModel::count(array(
            'conditions' => 'id <> :id: AND apartment_id = :apartment_id: AND status <> :rejected_status:',
            'bind' => array(
                'id' => $apartmentRequest->id,
                'apartment_id' => $apartment->id,
                'rejected_status' => \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_REJECTED
            )
        ));

        if ($count > 0) {
        } else {
            $apartment->condition = \ITECH\Data\Lib\Constant::APARTMENT_CONDITION_AVAILABLE;
            $apartment->updated_at = date('Y-m-d H:i:s');
            $apartment->save();
        }

        $apartmentRequest->status = \ITECH\Data\Lib\Constant::APARTMENT_REQUEST_STATUS_REJECTED;
        $apartmentRequest->approved_by = $userSession['id'];
        $apartmentRequest->updated_at = date('Y-m-d H:i:s');

        if (!$apartmentRequest->save()) {
            $messages = $apartmentRequest->getMessages();
            $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật trạng thái yêu cầu.';
            throw new \Exception($message);
        }

        $this->flashSession->success('Cập nhật trạng thái yêu cầu thành công.');
        return $this->response->redirect(array('for' => 'apartment_request_list'));
    }

    public function listFurnitureAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
        ));

        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $furnitureRepo = new \ITECH\Data\Repo\FurnitureRepo();
        $params = [
            'page' => $page,
            'limit' => $limit
        ];
        $furniture = $furnitureRepo->getPaginationList($params);

        $url = $this->url->get(array('for' => 'apartment_furniture_list'));
        $query = array();
        $query['page'] = $page;
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($furniture->total_pages) ? $furniture->total_pages : 0,
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
                'title' => 'Danh sách nhà cung cấp nội thất',
                'url' => $this->url->get([
                    'for' => 'apartment_furniture_list'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'furniture' => $furniture,
            'page' => $page
        ));

        $this->view->pick(parent::$theme . '/apartment/furniture_list');
    }

    public function addFurnitureAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
        ));

        $furniture = new \ITECH\Data\Model\FurnitureModel();
        $form = new \ITECH\Admin\Form\FurnitureForm();

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $furniture);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $furniture->name = $this->request->getPost('name');
                $furniture->name_eng = $this->request->getPost('name_eng');
                $furniture->intro = $this->request->getPost('intro');
                $furniture->intro_eng = $this->request->getPost('intro_eng');
                $furniture->email = $this->request->getPost('email');
                $furniture->phone = $this->request->getPost('phone');
                $furniture->address = $this->request->getPost('address');
                $furniture->address_eng = $this->request->getPost('address_eng');
                $furniture->logo = '';
                $furniture->status = $this->request->getPost('status');
                if ($furniture->save()) {
                    $query = array(
                        'id' => $furniture->id,
                    );
                    $this->flashSession->success('Thêm nhà cung cấp nội thất thành công.');
                    return $this->response->redirect(array('for' => 'apartment_furniture_edit', 'query' => '?' . http_build_query($query)));
                } else {
                    $this->flashSession->error(implode(', ', $messages = $furniture->getMessages()));
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
                'title' => 'Thêm nhà cung cấp nội thất',
                'url' => $this->url->get([
                    'for' => 'apartment_furniture_add'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/apartment/furniture_add');
    }

    public function editFurnitureAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
        ));

        $id = $this->request->getQuery('id', array('int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $from = $this->request->getQuery('from', array('striptags', 'trim', 'lower'), '');

        $furniture = \ITECH\Data\Model\FurnitureModel::findFirst([
           'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ]);

        $form = new \ITECH\Admin\Form\FurnitureForm($furniture);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $furniture);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $furniture->name = $this->request->getPost('name');
                $furniture->name_eng = $this->request->getPost('name_eng');
                $furniture->intro = $this->request->getPost('intro');
                $furniture->intro_eng = $this->request->getPost('intro_eng');
                $furniture->email = $this->request->getPost('email');
                $furniture->phone = $this->request->getPost('phone');
                $furniture->address = $this->request->getPost('address');
                $furniture->address_eng = $this->request->getPost('address_eng');
                $furniture->logo = '';
                $furniture->status = $this->request->getPost('status');
                if ($furniture->update()) {
                    $this->flashSession->success('Cập nhật nhà cung cấp nội thất thành công.');
                } else {
                    $this->flashSession->error(implode(', ', $messages = $furniture->getMessages()));
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
                'title' => 'Danh sách nhà cung cấp nội thất',
                'url' => $this->url->get([
                    'for' => 'apartment_furniture_list',
                ]),
                'active' => false
            ],
            [
                'title' => $furniture->name,
                'url' => $this->url->get([
                    'for' => 'apartment_furniture_edit',
                    'id' => $id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'page' => $page
        ));
        $this->view->pick(parent::$theme . '/apartment/furniture_edit');
    }

    public function deleteFurnitureAction()
    {
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_EDITOR
        ));

        $id = $this->request->getQuery('id', array('int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);

        $furniture = \ITECH\Data\Model\FurnitureModel::findFirst([
           'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ]);

        if ($furniture->delete()) {
            $query = array(
                'page' => $page,
            );
            $this->flashSession->success('Xóa nhà cung cấp nội thất thành công.');
            return $this->response->redirect(array('for' => 'apartment_furniture_list', 'query' => '?' . http_build_query($query)));
        } else {
            $query = array(
                'page' => $page,
            );
            $this->flashSession->success('Lỗi, không thể xóa, vui lòng thử lại.');
            return $this->response->redirect(array('for' => 'apartment_furniture_list', 'query' => '?' . http_build_query($query)));
        }
    }
}
