<?php
namespace ITECH\Admin\Controller;

class ProjectController extends \ITECH\Admin\Controller\BaseController
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

    public function indexAction()
    {
        $projectIds = parent::getPermissionProjects();

        if (!is_array($projectIds)) {
            $projects = \ITECH\Data\Model\ProjectModel::find(array(
                'conditions' => 'status <> :removedStatus:',
                'bind' => ['removedStatus' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_REMOVED]
            ));
        } else {
            $projects = \ITECH\Data\Model\ProjectModel::find(array(
                'conditions' => 'id IN (' . $projectIds['projectIdsString'] . ')
                    AND status <> :removedStatus:',
                'bind' => ['removedStatus' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_REMOVED]
            ));
        }

        $breadcrumbs = [
            [
                'title'  => 'Dashboard',
                'url'    => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title'  => 'Danh sách dự án',
                'url'    => $this->url->get(['for' => 'project_list']),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'projects'    => $projects
        ));
        $this->view->pick(parent::$theme . '/project/index');
    }

    public function addAction()
    {
        parent::allowRole([\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN]);

        $location = \ITECH\Data\Model\LocationModel::find(array(
            'order' => 'ordering ASC'
        ));

        $provinces = array();
        $districts = array();

        foreach ($location as $item) {
            if ($item->parent_id == 0) {
                $provinces[$item->id] = $item->name;
                $districts[$item->id] = array();
            }
        }

        foreach ($location as $item) {
            if ($item->parent_id > 0 && isset($districts[$item->parent_id])) {
                $districts[$item->parent_id][$item->id] = $item->name;
            }
        }

        $gallery = array();
        $direction = \ITECH\Data\Lib\Constant::getDirection();

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
                'title' => 'Thêm dự án',
                'url' => $this->url->get([
                    'for' => 'project_add'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'addAction'   => true,
            'provinces'   => $provinces,
            'districts'   => $districts,
            'gallery'     => $gallery,
            'trends'      => $direction,
            'breadcrumbs' => $breadcrumbs
        ));
        $this->view->pick(parent::$theme . '/project/add');
    }

    public function editAction()
    {
        $userSession = $this->session->get('USER');

        $id   = $this->request->getQuery('id', array('int'), '');
        $page = $this->request->getQuery('page', array('int'), 1);

        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :project_id:',
            'bind'       => array('project_id' => $id)
        ));

        if (!$project) {
            throw new \Exception('Không tồn tại dự án này.');
        }

        $permissionProjects = parent::getPermissionProjects();

        if (
            $userSession['membership'] != \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
            && isset($permissionProjects['projectIds'])
        ) {
            if (!in_array($id, $permissionProjects['projectIds'])) {
                throw new \Exception('Bạn không có quyền với dự án này.');
            }
        }

        $direction = \ITECH\Data\Lib\Constant::getDirection();
        $location  = \ITECH\Data\Model\LocationModel::find(array('order' => 'ordering ASC'));

        $provinces = array();
        $districts = array();

        foreach ($location as $item) {
            if ($item->parent_id == 0) {
                $provinces[$item->id] = $item->name;
                $districts[$item->id] = array();
            }
        }

        foreach ($location as $item) {
            if ($item->parent_id > 0 && isset($districts[$item->parent_id])) {
                $districts[$item->parent_id][$item->id] = $item->name;
            }
        }

        $gallery = array();
        if ($project->gallery != '') {
            $gallery = json_decode($project->gallery, true);
        }

        // --------- Project Attributes
        $projectAttributeModel = new \ITECH\Data\Model\ProjectAttributeModel();

        $b = $projectAttributeModel->getModelsManager()->createBuilder();
        $b->columns(array(
            'p1.id AS project_id',
            'a1.id AS attribute_id',
            'a1.name AS attribute_name',
            'a1.type AS attribute_type'
        ));

        $b->from(array('pa1' => 'ITECH\Data\Model\ProjectAttributeModel'));
        $b->innerJoin('ITECH\Data\Model\AttributeModel', 'a1.id = pa1.attribute_id', 'a1');
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = pa1.project_id', 'p1');

        $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));
        $b->andWhere('a1.module = :attribute_module:', array('attribute_module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT));
        $b->andWhere('p1.id = :project_id:', array('project_id' => $project->id));

        $result = $b->getQuery()->execute();

        $projectPropertyTypeVie = '';
        $projectPropertyViewVie = '';
        $projectPropertyUtilityVie = '';

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
                $projectPropertyTypeVie = implode(', ', $arrayTypeVie);
            }

            if (count($arrayViewVie)) {
                $projectPropertyViewVie = implode(', ', $arrayViewVie);
            }

            if (count($arrayUtilityVie)) {
                $projectPropertyUtilityVie = implode(', ', $arrayUtilityVie);
            }
        }
        // Project Attributes ---------

        $attribute = \ITECH\Data\Model\AttributeModel::find(array(
            'conditions' => 'status = :attribute_status: AND module = :attribute_module:',
            'bind' => array(
                'attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE,
                'attribute_module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
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

        $mapImage = parent::getMapImage(array(
            'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
            'item_id' => (int)$project->id
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
                'title' => 'Chỉnh sửa dự án',
                'url' => $this->url->get([
                    'for' => 'project_edit'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs'               => $breadcrumbs,
            'project'                   => $project,
            'id'                        => $id,
            'provinces'                 => $provinces,
            'districts'                 => $districts,
            'gallery'                   => $gallery,
            'trends'                    => $direction,
            'mapImage'                  => $mapImage,
            'projectPropertyTypeVie'    => $projectPropertyTypeVie,
            'projectPropertyViewVie'    => $projectPropertyViewVie,
            'projectPropertyUtilityVie' => $projectPropertyUtilityVie,
            'propertyTypeVie'           => $propertyTypeVie,
            'propertyViewVie'           => $propertyViewVie,
            'propertyUtilityVie'        => $propertyUtilityVie,
            'page'                      => $page
        ));
        $this->view->pick(parent::$theme . '/project/edit');
    }

    public function deleteAction()
    {
        parent::allowRole([\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN]);

        $userSession = $this->session->get('USER');
        $projectId   = $this->request->getQuery('project_id', array('int'), '');

        $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
            'conditions' => 'id = :project_id:',
            'bind' => array('project_id' => $projectId)
        ));

        if (!$project) {
            throw new \Exception('Không tồn tại dự án này.');
        }

        $project->status = \ITECH\Data\Lib\Constant::PROJECT_STATUS_REMOVED;
        $project->updated_by = $userSession['id'];
        $project->updated_at = date('Y-m-d H:i:s');

        try {
            if (!$project->save()) {
                $messages = $project->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa.';
                $this->flashSession->error($message);
            } else {
                $this->flashSession->success('Xóa thành công.');
            }

            return $this->response->redirect(array('for' => 'project_list'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function postAjaxAction()
    {
        $userSession = $this->session->get('USER');

        $tab  = $this->request->getQuery('tab', array('int'), 1);
        $page = $this->request->getQuery('page', array('int'), 1);

        $response = array(
            'status'  => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['id']) && $post['id'] != '') {
                $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'id = :project_id:',
                    'bind' => array('project_id'=> $post['id'])
                ));

                if (!$project) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Không tồn tại dự án này.'
                    );
                    goto RETURN_RESPONSE;
                }

                $project->status     = \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE;
                $project->updated_by = $userSession['id'];
                $project->updated_at = date('Y-m-d H:i:s');
                $isNew = false;
            } else {
                $project = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'name = :project_name: 
                        AND status <> :removedStatus:',
                    'bind' => array(
                        'project_name'  => $post['name'],
                        'removedStatus' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_REMOVED
                    )
                ));

                if ($project) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Tên dự án đã tồn tại.'
                    );
                    goto RETURN_RESPONSE;
                }

                $project = new \ITECH\Data\Model\ProjectModel();

                $project->status     = \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE;
                $project->created_by = $userSession['id'];
                $project->created_at = date('Y-m-d H:i:s');
                $project->updated_at = date('Y-m-d H:i:s');
                $isNew = true;
            }

            $validator = new \Phalcon\Validation();

            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên dự án.'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));

            $validator->add('name_eng', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên dự án (Tiếng Anh).'
            )));
            $validator->setFilters('name_eng', array('striptags', 'trim'));

            $validator->setFilters('slug', array('striptags', 'trim', 'lower'));
            $validator->setFilters('slug_eng', array('striptags', 'trim', 'lower'));

            $validator->add('address', new \Phalcon\Validation\Validator\Presenceof(array(
                'message' => 'Yêu cầu nhập địa chỉ.'
            )));
            $validator->setFilters('address', array('striptags', 'trim'));

            $validator->add('address_eng', new \Phalcon\Validation\Validator\Presenceof(array(
                'message' => 'Yêu cầu nhập địa chỉ (Tiếng Anh).'
            )));
            $validator->setFilters('address_eng', array('striptags', 'trim'));

            $validator->add('province_id', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn tỉnh thành.'
            )));
            $validator->setFilters('province_id', array('int'));

            $validator->setFilters('district_id', array('int'));
            $validator->setFilters('description', array('trim'));
            $validator->setFilters('description_eng', array('trim'));
            $validator->setFilters('default_image', array('striptags', 'trim'));
            $validator->setFilters('direction', array('int'));

            $validator->setFilters('property_type', array('striptags', 'trim'));
            $validator->setFilters('property_view', array('striptags', 'trim'));
            $validator->setFilters('property_utility', array('striptags', 'trim'));

            $validator->setFilters('total_area', array('striptags', 'trim'));
            $validator->setFilters('green_area', array('striptags', 'trim'));

            $validator->setFilters('block_count', array('int'));
            $validator->setFilters('apartment_count', array('int'));
            $validator->setFilters('meta_title', array('striptags', 'trim'));
            $validator->setFilters('meta_title_eng', array('striptags', 'trim'));
            $validator->setFilters('meta_description', array('striptags', 'trim'));
            $validator->setFilters('meta_description_eng', array('striptags', 'trim'));
            $validator->setFilters('meta_keywords', array('striptags', 'trim'));
            $validator->setFilters('meta_keywords_eng', array('striptags', 'trim'));

            $messages = $validator->validate($post);
            if (count($messages)) {
                $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin không hợp lệ.',
                    'result' => $result
                );
                goto RETURN_RESPONSE;
            }

            $project->name = $validator->getValue('name');
            $project->name_eng = $validator->getValue('name_eng');

            if ($project->name_eng == '') {
                $project->name_eng = $project->name;
            }

            $project->slug = \ITECH\Data\Lib\Util::slug($validator->getValue('slug'));
            $project->slug_eng = \ITECH\Data\Lib\Util::slug($validator->getValue('slug_eng'));

            if ($project->slug == 'n-a') {
                $project->slug = \ITECH\Data\Lib\Util::slug($project->name);
            }

            if ($project->slug_eng == 'n-a') {
                $project->slug_eng = \ITECH\Data\Lib\Util::slug($project->name_eng);
            }

            $project->address = $validator->getValue('address');
            $project->address_eng = $validator->getValue('address_eng');

            if ($project->address_eng == '') {
                $project->address_eng = $project->address;
            }

            if ($validator->getValue('province_id') > 0) {
                $project->province_id = $validator->getValue('province_id');
            } else {
                $project->province_id = null;
            }

            if ($project->province_id > 0) {
                if ($validator->getValue('district_id') > 0) {
                    $project->district_id = $validator->getValue('district_id');
                } else {
                    $project->district_id = null;
                }
            } else {
                $project->district_id = null;
            }

            if ($project->meta_title == '') {
                $project->meta_title = $project->name;
            }

            if ($project->meta_title_eng == '') {
                $project->meta_title_eng = $project->name_eng;
            }

            $project->description = $validator->getValue('description');
            $project->description_eng = $validator->getValue('description_eng');
            $project->default_image = $validator->getValue('default_image');

            if (isset($post['gallery']) && count($post['gallery'])) {
                $post['gallery'] = array_unique($post['gallery']);
                $project->gallery = json_encode($post['gallery']);
            } else {
                $project->gallery = json_encode(array());
            }

            $project->direction = $validator->getValue('direction');

            $attributes = array();

            if ($validator->getValue('property_type') != '') {
                $array = array_filter(array_unique(explode(',', $validator->getValue('property_type'))));
                foreach ($array as $item) {
                    $item = trim($item);

                    if ($item != '') {
                        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                            'conditions' => 'name = :attribute_name:
                                AND type = :attribute_type:
                                AND module = :attribute_module:',
                            'bind' => array(
                                'attribute_name' => $item,
                                'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_TYPE,
                                'attribute_module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                            )
                        ));

                        if ($attribute) {
                            $attributes[] = $attribute->id;
                        }
                    }
                }
            }

            if ($validator->getValue('property_view') != '') {
                $array = array_filter(array_unique(explode(',', $validator->getValue('property_view'))));
                foreach ($array as $item) {
                    $item = trim($item);

                    if ($item != '') {
                        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                            'conditions' => 'name = :attribute_name:
                                AND type = :attribute_type:
                                AND module = :attribute_module:',
                            'bind' => array(
                                'attribute_name' => $item,
                                'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_VIEW,
                                'attribute_module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                            )
                        ));

                        if ($attribute) {
                            $attributes[] = $attribute->id;
                        }
                    }
                }
            }

            if ($validator->getValue('property_utility') != '') {
                $array = array_filter(array_unique(explode(',', $validator->getValue('property_utility'))));
                foreach ($array as $item) {
                    $item = trim($item);

                    if ($item != '') {
                        $attribute = \ITECH\Data\Model\AttributeModel::findFirst(array(
                            'conditions' =>
                                'name = :attribute_name:
                                AND type = :attribute_type:
                                AND module = :attribute_module:',
                            'bind' => array(
                                'attribute_name' => $item,
                                'attribute_type' => \ITECH\Data\Lib\Constant::ATTRIBUTE_TYPE_UTILITY,
                                'attribute_module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
                            )
                        ));

                        if ($attribute) {
                            $attributes[] = $attribute->id;
                        }
                    }
                }
            }

            if ($project->id) {
                $projectAttributeModel = new \ITECH\Data\Model\ProjectAttributeModel();

                $q = 'DELETE FROM `land_project_attribute` WHERE `project_id` = "' . $project->id . '"';
                $projectAttributeModel->getWriteConnection()->query($q);
            }

            $project->total_area = $validator->getValue('total_area');
            $project->green_area = $validator->getValue('green_area');

            $project->block_count = $validator->getValue('block_count');
            $project->apartment_count = $validator->getValue('apartment_count');

            $project->meta_title     = $validator->getValue('meta_title');
            $project->meta_title_eng = $validator->getValue('meta_title_eng');

            $project->meta_description     = $validator->getValue('meta_description');
            $project->meta_description_eng = $validator->getValue('meta_description_eng');

            $project->meta_keywords     = $validator->getValue('meta_keywords');
            $project->meta_keywords_eng = $validator->getValue('meta_keywords_eng');

            if ($project->meta_title == '') {
                $project->meta_title = $project->name;
            }

            if ($project->meta_title_eng == '') {
                $project->meta_title_eng = $project->name_eng;
            }

            $project->meta_description     = $project->meta_description != '' ? $project->meta_description : $project->name;
            $project->meta_description_eng = $project->meta_description_eng != '' ? $project->meta_description_eng : $project->name_eng;

            $project->meta_keywords     = $project->meta_keywords != '' ? $project->meta_keywords : $project->name;
            $project->meta_keywords_eng = $project->meta_keywords_eng != '' ? $project->meta_keywords_eng : $project->name_eng;

            try {
                if (!$project->save()) {
                    $messages = $project->getMessages();
                    $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';

                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $message,
                    );
                    goto RETURN_RESPONSE;
                }

                // IMAGE
                $messageImage = array();
                if ($project->id && isset($post['galleries'])) {
                    $listImage = $post['galleries'];
                    $messageImage = parent::saveMapImage(array(
                        'images' => $listImage,
                        'module' => \ITECH\Data\Lib\Constant::MAP_IMAGE_MODULE_PROJECT,
                        'item_id' => (int)$project->id
                    ));
                }
                // IMAGE

                if ($isNew) {
                    $this->flashSession->success('Thêm thành công.');
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Success.',
                        'result' => array(
                            'redirect_url' => $this->url->get(array('for' => 'project_edit', 'query' => '?' . http_build_query(array('id' => $project->id, 'page' => $page))))
                        ),
                        'image' => $messageImage
                    );
                } else {
                    $this->flashSession->success('Cập nhật thành công.');

                    if (isset($attributes) && count($attributes)) {
                        foreach ($attributes as $item) {
                            $projectAttribute = new \ITECH\Data\Model\ProjectAttributeModel();
                            $projectAttribute->project_id = $project->id;
                            $projectAttribute->attribute_id = $item;
                            $projectAttribute->save();
                        }
                    }

                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Success.',
                        'result' => array(
                            'redirect_url' => $this->url->get(array('for' => 'project_edit', 'query' => '?' . http_build_query(array('id' => $project->id, 'page' => $page))))
                        ),
                        'image' => $messageImage
                    );
                }

                goto RETURN_RESPONSE;
            } catch (\Exception $e) {
                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => $e->getMessage()
                );
                goto RETURN_RESPONSE;
            }
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function listAttributeAction()
    {
        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách thuộc tính',
                'url' => $this->url->get([
                    'for' => 'project_list_attribute'
                ]),
                'active' => true
            ]
        ];
        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs
        ));
        $this->view->pick(parent::$theme . '/project/list_attribute');
    }
}
