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
                $banner->name = $this->request->getPost('name');
                $banner->slug = \ITECH\Data\Lib\Util::slug($banner->name);
                $banner->created_by = $userSession['id'];
                $banner->updated_by = $userSession['id'];
                $banner->created_at = date('Y-m-d H:i:s');
                $banner->updated_at = date('Y-m-d H:i:s');

                $banner->image = json_encode($this->request->getPost('image'));
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
                $banner->name = $this->request->getPost('name');
                $banner->slug = \ITECH\Data\Lib\Util::slug($banner->name);
                $banner->created_by = $userSession['id'];
                $banner->updated_by = $userSession['id'];
                $banner->created_at = date('Y-m-d H:i:s');
                $banner->updated_at = date('Y-m-d H:i:s');
                $banner->image = json_encode($this->request->getPost('image'));

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
            'banner' => $banner,
            'imageList' => json_decode($banner->image)
        ));

        $this->view->pick(parent::$theme . '/banner/edit');
    }
}
