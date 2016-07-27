<?php
namespace ITECH\Data\Model;

class ArticleCategoryModel extends \ITECH\Data\Model\BaseModel
{
    public $article_id;
    public $category_id;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_article_category');

        $this->belongsTo('article_id', 'ITECH\Data\Model\ArticleModel', 'id', array(
            'alias' => 'Article',
            'foreignKey' => true
        ));

        $this->belongsTo('category_id', 'ITECH\Data\Model\CategoryModel', 'id', array(
            'alias' => 'Category',
            'foreignKey' => true
        ));
    }
}