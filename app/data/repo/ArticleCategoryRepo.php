<?php
namespace ITECH\Data\Repo;

class ArticleCategoryRepo extends \ITECH\Data\Model\ArticleCategoryModel
{
    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\ArticleCategoryModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'ac1.article_id',
            'ac1.category_id',
            'c1.id AS category_id',
            'c1.name AS category_name',
            'c1.name_eng AS category_name_eng',
            'c1.slug AS category_slug',
            'c1.slug_eng AS category_slug_eng',
            'c1.parent_id AS category_parent_id',
            'c1.icon AS category_icon',
            'c1.image AS category_image',
            'c1.article_count AS category_article_count',
            'c1.meta_title AS category_meta_title',
            'c1.meta_description AS category_meta_description',
            'c1.meta_keyword AS category_meta_keyword'
        ));

        $b->from(array('ac1' => 'ITECH\Data\Model\ArticleCategoryModel'));
        $b->innerJoin('ITECH\Data\Model\CategoryModel', 'c1.id = ac1.category_id', 'c1');
        
        $b->andWhere('c1.status = :category_status:', array('category_status' => \ITECH\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));

        if (isset($params['conditions']['article_id'])) {
            $b->andWhere('ac1.article_id = :article_id:', array('article_id' => $params['conditions']['article_id']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('c1.id DESC');
        }

        return $b->getQuery()->execute();
    }
}