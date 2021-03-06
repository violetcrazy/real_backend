<?php
namespace ITECH\Admin\Form;

class AdminForm extends \Phalcon\Forms\Form
{
    public function initialize($model, $options)
    {
        if ($model) {}
        if ($options) {}

        $username = new \Phalcon\Forms\Element\Text('username');
        $username->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên đăng nhập.'
            )),
            new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên đăng nhập phải lớn hơn hoặc bằng 5 ký tự.'
            )),
            new \ITECH\Admin\Form\Validator\UserUsernameValidator(array(
                'message' => 'Tên đăng nhập không hợp lệ.'
            ))
        ));
        $username->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($username);

        if (isset($options['edit'])) {
            $new_password = new \Phalcon\Forms\Element\Text('new_password');
            $this->add($new_password);
        } else {
            $password = new \Phalcon\Forms\Element\Text('password');
            $password->addValidators(array(
                new \Phalcon\Validation\Validator\PresenceOf(array(
                    'message' => 'Yêu cầu nhập mật khẩu.'
                ))
            ));
            $this->add($password);
        }

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập họ tên.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $email = new \Phalcon\Forms\Element\Text('email');
        $email->addValidators(array(
            new \ITECH\Admin\Form\Validator\UserEmailValidator(array(
                'message' => 'Email không hợp lệ.'
            ))
        ));
        $email->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($email);

        if (isset($options['edit'])) {
            $projects     = [];
            $haveProjects = [];

            $userProjects = \ITECH\Data\Model\UserProjectModel::find([
                'conditions' => 'userId = :userId:',
                'bind'       => ['userId' => $options['userSession']['id']]
            ]);

            if ($options['userSession']['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN) {
                if (count($userProjects)) {
                    foreach ($userProjects as $item) {
                        $haveProjects[] = $item->projectId;
                    }
                }
            }

            $userProjects = \ITECH\Data\Model\UserProjectModel::find([
                'conditions' => 'userId = :userId:',
                'bind'       => ['userId' => $options['user']->id]
            ]);

            if (count($userProjects)) {
                foreach ($userProjects as $item) {
                    $haveProjects[] = $item->projectId;
                }
            }

            $haveProjects = array_unique($haveProjects);

            $projects = \ITECH\Data\Model\ProjectModel::find(array(
                'conditions' => 'status = :project_status:',
                'bind'       => array('project_status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE)
            ));

            $projectList = array();

            if (count($projects)) {
                foreach ($projects as $item) {
                    $projectList[$item->id] = $item->name;
                }

                if ($options['userSession']['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN) {
                    $newList = [];

                    foreach ($projectList as $key => $value) {
                        if (in_array($key, $haveProjects)) {
                            $newList[$key] = $value;
                        }
                    }

                    $projectList = $newList;
                }
            }

            $projectIds = new \Phalcon\Forms\Element\Select('projectIds', $projectList);
            $this->add($projectIds);
        }

        $membershipArray = \ITECH\Data\Lib\Constant::getUserMembershipAdministrator();

        if (
            isset($options['userSession']['membership'])
            && $options['userSession']['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ) {
            unset($membershipArray[\ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN]);
        }

        $membership = new \Phalcon\Forms\Element\Select('membership', $membershipArray);
        $this->add($membership);

        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getUserStatus());
        $this->add($status);

        if (
            isset($options['userSession']['membership'])
            && $options['userSession']['membership'] == \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN
        ) {
            $admins = \ITECH\Data\Model\UserModel::find([
                'conditions' => 'type = :type:
                    AND membership = :membership:
                    AND status = :status:',
                'bind' => [
                    'type'       => \ITECH\Data\Lib\Constant::USER_TYPE_ADMINISTRATOR,
                    'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN,
                    'status'     => \ITECH\Data\Lib\Constant::USER_STATUS_ACTIVE
                ]
            ]);

            $adminArray = ['' => '---'];

            if (count($admins)) {
                foreach ($admins as $item) {
                    $adminArray[$item->id] = $item->username;
                }
            }

            $createdBy = new \Phalcon\Forms\Element\Select('created_by', $adminArray);
            $this->add($createdBy);
        }

        $logined_at = new \Phalcon\Forms\Element\Text('logined_at');
        $logined_at->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($logined_at);
    }
}
