<?php
namespace ITECH\Admin\Controller;

class ArticleController extends \ITECH\Admin\Controller\BaseController
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
        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'module' => \ITECH\Data\Lib\Constant::ARTICLE_MODULE_POST,
                'status' => \ITECH\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
            ),
            'page' => $page,
            'limit' => $limit
        );

        $articleRepo = new \ITECH\Data\Repo\ArticleRepo();
        $articles = $articleRepo->getPaginationList($params);
        $categoryLayout = array();

        if ($articles->total_items > 0) {
            foreach ($articles->items as $item) {
                $params = array(
                    'conditions' => array(
                        'article_id' => $item->id
                    )
                );
                $articleCategoryRepo = new \ITECH\Data\Repo\ArticleCategoryRepo();
                $articleCategory = $articleCategoryRepo->getList($params);
                if (count($articleCategory)) {
                    foreach ($articleCategory as $category) {
                        $categoryLayout[$item->id][] = $category->category_name;
                    }
                }
            }
        }

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'article'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($articles->total_pages) ? $articles->total_pages : 0,
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
                'title' => 'Danh sách bài viết',
                'url' => $this->url->get([
                    'for' => 'article'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'articles' => $articles->items,
            'categoryLayout' => $categoryLayout
        ));

        $this->view->pick(parent::$theme . '/article/index');
    }

    public function pageAction()
    {
        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'module' => \ITECH\Data\Lib\Constant::ARTICLE_MODULE_PAGE,
                'status' => \ITECH\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
            ),
            'page' => $page,
            'limit' => $limit
        );
        $articleRepo = new \ITECH\Data\Repo\ArticleRepo();
        $articles = $articleRepo->getPaginationList($params);

        $categoryLayout = array();
        if ($articles->total_items > 0) {
            foreach ($articles->items as $item) {
                $params = array(
                    'conditions' => array(
                        'article_id' => $item->id
                    )
                );
                $articleCategoryRepo = new \ITECH\Data\Repo\ArticleCategoryRepo();
                $articleCategory = $articleCategoryRepo->getList($params);
                if (count($articleCategory)) {
                    foreach ($articleCategory as $category) {
                        $categoryLayout[$item->id][] = $category->category_name;
                    }
                }
            }
        }

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'article_list_page'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($articles->total_items) ? $articles->total_items : 0,
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
                'title' => 'Danh sách trang tĩnh',
                'url' => $this->url->get([
                    'for' => 'article_list_page'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'articles' => $articles->items,
            'categoryLayout' => $categoryLayout
        ));

        $this->view->pick(parent::$theme . '/article/page');
    }

    public function addAction()
    {
        $userSession = $this->session->get('USER');

        $article = new \ITECH\Data\Model\ArticleModel();
        $form = new \ITECH\Admin\Form\ArticleForm($article);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->name = $this->request->getPost('name');
                $article->name_eng = $this->request->getPost('name_eng');
                $article->image_default = $this->request->getPost('thumbnail');
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->slug_eng);
                $article->intro = $this->request->getPost('intro');
                $article->intro_eng = $this->request->getPost('intro_eng');
                $article->description = $this->request->getPost('description');
                $article->description_eng = $this->request->getPost('description_eng');
                $article->status = $this->request->getPost('status');
                $article->type = $this->request->getPost('type');
                $article->created_ip = $this->request->getClientAddress();
                $article->user_agent = $this->request->getUserAgent();
                $article->created_by = $userSession['id'];
                $article->updated_by = $userSession['id'];
                $article->created_at = date('Y-m-d H:i:s');
                $article->updated_at = date('Y-m-d H:i:s');

                if (!$article->create()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $categories = $this->request->getPost('category');
                    if (count($categories) > 0) {
                        foreach ($categories as $item) {
                            $articleCategory = new \ITECH\Data\Model\ArticleCategoryModel();
                            $articleCategory->article_id = $article->id;
                            $articleCategory->category_id = $item;
                            $articleCategory->create();
                        }
                    }

                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'article_edit', 'query' => '?' . http_build_query(array('id' => $article->id))));
                }
            }
        }

        $categoryController = new \ITECH\Admin\Controller\CategoryController();
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $aParams = array();
        $aParams['cache'] = false;
        $aParams['parent_id'] = (int) 0;
        $aParams['module'] = \ITECH\Data\Lib\Constant::CATEGORY_MODULE_ARTICLE;

        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);

        $categoryLayout = '';
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $inArray = isset($article->category) ? $article->category : array();
            foreach($r['result'] as $item) {
                $active = '';
                if (in_array($item['id'], $inArray)) {
                    $active = 'checked="checked"';
                }

                $level = 0;
                $categoryLayout .= '<div style="position:relative;min-height:27px;padding-left:20px; padding-top:7px; left:'. $level .'%" class="checkbox"><input type="checkbox" name="category[]" value="' . $item['id'] . '" class="red" ' . $active . '>' . $item['name'] . '</div>';
                $params = array(
                    'parent_id' => $item['id'],
                    'level' => (int)10,
                    'html' => $categoryLayout,
                    'in_array' => $inArray
                );
                $categoryLayout .= $categoryController->subCheckbox($params);
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách bài viết',
                'url' => $this->url->get([
                    'for' => 'article'
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm bài viết',
                'url' => $this->url->get([
                    'for' => 'article_add'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'categoryLayout' => $categoryLayout
        ));

        $this->view->pick(parent::$theme . '/article/add');
    }

    public function editAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$article) {
            throw new \Phalcon\Exception('Không tồn tại bài viết này.');
        }

        if ($article->image_default != '') {
            $article->image_default_url = $this->config->asset->frontend_url . 'upload/article/' . $article->image_default;
        }

        $article->gallery = json_decode($article->gallery);

        $articleCategories = array();
        if (count($article->getArticleCategory())) {
            foreach ($article->getArticleCategory() as $item) {
                $articleCategories[] = $item->category_id;
            }
        }

        $form = new \ITECH\Admin\Form\ArticleForm($article);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->name = $this->request->getPost('name');
                $article->name_eng = $this->request->getPost('name_eng');
                $article->image_default = $this->request->getPost('thumbnail');
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->intro = $this->request->getPost('intro');
                $article->intro_eng = $this->request->getPost('intro_eng');
                $article->description = $this->request->getPost('description');
                $article->description_eng = $this->request->getPost('description_eng');
                $article->status = $this->request->getPost('status');
                $article->type = $this->request->getPost('type');
                $article->updated_by = $userSession['id'];
                $article->updated_at = date('Y-m-d H:i:s');

                $image = $this->request->getPost('image');
                $imageDescription = $this->request->getPost('image_description');
                $gallery = array();

                if (count($image)) {
                    foreach ($image as $k => $v) {
                        $gallery[] = array(
                            'image' => $v,
                            'description' => isset($imageDescription[$k]) ? $imageDescription[$k] : null
                        );
                    }
                }
                $article->gallery = json_encode($gallery);

                $categories = $this->request->getPost('category');

                if (count($categories) > 0) {
                    foreach ($categories as $item) {
                        $hasCategory = \ITECH\Data\Model\ArticleCategoryModel::count(array(
                            'conditions' => 'category_id = :category_id: AND article_id = :article_id:',
                            'bind' => array(
                                'category_id' => $item,
                                'article_id' => $article->id
                            )
                        ));
                        if ($hasCategory > 0) {
                            foreach ($articleCategories as $k => $v) {
                                if ($v == $item) {
                                    unset($articleCategories[$k]);
                                    break;
                                }
                            }
                        } else {
                            $articleCategory = new \ITECH\Data\Model\ArticleCategoryModel();
                            $articleCategory->article_id = $article->id;
                            $articleCategory->category_id = $item;
                            $articleCategory->create();
                        }
                    }
                }

                if (count($articleCategories) > 0) {
                    $articleCategory = new \ITECH\Data\Model\ArticleCategoryModel();
                    $sql = 'DELETE FROM `land_article_category`
                        WHERE `category_id` IN (' . implode(',', $articleCategories) . ')';
                    $articleCategory->getWriteConnection()->query($sql);
                }

                $article->category = null;
                if (!$article->update()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'article_edit', 'query' => '?' . http_build_query(array('id' => $article->id))));
                }
            }
        }

        $categoryController = new \ITECH\Admin\Controller\CategoryController();
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $aParams = array();
        $aParams['cache'] = false;
        $aParams['parent_id'] = (int) 0;
        $aParams['module'] = \ITECH\Data\Lib\Constant::CATEGORY_MODULE_ARTICLE;

        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);

        $categoryLayout = '';
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $inArray = isset($articleCategories) ? $articleCategories : array();
            foreach($r['result'] as $item) {
                $active = '';
                if (in_array($item['id'], $inArray)) {
                    $active = 'checked="checked"';
                }
                $level = 0;
                $categoryLayout .= '<div style="position:relative;min-height:27px;padding-left:20px; padding-top:7px; left:'. $level .'%" class="checkbox"><input type="checkbox" name="category[]" value="' . $item['id'] . '" class="red" ' . $active . '>' . $item['name'] . '</div>';
                $params = array(
                    'parent_id' => $item['id'],
                    'level' => (int)10,
                    'html' => $categoryLayout,
                    'in_array' => $inArray
                );
                $categoryLayout .= $categoryController->subCheckbox($params);
            }
        }

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách bài viết',
                'url' => $this->url->get([
                    'for' => 'article'
                ]),
                'active' => false
            ],
            [
                'title' => $article->name,
                'url' => $this->url->get([
                    'for' => 'article_edit',
                    'id' => $id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'article' => $article,
            'categoryLayout' => $categoryLayout
        ));

        $this->view->pick(parent::$theme . '/article/edit');
    }

    public function deleteAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$article) {
            throw new \Exception('Không tồn tại bài viết này.');
        }

        $article->status = \ITECH\Data\Lib\Constant::ARTICLE_STATUS_REMOVED;
        $article->updated_by = $userSession['id'];
        $article->updated_at = date('Y-m-d H:i:s');

        try {
            if (!$article->save()) {
                $messages = $article->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa.';
                $this->flashSession->error($message);
            } else {
                $this->flashSession->success('Xóa thành công.');
            }

            return $this->response->redirect(array('for' => 'article'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function pageAddAction()
    {
        $userSession = $this->session->get('USER');

        $article = new \ITECH\Data\Model\ArticleModel();
        $form = new \ITECH\Admin\Form\PageForm($article);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->name = $this->request->getPost('name');
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->name_eng = $this->request->getPost('name_eng');
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->description = $this->request->getPost('description');
                $article->image_default = $this->request->getPost('thumbnail');
                $article->status = $this->request->getPost('status');
                $article->type = \ITECH\Data\Lib\Constant::ARTICLE_TYPE_DEFAULT;
                $article->created_ip = $this->request->getClientAddress();
                $article->user_agent = $this->request->getUserAgent();
                $article->created_by = $userSession['id'];
                $article->updated_by = $userSession['id'];
                $article->created_at = date('Y-m-d H:i:s');
                $article->updated_at = date('Y-m-d H:i:s');
                $article->module = \ITECH\Data\Lib\Constant::ARTICLE_MODULE_PAGE;

                if (!$article->create()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'article_edit_page', 'query' => '?' . http_build_query(array('id' => $article->id))));
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
                'title' => 'Danh sách trang tĩnh',
                'url' => $this->url->get([
                    'for' => 'article_list_page'
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm bài viết',
                'url' => $this->url->get([
                    'for' => 'article_add_page'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/article/page_add');
    }

    public function pageEditAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$article) {
            throw new \Phalcon\Exception('Không tồn tại bài viết này.');
        }

        if ($article->image_default != '') {
            $article->image_default_url = $this->config->asset->frontend_url . 'upload/article/' . $article->image_default;
        }

        $article->gallery = json_decode($article->gallery);

        $articleCategories = array();
        if (count($article->getArticleCategory())) {
            foreach ($article->getArticleCategory() as $item) {
                $articleCategories[] = $item->category_id;
            }
        }

        $article->category = array_filter($articleCategories);

        $form = new \ITECH\Admin\Form\PageForm($article);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->name = $this->request->getPost('name');
                $article->name_eng = $this->request->getPost('name_eng');
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->description = $this->request->getPost('description');
                $article->description_eng = $this->request->getPost('description_eng');
                $article->image_default = $this->request->getPost('thumbnail');
                $article->status = $this->request->getPost('status');
                $article->updated_by = $userSession['id'];
                $article->updated_at = date('Y-m-d H:i:s');

                $article->category = null;
                if (!$article->update()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'article_edit_page', 'query' => '?' . http_build_query(array('id' => $article->id))));
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
                'title' => 'Danh sách trang tĩnh',
                'url' => $this->url->get([
                    'for' => 'article_list_page'
                ]),
                'active' => false
            ],
            [
                'title' => $article->name,
                'url' => $this->url->get([
                    'for' => 'article_edit_page',
                    'id' => $id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'article' => $article
        ));

        $this->view->pick(parent::$theme . '/article/page_edit');
    }

    public function pageDeleteAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $page = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$page) {
            throw new \Exception('Không tồn tại trang tĩnh này.');
        }

        $page->status = \ITECH\Data\Lib\Constant::ARTICLE_STATUS_REMOVED;
        $page->updated_by = $userSession['id'];
        $page->updated_at = date('Y-m-d H:i:s');

        try {
            if (!$page->save()) {
                $messages = $page->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa.';
                $this->flashSession->error($message);
            } else {
                $this->flashSession->success('Xóa thành công.');
            }

            return $this->response->redirect(array('for' => 'article_list_page'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function fengshuiAction()
    {
        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'module' => \ITECH\Data\Lib\Constant::ARTICLE_MODULE_FENGSHUI,
                'status' => \ITECH\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
            ),
            'page' => $page,
            'limit' => $limit
        );
        $articleRepo = new \ITECH\Data\Repo\ArticleRepo();
        $articles = $articleRepo->getPaginationList($params);

        $categoryLayout = array();
        if ($articles->total_items > 0) {
            foreach ($articles->items as $item) {
                $params = array(
                    'conditions' => array(
                        'article_id' => $item->id
                    )
                );
                $articleCategoryRepo = new \ITECH\Data\Repo\ArticleCategoryRepo();
                $articleCategory = $articleCategoryRepo->getList($params);
                if (count($articleCategory)) {
                    foreach ($articleCategory as $category) {
                        $categoryLayout[$item->id][] = $category->category_name;
                    }
                }
            }
        }

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'article_list_fengshui'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($articles->total_pages) ? $articles->total_pages : 0,
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
                'title' => 'Danh sách bài viết phong thuỷ',
                'url' => $this->url->get([
                    'for' => 'article_list_fengshui'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'articles' => $articles->items,
            'categoryLayout' => $categoryLayout
        ));

        $this->view->pick(parent::$theme . '/article/fengshui');
    }

    public function fengshuiAddAction()
    {
        $userSession = $this->session->get('USER');

        $article = new \ITECH\Data\Model\ArticleModel();
        $form = new \ITECH\Admin\Form\ArticleFengshuiForm($article);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->name = $this->request->getPost('name');
                $article->name_eng = $this->request->getPost('name_eng');
                $article->image_default = $this->request->getPost('image_default');
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->intro = $this->request->getPost('intro');
                $article->intro_eng = $this->request->getPost('intro_eng');
                $article->description = $this->request->getPost('description');
                $article->description_eng = $this->request->getPost('description_eng');
                $article->status = $this->request->getPost('status');
                $article->type = $this->request->getPost('type');
                $article->created_ip = $this->request->getClientAddress();
                $article->user_agent = $this->request->getUserAgent();
                $article->created_by = $userSession['id'];
                $article->updated_by = $userSession['id'];
                $article->created_at = date('Y-m-d H:i:s');
                $article->updated_at = date('Y-m-d H:i:s');
                $article->category_id = $this->request->getPost('category_id');
                $article->module = \ITECH\Data\Lib\Constant::ARTICLE_MODULE_FENGSHUI;

                $image = $this->request->getPost('gallery');
                $gallery = array();
                if (count($image)) {
                    foreach ($image as $k => $v) {
                        if ($k) {}
                        $gallery[] = array(
                            'image' => $v
                        );
                    }
                }
                $article->gallery = json_encode($gallery);

                if (!$article->create()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {

                    if ($this->request->hasPost('category_id')) {
                        $category_id = $this->request->getPost('category_id');
                        $articleCategory = new \ITECH\Data\Model\ArticleCategoryModel();
                        $articleCategory->article_id = $article->id;
                        $articleCategory->category_id = $category_id;
                        $articleCategory->create();
                    }

                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'article_edit_fengshui', 'query' => '?' . http_build_query(array('id' => $article->id))));
                }
            }
        }

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $aParams = array();
        $aParams['cache'] = false;
        $aParams['parent_id'] = (int) 0;
        $aParams['module'] = \ITECH\Data\Lib\Constant::CATEGORY_MODULE_FENGSHUI;

        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $categoryList = $r['result'];

        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách bài viết phong thuỷ',
                'url' => $this->url->get([
                    'for' => 'article_list_fengshui'
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm bài viết phong thủy',
                'url' => $this->url->get([
                    'for' => 'article_add_fengshui'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'categoryList' => $categoryList
        ));

        $this->view->pick(parent::$theme . '/article/fengshui_add');
    }

    public function fengshuiEditAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$article) {
            throw new \Phalcon\Exception('Không tồn tại bài viết này.');
        }

        $article->gallery = json_decode($article->gallery);

        $form = new \ITECH\Admin\Form\ArticleFengshuiForm($article);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->name = $this->request->getPost('name');
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->name_eng = $this->request->getPost('name_eng');
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->image_default = $this->request->getPost('image_default');
                $article->intro = $this->request->getPost('intro');
                $article->description = $this->request->getPost('description');
                $article->status = $this->request->getPost('status');
                $article->type = $this->request->getPost('type');
                $article->updated_by = $userSession['id'];
                $article->updated_at = date('Y-m-d H:i:s');
                $article->category_id = $this->request->getPost('category_id');

                $image = $this->request->getPost('gallery');
                $imageDescription = $this->request->getPost('image_description');
                if ($imageDescription) {}

                $gallery = array();

                if (count($image)) {
                    foreach ($image as $k => $v) {
                        if ($k) {}
                        $gallery[] = array(
                            'image' => $v
                        );
                    }
                }
                $article->gallery = json_encode($gallery);

                if (!$article->update()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {

                    if ($this->request->hasPost('category_id')) {
                        $category_id = $this->request->getPost('category_id');
                        $hasCategory = \ITECH\Data\Model\ArticleCategoryModel::count(array(
                            'conditions' => 'category_id = :category_id: AND article_id = :article_id:',
                            'bind' => array(
                                'category_id' => $category_id,
                                'article_id' => $article->id
                            )
                        ));

                        if ($hasCategory == 0) {

                            $articleCategory = new \ITECH\Data\Model\ArticleCategoryModel();
                            $articleCategory->article_id = $article->id;
                            $articleCategory->category_id = $category_id;
                            $articleCategory->create();
                        }
                    }

                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'article_edit_fengshui', 'query' => '?' . http_build_query(array('id' => $article->id))));
                }
            }
        }

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $aParams = array();
        $aParams['cache'] = false;
        $aParams['parent_id'] = (int) 0;
        $aParams['module'] = \ITECH\Data\Lib\Constant::CATEGORY_MODULE_FENGSHUI;

        $aParams['authorized_token'] = $authorizedToken;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);

        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $categoryList = $r['result'];
        
        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Danh sách bài viết phong thuỷ',
                'url' => $this->url->get([
                    'for' => 'article_list_fengshui'
                ]),
                'active' => false
            ],
            [
                'title' => $article->name,
                'url' => $this->url->get([
                    'for' => 'article_edit_fengshui',
                    'id' => $id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'article' => $article,
            'categoryList' => $categoryList
        ));

        $this->view->pick(parent::$theme . '/article/fengshui_edit');
    }

    public function specialListAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'module' => \ITECH\Data\Lib\Constant::ARTICLE_MODULE_SPECIAL,
                'status' => \ITECH\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
            ),
            'page' => $page,
            'limit' => $limit
        );

        $articleRepo = new \ITECH\Data\Repo\ArticleRepo();
        $articles = $articleRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'article_special_list'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($articles->total_items) ? $articles->total_items : 0,
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
                'title' => 'Danh sách tin special',
                'url' => $this->url->get([
                    'for' => 'article_special_list',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'articles' => $articles->items
        ));

        $this->view->setVars(array());
        $this->view->pick(parent::$theme . '/article/special_list');
    }

    public function specialAddAction()
    {
        $userSession = $this->session->get('USER');

        $article = new \ITECH\Data\Model\ArticleModel();
        $form = new \ITECH\Admin\Form\ArticleSpecialForm();

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->image_default = $this->request->getPost('thumbnail');
                $article->type = \ITECH\Data\Lib\Constant::ARTICLE_TYPE_DEFAULT;
                $article->module = \ITECH\Data\Lib\Constant::ARTICLE_MODULE_SPECIAL;
                $article->created_ip = $this->request->getClientAddress();
                $article->user_agent = $this->request->getUserAgent();
                $article->created_by = $userSession['id'];
                $article->updated_by = $userSession['id'];
                $article->created_at = date('Y-m-d H:i:s');
                $article->updated_at = date('Y-m-d H:i:s');

                if (!$article->save()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'article_special_list'));
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
                'title' => 'Danh sách tin special',
                'url' => $this->url->get([
                    'for' => 'article_special_list',
                ]),
                'active' => false
            ],
            [
                'title' => 'Thêm tin special',
                'url' => $this->url->get([
                    'for' => 'article_special_add'
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/article/special_add');
    }

    public function specialEditAction()
    {
        $userSession = $this->session->get('USER');

        $id = $this->request->getQuery('id', array('int'), '');

        $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$article) {
            throw new \Exception('Không tồn tại bài viết này.');
        }

        $form = new \ITECH\Admin\Form\ArticleSpecialForm($article);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $article);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $article->slug = \ITECH\Data\Lib\Util::slug($article->name);
                $article->slug_eng = \ITECH\Data\Lib\Util::slug($article->name_eng);
                $article->image_default = $this->request->getPost('thumbnail');
                $article->type = \ITECH\Data\Lib\Constant::ARTICLE_TYPE_DEFAULT;
                $article->module = \ITECH\Data\Lib\Constant::ARTICLE_MODULE_SPECIAL;
                $article->user_agent = $this->request->getUserAgent();
                $article->updated_by = $userSession['id'];
                $article->updated_at = date('Y-m-d H:i:s');

                if (!$article->save()) {
                    $messages = $article->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'article_special_edit', 'query' => '?' . http_build_query(array('id' => $id))));
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
                'title' => 'Danh sách tin special',
                'url' => $this->url->get([
                    'for' => 'article_special_list',
                ]),
                'active' => false
            ],
            [
                'title' => $article->name,
                'url' => $this->url->get([
                    'for' => 'article_special_add',
                    'id' => $article->id
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/article/special_edit');
    }

    public function specialDeleteAction()
    {
        $userSession = $this->session->get('USER');

        $id = $this->request->getQuery('id', array('int'), '');

        $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$article) {
            throw new \Exception('Không tồn tại bài viết này.');
        }

        $article->status = \ITECH\Data\Lib\Constant::ARTICLE_STATUS_REMOVED;
        $article->updated_by = $userSession['id'];
        $article->updated_at = date('Y-m-d H:i:s');

        try {
            if (!$article->save()) {
                $messages = $article->getMessages();
                $message = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể xóa.';
                $this->flashSession->error($message);
            } else {
                $this->flashSession->success('Xóa thành công.');
            }

            return $this->response->redirect(array('for' => 'article_special_list'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}