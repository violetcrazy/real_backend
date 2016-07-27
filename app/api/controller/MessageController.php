<?php
namespace ITECH\Api\Controller;

class MessageController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function sendAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();
            if ($post == '') {
                $post = $this->request->getPost();
            }
            $validator = new \Phalcon\Validation();
            $language = $post['language'];
            $validator->add('name', new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => ($language == 'vi') ? 'Yêu cầu nhập họ tên.' : 'Enter your name'
            )));
            $validator->setFilters('name', array('striptags', 'trim'));
            $validator->add('phone', new \Phalcon\Validation\Validator\Numericality(array(
                'message' => ($language == 'vi') ? 'Yêu cầu nhập số điện thoại.' : 'Enter your phone'
            )));
            $validator->setFilters('phone', array('striptags', 'trim'));
            $validator->add('email', new \Phalcon\Validation\Validator\Email(array(
                'message' => ($language == 'vi') ? 'Định dạng e-mail không đúng' : 'Invalid email'
            )));
            $validator->setFilters('email', array('striptags', 'trim'));

            $validator->add('content', new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => ($language == 'vi') ? 'Nội dung ít nhất phải 5 ký tự.' : 'Content at least 2 characters'
            )));
            $validator->setFilters('content', array('striptags', 'trim'));
            $validator->setFilters('id', array('striptags', 'trim'));
            $validator->setFilters('created_by', array('striptags', 'trim'));
            $validator->setFilters('type', array('striptags', 'trim'));
            $validator->setFilters('status', array('striptags', 'trim'));
            $messages = $validator->validate($post);
            if(count($messages)) {
                $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result' => $result,
                    'update' => false
                );

                goto RETURN_RESPONSE;
            }

            $mess = new \ITECH\Data\Model\MessageModel();

            $mess->name = \ITECH\Data\Lib\Util::upperFirstLetters($validator->getValue('name'));
            $mess->phone = $validator->getValue('phone');
            $mess->email = $validator->getValue('email');
            $mess->description = $validator->getValue('content');
            $mess->status = $validator->getValue('status');
            $mess->type = $validator->getValue('type');
            $mess->created_at = date('Y-m-d H:i:s');
            $mess->updated_at = date('Y-m-d H:i:s');
            $mess->created_by = $validator->getValue('created_by');
            if (!$mess->create()) {
                $messages = $mess->getMessages();
                if (isset($messages[0])) {
                    $this->flashSession->error($messages[0]->getMessage());
                }
            } else {
                $messTo = new \ITECH\Data\Model\MessageToModel();
                $messTo->message_id = $mess->id;
                $messTo->user_id = $validator->getValue('id');
                $messTo->read = \ITECH\Data\Lib\Constant::MESSAGE_STATUS_UNREAD;
                if (!$messTo->create()) {
                    $messages = $messTo->getMessages();
                    if (isset($messages[0])) {
                        $this->flashSession->error($messages[0]->getMessage());
                    }
                } else {
                    $this->flashSession->success('Thêm thành công.');
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                        'message' => 'Success.',
                        'result' => ''
                    );

                    goto RETURN_RESPONSE;
                }
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function countMessageByIdAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('striptags', 'trim'));
        $status = $this->request->getQuery('status', array('striptags', 'trim'));
        $read = $this->request->getQuery('read', array('striptags', 'trim'));
        $created_by = $this->request->getQuery('created_by', array('striptags', 'trim'));
        $params = array(
            'conditions' => array(
                'id' => $id,
                'status' => $status,
                'read' => $read,
                'created_by' => $created_by,
            )
        );
        $messageRepo = new \ITECH\Data\Repo\MessageRepo();
        $count = $messageRepo->countMessageById($params);
        echo $count;
    }

    public function listAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('striptags', 'trim'));
        $filter = $this->request->getQuery('filter', array('striptags', 'trim'));
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $messageRepo = new \ITECH\Data\Repo\MessageRepo();
        $params = array(
            'conditions' => array(
                'id' => $id,
                'filter' => $filter
            ),
            'order' => 'm1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        $listMessage = $messageRepo->getListMessageById($params);
        if (isset($listMessage->items) && count($listMessage->items)) {
            foreach ($listMessage->items as $message) {
                $result[] = array(
                    'message_id' => (int)$message['message_id'],
                    'user_id' => $message['user_id'],
                    'name' => $message['name'],
                    'phone' => $message['phone'],
                    'email' => $message['email'],
                    'description' => $message['description'],
                    'type' => $message['type'],
                    'status_send' => $message['status_send'],
                    'status_receive' => $message['status_receive'],
                    'read' => $message['read'],
                    'created_at' => date('H:i d-m-Y', strtotime($message['created_at'])),
                    'updated_at' => $message['updated_at'],
                    'created_by' => $message['created_by'],
                    'updated_by' => $message['updated_by'],
                );
            }
            $response['result']['items'] = $result;
            $response['result']['total_items'] = $listMessage->total_items;
            $response['result']['total_pages'] = $listMessage->total_pages;
            $response['result']['first'] = $listMessage->first;
            $response['result']['before'] = $listMessage->before;
            $response['result']['current'] = $listMessage->current;
            $response['result']['last'] = $listMessage->last;
            $response['result']['next'] = $listMessage->next;
            $response['result']['limit'] = $listMessage->limit;
        }
        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function detailAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('striptags', 'trim'));
        $user_id = $this->request->getQuery('id_user', array('striptags', 'trim'));
        $filter = $this->request->getQuery('filter', array('striptags', 'trim'));

        $message = \ITECH\Data\Model\MessageModel::findFirst(array(
            "conditions" => "id = :id:",
            "bind"       => array('id' => $id)
         ));
        if($message) {
            if ($filter == 'inbox') {
                $messTo = \ITECH\Data\Model\MessageToModel::findFirst(array(
                    "conditions" => "message_id = :message_id: AND user_id = :user_id:",
                    "bind"       => array(
                        'message_id' => $message->id,
                        'user_id' => $user_id
                        )
                ));
                if ($messTo && $messTo->read == 1) {
                    $messTo->read = 2;
                    $messTo->update();
                }
            }
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Success.',
                'result' => $message
            );
        } else {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Error.',
                'result' => ''
            );
        }

        return parent::outputJSON($response);
    }
}
