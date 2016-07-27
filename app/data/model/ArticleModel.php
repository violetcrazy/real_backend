<?php
namespace ITECH\Data\Model;

class ArticleModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $category_id;
    public $project_id;
    public $name;
    public $name_eng;
    public $slug;
    public $slug_eng;
    public $intro;
    public $intro_eng;
    public $description;
    public $description_eng;
    public $image_default;
    public $gallery;
    public $view_count;
    public $type;
    public $status;
    public $ordering;
    public $module;
    public $created_by;
    public $created_ip;
    public $updated_by;
    public $user_agent;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_article');

        $this->hasMany('id', 'ITECH\Data\Model\ArticleCategoryModel', 'article_id', array(
            'alias' => 'ArticleCategory',
            'reusable' => true
        ));

        $this->belongsTo('category_id', 'ITECH\Data\Model\CategoryModel', 'id', array(
            'alias' => 'Category',
            'reusable' => true
        ));

        $this->belongsTo('project_id', 'ITECH\Data\Model\ProjectModel', 'id', array(
            'alias' => 'Project',
            'reusable' => true
        ));
    }
}