<?php
namespace ITECH\Admin\Controller;

class BannerController extends \ITECH\Admin\Controller\BaseController
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
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'parent_id' => (int)0
            ),
            'page' => $page,
            'limit' => $limit
        );
        $bannerRepo = new \ITECH\Data\Repo\BannerRepo();
        $result = $bannerRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'banner'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($result->total_pages) ? $result->total_pages : 0,
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
                'title' => 'Danh sách banner',
                'url' => $this->url->get([
                    'for' => 'banner',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'result' => $result->items
        ));

        $this->view->pick(parent::$theme . '/banner/index');
    }

    public function addAction()
    {
        $userSession = $this->session->get('USER');

        $banner = new \ITECH\Data\Model\BannerModel();
        $form = new \ITECH\Admin\Form\BannerForm($banner);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $banner);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                if ($this->request->hasFiles()) {
                    $file = $this->request->getUploadedFiles();

                    if (isset($file[0]) && $file[0]->getName() != '') {
                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        list($width, $height, $type, $attr) = getimagesize($file[0]->getTempName());

                        $group_id = $this->request->getPost('group_id');
                        $group = \ITECH\Data\Model\GroupModel::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array(
                                'id' => $group_id
                            )
                        ));

                        if (isset($group_id->width) && $group_id->width != '') {
                            $size = $group_id->width;
                        } else {
                            if (isset($width)) {
                                $size = $width;
                            } else {
                                $size = (int)940;
                            }
                        }

                        $response = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', $size, $resource);
                        if (!empty($response['status']) && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                            $banner->image = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $response['result'];
                            parent::uploadImageToCdn(ROOT . '/web/admin/asset/images/', 'banner', $banner->image);
                            parent::deleteImageFromLocal(ROOT . '/web/admin/asset/images/', $banner->image);
                        }
                    }
                }

                $banner->name = $this->request->getPost('name');
                $banner->slug = \ITECH\Data\Lib\Util::slug($banner->name);
                $banner->created_by = $userSession['id'];
                $banner->updated_by = $userSession['id'];
                $banner->created_at = date('Y-m-d H:i:s');
                $banner->updated_at = date('Y-m-d H:i:s');

                if (!$banner->create()) {
                    $messages = $banner->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'banner_edit', 'query' => '?' . http_build_query(array('id' => $banner->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/banner/add');
    }

    public function editAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $banner = \ITECH\Data\Model\BannerModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$banner) {
            throw new \Phalcon\Exception('Không tồn tại banner này.');
        }

        if ($banner->image != '') {
            $banner->image_url = $this->config->asset->frontend_url . 'upload/banner/' . $banner->image;
        }

        $form = new \ITECH\Admin\Form\BannerForm($banner, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $banner);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                if ($this->request->hasFiles()) {
                    $file = $this->request->getUploadedFiles();

                    if (isset($file[0]) && $file[0]->getName() != '') {
                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        list($width, $height, $type, $attr) = getimagesize($file[0]->getTempName());

                        $group_id = $this->request->getPost('group_id');
                        $group = \ITECH\Data\Model\GroupModel::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array(
                                'id' => $group_id
                            )
                        ));

                        if (isset($group_id->width) && $group_id->width != '') {
                            $size = $group_id->width;
                        } else {
                            if (isset($width)) {
                                $size = $width;
                            } else {
                                $size = (int)940;
                            }
                        }

                        $response = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', $size, $resource);

                        if (!empty($response['status']) && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                            $banner->image = $response['result'];
                            parent::uploadImageToCdn(ROOT . '/web/admin/asset/upload/', 'banner', $banner->image);
                            parent::deleteImageFromLocal(ROOT . '/web/admin/asset/upload/', $banner->image);
                            $banner->image = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $banner->image;
                        }
                    }
                }

                $banner->name = $this->request->getPost('name');
                $banner->slug = \ITECH\Data\Lib\Util::slug($banner->name);
                $banner->created_by = $userSession['id'];
                $banner->updated_by = $userSession['id'];
                $banner->created_at = date('Y-m-d H:i:s');
                $banner->updated_at = date('Y-m-d H:i:s');

                if (!$banner->update()) {
                    $messages = $banner->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'banner_edit', 'query' => '?' . http_build_query(array('id' => $banner->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'banner' => $banner
        ));

        $this->view->pick(parent::$theme . '/banner/edit');
    }
}
