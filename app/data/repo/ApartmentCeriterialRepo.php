<?php
namespace ITECH\Data\Repo;

class ApartmentCeriterialRepo extends \ITECH\Data\Model\ApartmentCeriterialModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentCeriterialModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'ac1.id',
            'ac1.name',
            'ac1.is_home',
            'ac1.type',
            'ac1.status',
            'ac1.updated_at'
        ));

        $b->from(array('ac1' => 'ITECH\Data\Model\ApartmentCeriterialModel'));

        if (isset($params['conditions']['type'])) {
            $b->andWhere('ac1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ac1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}