<?php
namespace ITECH\Data\Repo;

class GroupRepo extends \ITECH\Data\Model\GroupModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\GroupModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'g1.id',
            'g1.name',
            'g1.type',
            'g1.status',
            'g1.created_at',
            'g1.updated_at',
            'g1.created_by',
            'g1.updated_by'
        ));

        $b->from(array('g1' => 'ITECH\Data\Model\GroupModel'));

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'g1.slug LIKE :q1:';
            $query[] = 'g1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('g1.type = :type:', array('type' => $params['conditions']['type']));
        }
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('g1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}