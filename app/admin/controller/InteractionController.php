<?php
namespace ITECH\Admin\Controller;

class InteractionController extends \ITECH\Admin\Controller\BaseController
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
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'page' => $page,
            'limit' => $limit
        );
        $messageRepo = new \ITECH\Data\Repo\MessageRepo();
        $result = $messageRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'interaction'));
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
        ));

        $this->view->pick(parent::$theme . '/interaction/index');
    }

    public function addAction()
    {
        $userSession = $this->session->get('USER');

        $message = new \ITECH\Data\Model\MessageModel();
        $form = new \ITECH\Admin\Form\MessageForm($message, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->description = $this->request->getPost('description');
                $message->created_by = $userSession['id'];
                $message->updated_by = $userSession['id'];
                $message->created_at = date('Y-m-d H:i:s');
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->create()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'interaction_edit', 'query' => '?' . http_build_query(array('id' => $message->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/interaction/add');
    }

    public function editAction()
    {
        $userSession = $this->session->get('USER');
        $id = $this->request->getQuery('id', array('int'), '');

        $message = \ITECH\Data\Model\MessageModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$message) {
            throw new \Phalcon\Exception('Không tồn tại thông báo này.');
        }

        $form = new \ITECH\Admin\Form\MessageForm($message);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->description = $this->request->getPost('description');
                $message->updated_by = $userSession['id'];
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->update()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Cập nhật thành công.');
                    return $this->response->redirect(array('for' => 'interaction_edit', 'query' => '?' . http_build_query(array('id' => $message->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form,
            'message' => $message
        ));

        $this->view->pick(parent::$theme . '/interaction/edit');
    }

    public function emailListAction()
    {
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $params = array(
            'conditions' => array(
                'type' => \ITECH\Data\Lib\Constant::MESSAGE_INBOX_TYPE_SYSTEM_EMAIL
            ),
            'page' => $page,
            'limit' => $limit
        );
        $messageRepo = new \ITECH\Data\Repo\MessageRepo();
        $result = $messageRepo->getPaginationList($params);

        $query = array();
        $query['page'] = $page;

        $url = $this->url->get(array('for' => 'interaction_list_email'));
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
        ));

        $this->view->pick(parent::$theme . '/interaction/email_list');
    }

    public function emailAddAction()
    {
        $userSession = $this->session->get('USER');

        $message = new \ITECH\Data\Model\MessageModel();
        $form = new \ITECH\Admin\Form\EmailForm($message, $this);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->email = $this->request->getPost('email');
                $message->type = \ITECH\Data\Lib\Constant::MESSAGE_INBOX_TYPE_SYSTEM_EMAIL;
                $message->description = $this->request->getPost('description');
                $message->created_by = $userSession['id'];
                $message->updated_by = $userSession['id'];
                $message->created_at = date('Y-m-d H:i:s');
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->create()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    return $this->response->redirect(array('for' => 'interaction_edit_email', 'query' => '?' . http_build_query(array('id' => $message->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/interaction/email_add');
    }

    public function emailEditAction()
    {
        $userSession = $this->session->get('USER');

        $id = $this->request->getQuery('id', array('int'), '');

        $message = \ITECH\Data\Model\MessageModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$message) {
            throw new \Phalcon\Exception('Không tồn tại thông báo này.');
        }

        $form = new \ITECH\Admin\Form\EmailForm($message);
        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $message);

            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin chưa hợp lệ.');
            } else {
                $message->name = $this->request->getPost('name');
                $message->email = $this->request->getPost('email');
                $message->description = $this->request->getPost('description');
                $message->created_by = $userSession['id'];
                $message->updated_by = $userSession['id'];
                $message->created_at = date('Y-m-d H:i:s');
                $message->updated_at = date('Y-m-d H:i:s');

                if (!$message->update()) {
                    $messages = $message->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Tạo thành công.');
                    return $this->response->redirect(array('for' => 'interaction_edit_email', 'query' => '?' . http_build_query(array('id' => $message->id))));
                }
            }
        }

        $this->view->setVars(array(
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/interaction/email_edit');
    }
}
