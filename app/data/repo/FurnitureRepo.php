<?php
namespace ITECH\Data\Repo;

class FurnitureRepo extends \ITECH\Data\Model\FurnitureModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\FurnitureModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.intro',
            'a1.intro_eng',
            'a1.email',
            'a1.phone',
            'a1.address',
            'a1.address_eng',
            'a1.logo'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\FurnitureModel'));

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('a1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}