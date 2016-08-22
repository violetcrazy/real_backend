<?php
namespace ITECH\Data\Repo;

class MediaTermRepo extends \ITECH\Data\Model\MediaTermModel
{
    public function getPaginationList(array $params)
    {
        $term = \ITECH\Data\Model\MediaTermModel::getModelsManager()->createBuilder();

        $term->columns(array(
            'term1.category_id',
            'term1.name',
            'term1.parent_id',
            'term1.counter_media'
        ));
        $term->from(array('term1' => 'ITECH\Data\Model\MediaTermModel'));
        $term->orderBy('term1.category_id DESC');
        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $term,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        $page = $paginator->getPaginate();

        $dataCount = $term->getQuery()->execute()->count();
        $page->next = $page->current + 1;
        $page->before = $page->current - 1 > 0 ? $page->current - 1 : 1;
        $page->total_items = $dataCount;
        $page->total_pages = ceil($dataCount / $params['limit']);
        $page->last = $page->total_pages;

        return $page;
    }
}
