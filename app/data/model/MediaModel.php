<?php
namespace ITECH\Data\Model;

class MediaModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $category_id;
    public $name;
    public $type;
    public $url;
    public $attribute;
    public $relative_path;
    public $size;
    public $created_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_media');

        $this->belongsTo('category_id', 'ITECH\Data\Model\MediaTermModel', 'category_id', array(
            'alias' => 'Category',
            'foreignKey' => true
        ));
    }
}
