<?php
namespace ITECH\Admin\Controller;

class CeriterialController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();
        parent::allowRole(array(\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN));
    }

    public function indexAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'page' => $page,
            'limit' => $limit
        );

        $apartmentCeriterialRepo = new \ITECH\Data\Repo\ApartmentCeriterialRepo();
        $ceriterials = $apartmentCeriterialRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'ceriterial'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($ceriterials->total_pages) ? $ceriterials->total_pages : 0,
            'page' => $page,
            'pages_display' => 3
        );

        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'ceriterials' => $ceriterials->items
        ));

        $this->view->pick(parent::$theme . '/ceriterial/index');
    }

    public function buyListAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'type' => \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_BUY
            ),
            'page' => $page,
            'limit' => $limit
        );
        $apartmentCeriterialRepo = new \ITECH\Data\Repo\ApartmentCeriterialRepo();
        $ceriterials = $apartmentCeriterialRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'ceriterial_buy_list'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($ceriterials->total_pages) ? $ceriterials->total_pages : 0,
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
                'title' => 'Box hiển thị sản phẩm cần bán',
                'url' => $this->url->get([
                    'for' => 'ceriterial_buy_list',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'ceriterials' => $ceriterials->items
        ));

        $this->view->pick(parent::$theme . '/ceriterial/buy_list');
    }

    public function rentListAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'type' => \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_RENT
            ),
            'page' => $page,
            'limit' => $limit
        );
        $apartmentCeriterialRepo = new \ITECH\Data\Repo\ApartmentCeriterialRepo();
        $ceriterials = $apartmentCeriterialRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'ceriterial_rent_list'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($ceriterials->total_pages) ? $ceriterials->total_pages : 0,
            'page' => $page,
            'pages_display' => 3
        );

        $layoutComponent = new \ITECH\Admin\Component\LayoutComponent();
        $paginationLayout = $layoutComponent->pagination(parent::$theme, $options);

        $this->view->setVars(array(
            'paginationLayout' => $paginationLayout,
            'ceriterials' => $ceriterials->items
        ));

        $this->view->pick(parent::$theme . '/ceriterial/rent_list');
    }

    public function smartSearchListAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array('type' => \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_SMART_SEARCH),
            'page' => $page,
            'limit' => $limit
        );
        $apartmentCeriterialRepo = new \ITECH\Data\Repo\ApartmentCeriterialRepo();
        $ceriterials = $apartmentCeriterialRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'ceriterial_smart_search_list'));
        $options = array(
            'url' => $url,
            'query' => $query,
            'total_pages' => isset($ceriterials->total_pages) ? $ceriterials->total_pages : 0,
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
                'title' => 'Smart search',
                'url' => $this->url->get([
                    'for' => 'ceriterial_smart_search_list',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'paginationLayout' => $paginationLayout,
            'ceriterials' => $ceriterials->items
        ));

        $this->view->pick(parent::$theme . '/ceriterial/smart_search_list');
    }

    public function addAction()
    {
        $userSession = $this->session->get('USER');
        $for = $this->request->getQuery('for', array('striptags', 'trim', 'lower'), '');

        $apartmentCeriterial = new \ITECH\Data\Model\ApartmentCeriterialModel();

        switch ($for) {
            case 'buy':
                $apartmentCeriterial->type = \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_BUY;
                $titleBreadcrumbs = 'Thêm box hiển thị sản phẩm cần bán';
            break;

            case 'rent':
                $apartmentCeriterial->type = \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_RENT;
                $titleBreadcrumbs = 'Thêm box hiển thị sản phẩm cho thuê';
            break;

            case 'smart-search':
                $apartmentCeriterial->type = \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_SMART_SEARCH;
                $titleBreadcrumbs = 'Thêm smart search';
            break;
        }

        $form = new \ITECH\Admin\Form\ApartmentCeriterialForm($apartmentCeriterial);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $apartmentCeriterial);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $apartmentCeriterial->name = $this->request->getPost('name');
                $apartmentCeriterial->name_eng = $this->request->getPost('name_eng');

                if ($for != 'smart-search') {
                    $apartmentCeriterial->type = $this->request->getPost('type');
                }

                $apartmentCeriterial->is_new = null;
                $apartmentCeriterial->status = $this->request->getPost('status');
                $apartmentCeriterial->created_by = $userSession['id'];
                $apartmentCeriterial->updated_by = $userSession['id'];
                $apartmentCeriterial->created_at = date('Y-m-d H:i:s');
                $apartmentCeriterial->updated_at = date('Y-m-d H:i:s');

                $apartmentCeriterial->bathroom_count = $this->request->getPost('bathroom_count');
                if ($apartmentCeriterial->bathroom_count <= 0) {
                    $apartmentCeriterial->bathroom_count = null;
                }

                $apartmentCeriterial->bedroom_count = $this->request->getPost('bedroom_count');
                if ($apartmentCeriterial->bedroom_count <= 0) {
                    $apartmentCeriterial->bedroom_count = null;
                }

                $apartmentCeriterial->price_min = $this->request->getPost('price_min');
                if ($apartmentCeriterial->price_min <= 0) {
                    $apartmentCeriterial->price_min = null;
                }

                $apartmentCeriterial->price_max = $this->request->getPost('price_max');
                if ($apartmentCeriterial->price_max <= 0) {
                    $apartmentCeriterial->price_max = null;
                }

                if ($this->request->getPost('price') > 0) {
                    $apartmentCeriterial->price = $this->request->getPost('price');
                } else {
                    $apartmentCeriterial->price = null;
                }

                $apartmentCeriterial->price_min_eng = $this->request->getPost('price_min_eng');
                if ($apartmentCeriterial->price_min_eng <= 0) {
                    $apartmentCeriterial->price_min_eng = null;
                }

                $apartmentCeriterial->price_max_eng = $this->request->getPost('price_max_eng');
                if ($apartmentCeriterial->price_max_eng <= 0) {
                    $apartmentCeriterial->price_max_eng = null;
                }

                if ($this->request->getPost('price_eng') > 0) {
                    $apartmentCeriterial->price_eng = $this->request->getPost('price_eng');
                } else {
                    $apartmentCeriterial->price_eng = null;
                }

                $array = $this->request->getPost('attribute_type');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_type = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_type = null;
                }

                $array = $this->request->getPost('attribute_view');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_view = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_view = null;
                }

                $array = $this->request->getPost('attribute_utility');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_utility = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_utility = null;
                }

                $array = $this->request->getPost('attribute_room_type');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_room_type = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_room_type = null;
                }

                $array = $this->request->getPost('attribute_best_for');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_best_for = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_best_for = null;
                }

                $array = $this->request->getPost('attribute_suitable_for');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_suitable_for = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_suitable_for = null;
                }

                $array = $this->request->getPost('direction');
                if (!empty($array)) {
                    $apartmentCeriterial->direction = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->direction = null;
                }

                $array = $this->request->getPost('project_ids');
                if (!empty($array)) {
                    $apartmentCeriterial->project_ids = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->project_ids = null;
                }

                $array = $this->request->getPost('direction');
                if (!empty($array)) {
                    $apartmentCeriterial->direction = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->direction = null;
                }

                if (!$apartmentCeriterial->create()) {
                    $messages = $apartmentCeriterial->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'ceriterial_edit', 'query' => '?' . http_build_query(array('id' => $apartmentCeriterial->id, 'for' => $for))));
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
                'title' => $titleBreadcrumbs,
                'url' => $this->url->get([
                    'for' => 'ceriterial_add',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'f' => $for
        ));

        $this->view->pick(parent::$theme . '/ceriterial/add');
    }

    public function editAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');
        $for = $this->request->getQuery('for', array('striptags', 'trim', 'lower'), '');

        $apartmentCeriterial = \ITECH\Data\Model\ApartmentCeriterialModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
    
        if (!$apartmentCeriterial) {
            throw new \Phalcon\Exception('Không tồn tại box hiển thị này.');
        }

        switch ($for) {
            case 'buy':
                $apartmentCeriterial->type = \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_BUY;
                $titleBreadcrumbs = 'Box hiển thị sản phẩm cần bán';
                $urlBreadcrumbs =  $this->url->get([
                    'for' => 'ceriterial_buy_list',
                ]);
            break;

            case 'rent':
                $apartmentCeriterial->type = \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_RENT;
                $titleBreadcrumbs = 'Box hiển thị sản phẩm cho thuê';
                $urlBreadcrumbs =  $this->url->get([
                    'for' => 'ceriterial_rent_list',
                ]);
            break;

            case 'smart-search':
                $apartmentCeriterial->type = \ITECH\Data\Lib\Constant::CERITERIAL_TYPE_SMART_SEARCH;
                $titleBreadcrumbs = 'Smart search';
                $urlBreadcrumbs =  $this->url->get([
                    'for' => 'ceriterial_smart_search_list',
                ]);
            break;
        }

        $apartmentCeriterial->attribute_type = array_filter(explode('-', $apartmentCeriterial->attribute_type));
        $apartmentCeriterial->attribute_view = array_filter(explode('-', $apartmentCeriterial->attribute_view));
        $apartmentCeriterial->attribute_utility = array_filter(explode('-', $apartmentCeriterial->attribute_utility));
        $apartmentCeriterial->attribute_room_type = array_filter(explode('-', $apartmentCeriterial->attribute_room_type));
        $apartmentCeriterial->attribute_best_for = array_filter(explode('-', $apartmentCeriterial->attribute_best_for));
        $apartmentCeriterial->attribute_suitable_for = array_filter(explode('-', $apartmentCeriterial->attribute_suitable_for));
        $apartmentCeriterial->project_ids = array_filter(explode('-', $apartmentCeriterial->project_ids));
        $apartmentCeriterial->direction = array_filter(explode('-', $apartmentCeriterial->direction));
        $form = new \ITECH\Admin\Form\ApartmentCeriterialForm($apartmentCeriterial);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $apartmentCeriterial);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $apartmentCeriterial->name = $this->request->getPost('name');
                $apartmentCeriterial->name_eng = $this->request->getPost('name_eng');
                
                if ($for != 'smart-search') {
                    $apartmentCeriterial->type = $this->request->getPost('type');
                }
                
                $apartmentCeriterial->status = $this->request->getPost('status');
                $apartmentCeriterial->updated_by = $userSession['id'];
                $apartmentCeriterial->updated_at = date('Y-m-d H:i:s');

                $apartmentCeriterial->bathroom_count = $this->request->getPost('bathroom_count');
                if ($apartmentCeriterial->bathroom_count <= 0) {
                    $apartmentCeriterial->bathroom_count = null;
                }

                $apartmentCeriterial->bedroom_count = $this->request->getPost('bedroom_count');
                if ($apartmentCeriterial->bedroom_count <= 0) {
                    $apartmentCeriterial->bedroom_count = null;
                }

                $apartmentCeriterial->price_min = $this->request->getPost('price_min');
                if ($apartmentCeriterial->price_min <= 0) {
                    $apartmentCeriterial->price_min = null;
                }

                $apartmentCeriterial->price_max = $this->request->getPost('price_max');
                if ($apartmentCeriterial->price_max <= 0) {
                    $apartmentCeriterial->price_max = null;
                }

                if ($this->request->getPost('price') > 0) {
                    $apartmentCeriterial->price = $this->request->getPost('price');
                } else {
                    $apartmentCeriterial->price = null;
                }

                $apartmentCeriterial->price_min_eng = $this->request->getPost('price_min_eng');
                if ($apartmentCeriterial->price_min_eng <= 0) {
                    $apartmentCeriterial->price_min_eng = null;
                }

                $apartmentCeriterial->price_max_eng = $this->request->getPost('price_max_eng');
                if ($apartmentCeriterial->price_max_eng <= 0) {
                    $apartmentCeriterial->price_max_eng = null;
                }

                if ($this->request->getPost('price_eng') > 0) {
                    $apartmentCeriterial->price_eng = $this->request->getPost('price_eng');
                } else {
                    $apartmentCeriterial->price_eng = null;
                }

                //----
                $array = $this->request->getPost('attribute_type');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_type = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_type = null;
                }
                //var_dump($apartmentCeriterial->attribute_type); die;
                $array = $this->request->getPost('attribute_view');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_view = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_view = null;
                }

                $array = $this->request->getPost('attribute_utility');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_utility = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_utility = null;
                }

                $array = $this->request->getPost('attribute_room_type');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_room_type = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_room_type = null;
                }

                $array = $this->request->getPost('attribute_best_for');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_best_for = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_best_for = null;
                }

                $array = $this->request->getPost('attribute_suitable_for');
                if (!empty($array)) {
                    $apartmentCeriterial->attribute_suitable_for = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->attribute_suitable_for = null;
                }

                $array = $this->request->getPost('project_ids');
                if (!empty($array)) {
                    $apartmentCeriterial->project_ids = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->project_ids = null;
                }
                //---------

                $array = $this->request->getPost('direction');
                if (!empty($array)) {
                    $apartmentCeriterial->direction = '-' . implode('-', $array) . '-';
                } else {
                    $apartmentCeriterial->direction = null;
                }

                if (!$apartmentCeriterial->update()) {
                    $messages = $apartmentCeriterial->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'ceriterial_edit', 'query' => '?' . http_build_query(array('id' => $apartmentCeriterial->id, 'for' => $for))));
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
                'title' => $titleBreadcrumbs,
                'url' => $urlBreadcrumbs,
                'active' => false
            ],
            [
                'title' => $apartmentCeriterial->name,
                'url' => $this->url->get([
                    'for' => 'ceriterial_edit',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'f' => $for
        ));

        $this->view->pick(parent::$theme . '/ceriterial/edit');
    }
}
