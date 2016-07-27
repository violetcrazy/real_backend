<?php
namespace ITECH\Data\Repo;

class UserContactRepo extends \ITECH\Data\Model\UserModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\UserModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'u1.id',
            'u1.user_id',
            'u1.created_at',
            'u1.customer'
        ));

        $b->from(array('u1' => 'ITECH\Data\Model\UserContactModel'));

        if (isset($params['user_id'])) {
            $b->andWhere('u1.user_id = :user_id:', array('user_id' => $params['user_id']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('u1.created_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}