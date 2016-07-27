<?php
namespace ITECH\Data\Model;

class ProjectAttributeModel extends \ITECH\Data\Model\BaseModel
{
    public $project_id;
    public $attribute_id;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_project_attribute');

        $this->belongsTo('project_id', 'ITECH\Data\Model\ProjectModel', 'id', array(
            'alias' => 'Project',
            'foreignKey' => true
        ));

        $this->belongsTo('attribute_id', 'ITECH\Data\Model\AttributeModel', 'id', array(
            'alias' => 'Attribute',
            'foreignKey' => true
        ));
    }
}