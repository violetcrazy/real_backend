<?php
namespace ITECH\Data\Repo;

class MessageRepo extends \ITECH\Data\Model\MessageModel
{

    public function countMessageById($params)
    {
        $b = \ITECH\Data\Model\MessageModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'mt1.message_id'
        ));
        if (isset($params['conditions']['id']) && $params['conditions']['id'] != '' ) {
            $b->andWhere('mt1.user_id = :id:', array('id' => $params['conditions']['id']));
        }
        if (isset($params['conditions']['status']) && $params['conditions']['status'] != '' ) {
            $b->andWhere('(m1.status_send = :status_send: OR m1.status_receive = :status_receive:)', array('status_send' => $params['conditions']['status'], 'status_receive' => $params['conditions']['status']));
        }
        if (isset($params['conditions']['read']) && $params['conditions']['read'] != '' ) {
            $b->andWhere('mt1.read = :read:', array('read' => $params['conditions']['read']));
        }
        if (isset($params['conditions']['created_by']) && $params['conditions']['created_by'] != '' ) {
            $b->andWhere('m1.created_by = :created_by:', array('created_by' => $params['conditions']['created_by']));
        } 
        $b->from(array('mt1' => 'ITECH\Data\Model\MessageToModel'));
        $b->innerJoin('ITECH\Data\Model\MessageModel', 'mt1.message_id = m1.id', 'm1');
        return $b->getQuery()->execute()->count();
    }
    
    public function getListMessageById(array $params)
    {
        $b = \ITECH\Data\Model\MessageModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'mt1.message_id',
            'mt1.user_id',
            'm1.id',
            'm1.name',
            'm1.phone',
            'm1.email',
            'm1.description',
            'm1.type',
            'm1.status_send',
            'm1.status_receive',
            'mt1.read',
            'm1.created_at',
            'm1.updated_at',
            'm1.created_by',
            'm1.updated_by',
        ));

        $b->from(array('mt1' => 'ITECH\Data\Model\MessageToModel'));
        $b->innerJoin('ITECH\Data\Model\MessageModel', 'mt1.message_id = m1.id', 'm1');
        if (isset($params['conditions']['id']) && (isset($params['conditions']['filter'])) && ($params['conditions']['filter'] == 'send')) {
            $b->andWhere('m1.created_by = :id:', array('id' => $params['conditions']['id']));
            $b->andWhere('m1.status_send = :status_send:', array('status_send' => \ITECH\Data\Lib\Constant::MESSAGE_STATUS_ACTIVE));
        }
        if (isset($params['conditions']['id']) && (isset($params['conditions']['filter'])) && ($params['conditions']['filter'] == 'inbox')) {
            $b->andWhere('mt1.user_id = :id:', array('id' => $params['conditions']['id']));
            $b->andWhere('m1.status_receive = :status_receive:', array('status_receive' => \ITECH\Data\Lib\Constant::MESSAGE_STATUS_ACTIVE));
        }
        if (isset($params['conditions']['id']) && (isset($params['conditions']['filter'])) && ($params['conditions']['filter'] == 'trash')) {
            $b->andWhere('mt1.user_id = :id:', array('id' => $params['conditions']['id']));
            $b->andWhere('m1.status_receive = :status_receive:', array('status_receive' => \ITECH\Data\Lib\Constant::MESSAGE_STATUS_INACTIVE));
            //$b->from(array('m1' => 'ITECH\Data\Model\MessageModel'));
        }
        if (isset($params['conditions']['id']) && (isset($params['conditions']['filter'])) && ($params['conditions']['filter'] == 'delete')) {
            $b->andWhere('mt1.user_id = :id:', array('id' => $params['conditions']['id']));
            $b->andWhere('m1.status_receive = :status_receive:', array('status_receive' => \ITECH\Data\Lib\Constant::MESSAGE_STATUS_REMOVED));
            //$b->from(array('m1' => 'ITECH\Data\Model\MessageModel'));
        }

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'm1.name LIKE :q1:';
            $query[] = 'm1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }
        
        if (isset($params['conditions']['type'])) {
            $b->andWhere('m1.type = :type:', array('type' => $params['conditions']['type']));
        }
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('m1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}