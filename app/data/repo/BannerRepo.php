<?php
namespace ITECH\Data\Repo;

class BannerRepo extends \ITECH\Data\Model\BannerModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\BannerModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'b1.id',
            'b1.name',
            'b1.slug',
            'b1.url',
            'b1.description',
            'b1.click',
            'b1.image',
            'b1.status',
            'b1.created_at',
            'b1.updated_at',
            'b1.created_by',
            'b1.updated_by'
        ));

        $b->from(array('b1' => 'ITECH\Data\Model\BannerModel'));

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'b1.slug LIKE :q1:';
            $query[] = 'b1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('b1.updated_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}