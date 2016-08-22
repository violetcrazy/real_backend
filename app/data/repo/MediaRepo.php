<?php
namespace ITECH\Data\Repo;

class MediaRepo extends \ITECH\Data\Model\MediaModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\MediaModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'media.id',
            'media.name',
            'media.type',
            'media.category_id',
            'media.attribute',
            'media.created_at',
            'media.size',
            'media.relative_path'
        ));
        $b->from(array('media' => 'ITECH\Data\Model\MediaModel'));

        if (isset($params['category_id'])) {
            $b->andWhere('media.category_id = :category_id:', array('category_id' => $params['category_id']));
        }
        if (isset($params['type'])) {
            $b->andWhere('media.type = :type:', array('type' => $params['type']));
        }

        $b->orderBy('media.created_at DESC');
        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        $page = $paginator->getPaginate();
        $dataCount = $b->getQuery()->execute()->count();
        $page->next = $page->current + 1;
        $page->before = $page->current - 1 > 0 ? $page->current - 1 : 1;
        $page->total_items = $dataCount;
        $page->total_pages = ceil($dataCount / $params['limit']);
        $page->last = $page->total_pages;

        return $page;
    }

}
