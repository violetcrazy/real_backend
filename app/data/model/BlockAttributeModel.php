<?php
namespace ITECH\Data\Model;

class BlockAttributeModel extends \ITECH\Data\Model\BaseModel
{
    public $block_id;
    public $attribute_id;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_block_attribute');

        $this->belongsTo('block_id', 'ITECH\Data\Model\BlockModel', 'id', array(
            'alias' => 'Block',
            'foreignKey' => true
        ));

        $this->belongsTo('attribute_id', 'ITECH\Data\Model\AttributeModel', 'id', array(
            'alias' => 'Attribute',
            'foreignKey' => true
        ));
    }
}