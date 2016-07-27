<?php
namespace ITECH\Data\Model;

class UserProjectModel extends BaseModel
{
    public $userId;
    public $projectId;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_user_project');

        $this->belongsTo('userId', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));

        $this->belongsTo('projectId', 'ITECH\Data\Model\ProjectModel', 'id', array(
            'alias' => 'Project',
            'foreignKey' => true
        ));
    }
}
