<?php
namespace ITECH\Data\Model;

class LinkModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $group_id;
    public $parent_id;
    public $name;
    public $name_eng;
    public $slug;
    public $slug_eng;
    public $icon;
    public $url;
    public $target;
    public $status;
    public $ordering;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_link');

        $this->belongsTo('group_id', 'ITECH\Data\Model\GroupModel', 'id', array(
            'alias'      => 'Group',
            'foreignKey' => true
        ));
    }
}
