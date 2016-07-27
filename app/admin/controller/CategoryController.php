<?php
namespace ITECH\Admin\Controller;

class CategoryController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));
    }

    public function indexAction()
    {
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'parent_id' => (int)0,
                'module' => \ITECH\Data\Lib\Constant::CATEGORY_MODULE_ARTICLE
            ),
            'page' => $page,
            'limit' => $limit
        );
        $categoryRepo = new \ITECH\Data\Repo\CategoryRepo();
        $categories = $categoryRepo->getPaginationList($params);

        $subCategory = array();
        if (count($categories)) {
            foreach ($categories->items as $item) {
                $params = array(
                    'parent_id' => $item->id,
                    'q' => $q,
                    'page' => $page,
                    'level' => '',
                    'html' => '',
                    'link' => 'category_edit',
                    'link_delete' => 'category_delete'
                );

                $subCategory[$item->id] = $this->subCategory($params);
            }
        }

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'category'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($categories->total_pages) ? $categories->total_pages : 0,
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
                'title' => 'Danh mục',
                'url' => $this->url->get([
                    'for' => 'category',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'categories' => $categories->items,
            'subCategory' => $subCategory
        ));

        $this->view->pick(parent::$theme . '/category/index');
    }

    public function fengShuiAction()
    {
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'parent_id' => (int)0,
                'module' => \ITECH\Data\Lib\Constant::CATEGORY_MODULE_FENGSHUI
            ),
            'page' => $page,
            'limit' => $limit
        );
        $categoryRepo = new \ITECH\Data\Repo\CategoryRepo();
        $categories = $categoryRepo->getPaginationList($params);

        $subCategory = array();
        if (count($categories)) {
            foreach ($categories->items as $item) {
                $params = array(
                    'parent_id' => $item->id,
                    'q' => $q,
                    'page' => $page,
                    'level' => '',
                    'html' => '',
                    'link' => 'category_edit_fengshui',
                    'link_delete' => 'category_delete'
                );

                $subCategory[$item->id] = $this->subCategory($params);
            }
        }

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'category'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($categories->total_pages) ? $categories->total_pages : 0,
            'page' => $page,
            'pages_display' => 3
        );

        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'categories' => $categories->items,
            'subCategory' => $subCategory
        ));

        $this->view->pick(parent::$theme . '/category/fengshui');
    }

    public function addAction()
    {
        $userSession = $this->session->get('USER');

        $category = new \ITECH\Data\Model\CategoryModel();
        $form = new \ITECH\Admin\Form\CategoryForm($category, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $category);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $category->name = $this->request->getPost('name');
                $category->name_eng = $this->request->getPost('name_eng');
                $category->icon = $this->request->getPost('icon');
                $category->image = $this->request->getPost('image');
                $category->slug = \ITECH\Data\Lib\Util::slug($category->name);
                $category->slug_eng = \ITECH\Data\Lib\Util::slug($category->name_eng);
                $category->meta_title = $this->request->getPost('meta_title');
                $category->meta_title_eng = $this->request->getPost('meta_title_eng');
                $category->meta_description = $this->request->getPost('meta_description');
                $category->meta_description_eng = $this->request->getPost('meta_description_eng');
                $category->meta_keyword = $this->request->getPost('meta_keyword');
                $category->meta_keyword_eng = $this->request->getPost('meta_keyword_eng');
                $category->status = $this->request->getPost('status');
                $category->parent_id = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('parent_id'));
                $category->created_by = $userSession['id'];
                $category->updated_by = $userSession['id'];
                $category->created_at = date('Y-m-d H:i:s');
                $category->updated_at = date('Y-m-d H:i:s');

                if (!$category->create()) {
                    $messages = $category->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'category_edit', 'query' => '?' . http_build_query(array('id' => $category->id))));
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
                'title' => 'Danh mục',
                'url' => $this->url->get([
                    'for' => 'category',
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm danh mục',
                'url' => $this->url->get([
                    'for' => 'category_add'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/category/add');
    }

    public function editAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $category = \ITECH\Data\Model\CategoryModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$category) {
            throw new \Phalcon\Exception('Không tồn tại danh mục này.');
        }

        if ($category->icon != '') {
            $category->image_icon_url = $this->config->asset->frontend_url . 'upload/category/' . $category->icon;
        }

        if ($category->image != '') {
            $category->image_default_url = $this->config->asset->frontend_url . 'upload/category/' . $category->image;
        }

        $form = new \ITECH\Admin\Form\CategoryForm($category, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $category);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $category->name = $this->request->getPost('name');
                $category->name_eng = $this->request->getPost('name_eng');
                $category->icon = $this->request->getPost('icon');
                $category->image = $this->request->getPost('image');
                $category->slug = \ITECH\Data\Lib\Util::slug($category->name);
                $category->slug_eng = \ITECH\Data\Lib\Util::slug($category->name_eng);
                $category->meta_title = $this->request->getPost('meta_title');
                $category->meta_title_eng = $this->request->getPost('meta_title_eng');
                $category->meta_description = $this->request->getPost('meta_description');
                $category->meta_desctiption_eng = $this->request->getPost('meta_description_eng');
                $category->meta_keyword = $this->request->getPost('meta_keyword');
                $category->meta_keyword_eng = $this->request->getPost('meta_keyword_eng');
                $category->status = $this->request->getPost('status');
                $category->parent_id = \ITECH\Data\Lib\Util::numberOnly($this->request->getPost('parent_id'));
                $category->updated_by = $userSession['id'];
                $category->updated_at = date('Y-m-d H:i:s');

                if (!$category->update()) {
                    $messages = $category->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'category_edit', 'query' => '?' . http_build_query(array('id' => $category->id))));
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
                'title' => 'Danh mục',
                'url' => $this->url->get([
                    'for' => 'category',
                ]),
                'active' => false
            ],
            [
                'title' => $category->name,
                'url' => $this->url->get([
                    'for' => 'category_edit',
                    'id' => $id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'category' => $category
        ));

        $this->view->pick(parent::$theme . '/category/edit');
    }

    public function deleteAction()
    {
        //$userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $category = \ITECH\Data\Model\CategoryModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$category) {
            throw new \Phalcon\Exception('Không tồn tại danh mục này.');
        }

        $category->status = \ITECH\Data\Lib\Constant::CATEGORY_STATUS_REMOVED;
        $this->db->begin();
        try {
            if (!$category->update()) {
                $messages = $category->getMessages();
                if (isset($messages[0])) {
                    $this->flashSession->error($messages[0]->getMessage());
                }
                $this->db->rollback;
            } else {
                $this->db->commit();
                $this->flashSession->success('Xóa thành công.');
            }
        } catch (\Phalcon\Exception $e) {
            $this->db->rollback;
            $this->logger->log('[CategoryController][deleteAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
        }

        return $this->response->redirect(array('for' => 'category'));
    }

    public function subCategory($params)
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $aParams = array();
        $aParams['cache'] = false;
        $aParams['parent_id'] = $params['parent_id'];
        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);
        $categories = array();
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $categories = $r['result'];
        }

        $link = $params['link'];
        $linkDelete = $params['link_delete'];
        $html = $params['html'];
        $level = $params['level'];
        $level .= '--';
        $selectStatus = \ITECH\Data\Lib\Constant::getCategoryStatus();
        if(count($categories) > 0) {
            foreach ($categories as $item) {
                $query = array(
                    'id' => $item['id'],
                    'page' => $params['page'],
                    'q' => $params['q']
                );
                $html .= '<tr>
                <td>' . $item['id'] . '</td>
                <td>
                    <a href="' . $this->url->get(array('for' => $link, 'query' => '?' . http_build_query($query))) . '">
                        ' . $level . ' ' . $item['name'] . '
                    </a>
                </td>
                <td>' . $item['slug'] . '</td>
                <td>' . $item['article_count'] . '</td>';

                if ($item['status'] == \ITECH\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE) {
                    $html .= '<td><a href="' . $this->url->get(array('for' => $linkDelete, 'query' => '?' . http_build_query($query))) . '" onclick="javascript:return confirm("Đồng ý xoá?");" class="btn btn-xs btn-bricky">
                        <i class="fa fa-times fa fa-white"></i>
                    </a></td>';
                } else {

                    $html .= '<td>' . $selectStatus[$item['status']] . '</td>';
                }

                $html .= '</tr>';

                $subParams = array(
                    'parent_id' => $item['id'],
                    'q' => $params['q'],
                    'page' => $params['page'],
                    'level' => $level,
                    'html' => '',
                    'link' => $link,
                    'link_delete' => $linkDelete
                );

                $html .= $this->subCategory($subParams);
            }
        } else {
            $html .= '';
        }

        return  $html;
    }

    public function subSelect($params)
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $aParams = array();
        $aParams['cache'] = false;
        if (isset($params['not_id'])) {
            $aParams['not_id'] = $params['not_id'];
        }

        $aParams['parent_id'] = $params['parent_id'];
        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);
        $categories = array();
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $categories = $r['result'];
        }

        $subCategories = $params['sub_categories'];
        $level = $params['level'];

        if (count($categories) > 0) {
            $level .= '--';
            foreach ($categories as $item):
                $subCategories[$item['id']] = $level . ' ' . $item['name'];
                $subParams = array(
                    'parent_id' => $item['id'],
                    'sub_categories' => $subCategories,
                    'level' => $level
                );
                if (isset($params['not_id'])) {
                    $subParams['not_id'] = $params['not_id'];
                }

                $subCategories = $subCategories + $this->subSelect($subParams);
            endforeach;
        } else {
            $subCategories = array();
        }

        return $subCategories;
    }

    public function subCheckbox($params)
    {
        //$userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $aParams = array();
        $aParams['cache'] = false;
        if (isset($params['not_id'])) {
            $aParams['not_id'] = $params['not_id'];
        }

        $aParams['parent_id'] = $params['parent_id'];
        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);
        $categories = array();
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $categories = $r['result'];
        }

        $html = $params['html'];
        $inArray = $params['in_array'];
        $level = $params['level'];
        if (count($categories) > 0) {
            $html = '';
            foreach ($categories as $item):
                $active = '';
                if (in_array($item['id'], $inArray)) {
                    $active = 'checked="checked"';
                }
                $html .= '<div style="position:relative;min-height:27px;padding-left:20px; padding-top:7px; left:'. $level .'%" class="checkbox"><input type="checkbox" name="category[]" value="' . $item['id'] . '" class="red" ' . $active . '>' . $item['name'] . '</div>';

                $subParams = array(
                    'parent_id' => $item['id'],
                    'level' => $level + 10,
                    'html' => '',
                    'in_array' => $inArray
                );

                $html .= $this->subCheckbox($subParams);
            endforeach;

        }
        else {
            $html = '';
        }

        return $html;
    }

    public function groupListAction()
    {
        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'parent_id' => (int)0
            ),
            'page' => $page,
            'limit' => $limit
        );
        $groupRepo = new \ITECH\Data\Repo\GroupRepo();
        $result = $groupRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'category_list_group'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($result->total_pages) ? $result->total_pages : 0,
            'page' => $page,
            'pages_display' => 3
        );

        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'result' => $result->items
        ));

        $this->view->pick(parent::$theme . '/category/group_list');
    }

    public function groupAddAction()
    {
        $userSession = $this->session->get('USER');

        $group = new \ITECH\Data\Model\GroupModel();
        $form = new \ITECH\Admin\Form\GroupForm($group);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $group);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $group->name = $this->request->getPost('name');
                $group->created_by = $userSession['id'];
                $group->updated_by = $userSession['id'];
                $group->created_at = date('Y-m-d H:i:s');
                $group->updated_at = date('Y-m-d H:i:s');

                if (!$group->create()) {
                    $messages = $group->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'category_edit_group', 'query' => '?' . http_build_query(array('id' => $group->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/category/group_add');
    }

    public function groupEditAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $group = \ITECH\Data\Model\GroupModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$group) {
            throw new \Phalcon\Exception('Không tồn tại nhóm này.');
        }

        $form = new \ITECH\Admin\Form\GroupForm($group);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $group);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $group->name = $this->request->getPost('name');
                $group->updated_by = $userSession['id'];
                $group->updated_at = date('Y-m-d H:i:s');

                if (!$group->update()) {
                    $messages = $group->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'category_edit_group', 'query' => '?' . http_build_query(array('id' => $group->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'group' => $group
        ));

        $this->view->pick(parent::$theme . '/category/group_edit');
    }

    public function linkListAction()
    {
        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'type' => \ITECH\Data\Lib\Constant::GROUP_TYPE_LINK
            ),
            'page' => $page,
            'limit' => $limit
        );
        $groupRepo = new \ITECH\Data\Repo\GroupRepo();
        $result = $groupRepo->getPaginationList($params);

        $linkLayout = array();
        $groupLinkLayout = array();
        $linkComponent = new \ITECH\Admin\Component\LinkComponent();

        if ($result->total_items > 0) {
            foreach ($result->items as $group) {
                $link = \ITECH\Data\Model\LinkModel::find(array(
                    'conditions' => 'group_id = :group_id: and parent_id = :parent_id:',
                    'bind' => array(
                        'group_id' => $group->id,
                        'parent_id' => (int)0
                    ),
                    'order' => 'ordering ASC'
                ));

                if (count($link)) {
                    $linkLayout = '<ol class="dd-list">';
                    foreach ($link as $item) {
                        $query = array(
                            'id' => $item->id,
                        );

                        $linkLayout .= '<li class="dd-item dd3-item" data-id="' . $item->id . '">
                                        <div class="dd-handle dd3-handle"></div>
                                        <div class="dd3-content">
                                            ' . $item->name . '
                                            <div class="visible-md visible-lg hidden-sm hidden-xs float-right">
                                                <a class="btn btn-squared btn-xs btn-primary tooltips" data-original-title="Sửa" data-placement="top" href="'
                                                . $this->url->get(array('for' => 'category_edit_link', 'query' =>'?' . http_build_query($query))) . '">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a class="btn btn-squared btn-xs btn-primary tooltips" data-original-title="Xóa" data-placement="top" href="'
                                                . $this->url->get(array('for' => 'category_delete_link', 'query' =>'?' . http_build_query($query))) .'" ' . 'onclick="javascript:return confirm(\'Đồng ý xoá?\');"' . '>
                                                    <i class="fa fa-times fa fa-white"></i>
                                                </a>
                                            </div>
                                        </div>';
                        $params = array(
                            'conditions' => array(
                                'group_id' => $group->id,
                                'parent_id' => $item->id
                            )
                        );

                        $linkLayout .= $linkComponent->sub($params);
                        $linkLayout .= '</li>';
                    }

                    $linkLayout .= '</ol>';
                } else {
                    $linkLayout = '';
                }

                $groupLinkLayout[$group->id] = $linkLayout;
            }
        }

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'category_list_link'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($result->total_pages) ? $result->total_pages : 0,
            'page' => $page,
            'pages_display' => 3
        );

        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'result' => $result->items,
            'groupLinkLayout' => $groupLinkLayout
        ));

        $this->view->pick(parent::$theme . '/category/link_list');
    }

    public function linkAddAction()
    {
        $userSession = $this->session->get('USER');

        $link = new \ITECH\Data\Model\LinkModel();
        $form = new \ITECH\Admin\Form\LinkForm($link);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $link);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $link->name = $this->request->getPost('name');
                $link->slug = \ITECH\Data\Lib\Util::slug($link->name);
                $link->created_by = $userSession['id'];
                $link->updated_by = $userSession['id'];
                $link->created_at = date('Y-m-d H:i:s');
                $link->updated_at = date('Y-m-d H:i:s');

                if (!$link->create()) {
                    $messages = $link->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'category_edit_link', 'query' => '?' . http_build_query(array('id' => $link->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/category/link_add');
    }

    public function linkEditAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $link = \ITECH\Data\Model\LinkModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$link) {
            throw new \Phalcon\Exception('Không tồn tại liên kết này.');
        }

        $form = new \ITECH\Admin\Form\LinkForm($link);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $link);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $link->name = $this->request->getPost('name');
                $link->slug = \ITECH\Data\Lib\Util::slug($link->name);
                $link->updated_by = $userSession['id'];
                $link->updated_at = date('Y-m-d H:i:s');

                if (!$link->update()) {
                    $messages = $link->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'category_edit_link', 'query' => '?' . http_build_query(array('id' => $link->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'link' => $link
        ));

        $this->view->pick(parent::$theme . '/category/link_edit');
    }

    public function linkDeleteAction()
    {
        //$userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $link = \ITECH\Data\Model\LinkModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$link) {
            throw new \Phalcon\Exception('Không tồn tại liên kết này.');
        }

        $this->db->begin();
        try {
            if (!$link->delete()) {
                $messages = $link->getMessages();
                if (isset($messages[0])) {
                    $this->flashSession->error($messages[0]->getMessage());
                }
                $this->db->rollback;
            } else {
                $this->db->commit();
                $this->flashSession->success('Xóa thành công.');
            }
        } catch (\Phalcon\Exception $e) {
            $this->db->rollback;
            $this->logger->log('[CategoryController][linkDeleteAction]: ' . $e->getMessage(), \Phalcon\Logger::ERROR);
        }

        return $this->response->redirect(array('for' => 'category_list_link'));
    }

    public function fengshuiAddAction()
    {
        $userSession = $this->session->get('USER');

        $category = new \ITECH\Data\Model\CategoryModel();
        $form = new \ITECH\Admin\Form\CategoryFengshuiForm($category, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $category);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $category->name = $this->request->getPost('name');
                $category->slug = \ITECH\Data\Lib\Util::slug($category->name);
                $category->meta_title = $this->request->getPost('meta_title');
                $category->meta_description = $this->request->getPost('meta_description');
                $category->meta_keyword = $this->request->getPost('meta_keyword');
                $category->status = $this->request->getPost('status');
                $category->parent_id = 0;
                $category->created_by = $userSession['id'];
                $category->updated_by = $userSession['id'];
                $category->created_at = date('Y-m-d H:i:s');
                $category->module = \ITECH\Data\Lib\Constant::CATEGORY_MODULE_FENGSHUI;

                if (!$category->create()) {
                    $messages = $category->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'category_edit_fengshui', 'query' => '?' . http_build_query(array('id' => $category->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/category/fengshui_add');
    }

    public function fengshuiEditAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $category = \ITECH\Data\Model\CategoryModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$category) {
            throw new \Phalcon\Exception('Không tồn tại danh mục này.');
        }

        if ($category->icon != '') {
            $category->image_icon_url = $this->config->asset->frontend_url . 'upload/category/' . $category->icon;
        }

        if ($category->image != '') {
            $category->image_default_url = $this->config->asset->frontend_url . 'upload/category/' . $category->image;
        }

        $form = new \ITECH\Admin\Form\CategoryFengshuiForm($category, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $category);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $category->name = $this->request->getPost('name');
                $category->slug = \ITECH\Data\Lib\Util::slug($category->name);
                $category->meta_title = $this->request->getPost('meta_title');
                $category->meta_description = $this->request->getPost('meta_description');
                $category->meta_keyword = $this->request->getPost('meta_keyword');
                $category->status = $this->request->getPost('status');
                $category->parent_id = 0;
                $category->updated_by = $userSession['id'];
                $category->updated_at = date('Y-m-d H:i:s');
                $category->updated_at = date('Y-m-d H:i:s');

                if (!$category->update()) {
                    $messages = $category->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'category_edit_fengshui', 'query' => '?' . http_build_query(array('id' => $category->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'category' => $category
        ));

        $this->view->pick(parent::$theme . '/category/fengshui_edit');
    }
}