<?php
namespace ITECH\Data\Repo;

class SaveSearchRepo extends \ITECH\Data\Model\UserSaveModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\UserSaveModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'b1.id',
            'b1.user_id',
            'b1.key',
            'b1.value',
            'b1.created_at'
        ));

        $b->from(array('b1' => 'ITECH\Data\Model\UserSaveModel'));

        if (isset($params['conditions']['user_id'])) {
            $b->andWhere('b1.user_id = :user_id:', array('user_id' => $params['conditions']['user_id']));
        }
        
        if (isset($params['conditions']['key'])) {
            $b->andWhere('b1.key = :key:', array('key' => $params['conditions']['key']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('b1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}