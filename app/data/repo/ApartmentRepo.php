<?php
namespace ITECH\Data\Repo;

class ApartmentRepo extends \ITECH\Data\Model\ApartmentModel
{
    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.slug',
            'a1.status',
            'a1.ordering',
            'a1.condition',
            'b1.id AS block_id',
            'b1.name AS block_name',
            'b1.slug AS block_slug',
            'p1.id AS project_id',
            'p1.name AS project_name',
            'p1.slug AS project_slug',
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
        $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

        if (isset($params['conditions']['status'])) {
            $b->andWhere('a1.status = :apartment_status:', array('apartment_status' => $params['conditions']['status']));
        }

        if (isset($params['conditions']['project_id'])) {
            $b->andWhere('a1.project_id = :project_id:', array('project_id' => $params['conditions']['project_id']));
        }
        
        if (isset($params['conditions']['block_id'])) {
            $b->andWhere('a1.block_id = :block_id:', array('block_id' => $params['conditions']['block_id']));
        }

        if (isset($params['conditions']['projectIdsString'])) {
            $b->andWhere('a1.project_id IN (' . $params['conditions']['projectIdsString'] . ')');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('a1.updated_at DESC');
        }

        return $b->getQuery()->execute();
    }

    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.user_id',
            'a1.name',
            'a1.name_eng',
            'a1.slug',
            'a1.slug_eng',
            'a1.condition',
            'a1.type',
            'a1.price',
            'a1.price_eng',
            'a1.price_sale_off',
            'a1.price_sale_off_eng',
            'a1.default_image',
            'a1.gallery',
            'a1.position',
            'a1.position_eng',
            'a1.floor',
            'a1.room_count',
            'a1.bedroom_count',
            'a1.bathroom_count',
            'a1.adults_count',
            'a1.children_count',
            'a1.direction',
            'a1.total_area',
            'a1.green_area',
            'a1.rose',
            'a1.ordering',
            'a1.view_count',
            'a1.status',
            'a1.created_by',
            'a1.updated_by',
            'a1.approved_by',
            'a1.created_at',
            'a1.updated_at',
            'a1.meta_title',
            'a1.meta_title_eng',
            'a1.meta_keywords',
            'a1.meta_keywords_eng',
            'a1.meta_description',
            'a1.meta_description_eng',
            'p1.id AS project_id',
            'p1.name AS project_name',
            'p1.name_eng AS project_name_eng',
            'p1.slug AS project_slug',
            'p1.slug_eng AS project_slug_eng',
            'p1.address AS project_address',
            'p1.address_eng AS project_address_eng',
            'b1.id AS block_id',
            'b1.name AS block_name',
            'b1.name_eng AS block_name_eng',
            'b1.slug AS block_slug',
            'b1.slug_eng AS block_slug_eng'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
        $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

        $b->andWhere('p1.status = :project_status:', array('project_status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE));
        $b->andWhere('b1.status = :block_status:', array('block_status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE));

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'a1.slug LIKE :q1:';
            $query[] = 'b1.slug LIKE :q2:';
            $query[] = 'p1.slug LIKE :q3:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q3' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%'
            ));
        }

        if (isset($params['conditions']['total_area'])) {
            $b->andWhere('a1.total_area = :apartment_total_area:', array('apartment_total_area' => $params['conditions']['total_area']));
        }

        if (isset($params['conditions']['floor'])) {
            $b->andWhere('a1.floor = :apartment_floor:', array('apartment_floor' => $params['conditions']['floor']));
        }

        if (isset($params['conditions']['bedroom_count'])) {
            $b->andWhere('a1.bedroom_count = :apartment_bedroom_count:', array('apartment_bedroom_count' => $params['conditions']['bedroom_count']));
        }

        if (isset($params['conditions']['bedroom_min'])) {
            $b->andWhere('a1.bedroom_count >= :apartment_bedroom_min:', array('apartment_bedroom_min' => $params['conditions']['bedroom_min']));
        }

        if (isset($params['conditions']['bedroom_max'])) {
            $b->andWhere('a1.bedroom_count <= :apartment_bedroom_max:', array('apartment_bedroom_max' => $params['conditions']['bedroom_max']));
        }

        if (isset($params['conditions']['bathroom_count'])) {
            $b->andWhere('a1.bathroom_count = :apartment_bathroom_count:', array('apartment_bathroom_count' => $params['conditions']['bathroom_count']));
        }

        if (isset($params['conditions']['block_id'])) {
            $b->andWhere('b1.id = :block_id:', array('block_id' => $params['conditions']['block_id']));
        }

        if (isset($params['conditions']['project_id'])) {
            $b->andWhere('p1.id = :project_id:', array('project_id' => $params['conditions']['project_id']));
        }

        if (isset($params['conditions']['project_ids'])) {
            $b->inWhere('p1.id', $params['conditions']['project_ids']);
        }

        if (isset($params['conditions']['location'])) {
            $b->andWhere('p1.province_id = :location:', array('location' => $params['conditions']['location']));
        }

        if (isset($params['conditions']['location'])) {
            $b->andWhere('p1.province_id = :location:', array('location' => $params['conditions']['location']));
        }

        if (isset($params['conditions']['adults_count'])) {
            $b->andWhere('a1.adults_count = :adults_count:', array('adults_count' => $params['conditions']['adults_count']));
        }

        if (isset($params['conditions']['children_count'])) {
            $b->andWhere('a1.children_count = :children_count:', array('children_count' => $params['conditions']['children_count']));
        }

        if (isset($params['conditions']['ids'])) {
            $b->inWhere('a1.id', $params['conditions']['ids']);
        }

        if (isset($params['conditions']['id'])) {
            $b->andWhere('a1.id = :id:', array('id' => $params['conditions']['id']));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('a1.status = :apartment_status:', array('apartment_status' => $params['conditions']['status']));
        } else {
            $b->andWhere('a1.status = :apartment_status:', array('apartment_status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
        }

        if (isset($params['conditions']['direction'])) {
            if (is_array($params['conditions']['direction'])) {
                $query = array();
                foreach ($params['conditions']['direction'] as $item) {
                    $query[] = 'a1.direction = "' . $item . '"';
                }

                $b->andWhere(implode(' OR ', $query));
            } else {
                $b->andWhere('a1.direction = :direction:', array('direction' => $params['conditions']['direction']));
            }
        }

        if (isset($params['conditions']['directions_id'])) {
            $b->inWhere('a1.direction', $params['conditions']['directions_id']);
        }

        if (isset($params['conditions']['price_min'])) {
            $b->andWhere('a1.price >= :price_min:', array('price_min' => $params['conditions']['price_min']));
        }

        if (isset($params['conditions']['price_max'])) {
            $b->andWhere('a1.price <= :price_max:', array('price_max' => $params['conditions']['price_max']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('a1.updated_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }

    public function getPaginationListByAttribute(array $params)
    {
        $result = new \stdClass();
        $result->items = array();
        $result->total_items = 0;

        if (isset($params['conditions']['attributes_id'])) {
            $attributes = $params['conditions']['attributes_id'];
            $query = array();
            $i = 1;

            foreach ($attributes as $value) {
                $where = array();
                $q = "SELECT
                        ap$i.id AS id,
                        ap$i.user_id AS user_id,
                        ap$i.name AS name,
                        ap$i.name_eng AS name_eng,
                        ap$i.slug AS slug,
                        ap$i.condition AS condition,
                        ap$i.type AS type,
                        ap$i.price AS price,
                        ap$i.price_eng AS price_eng,
                        ap$i.price_sale_off AS price_sale_off,
                        ap$i.price_sale_off_eng AS price_sale_off_eng,
                        ap$i.default_image AS default_image,
                        ap$i.gallery AS gallery,
                        ap$i.position AS position,
                        ap$i.floor AS floor,
                        ap$i.room_count AS room_count,
                        ap$i.bedroom_count AS bedroom_count,
                        ap$i.bathroom_count AS bathroom_count,
                        ap$i.direction AS direction,
                        ap$i.total_area AS total_area,
                        ap$i.green_area AS green_area,
                        ap$i.ordering AS ordering,
                        ap$i.view_count AS view_count,
                        ap$i.status AS status,
                        ap$i.created_by AS created_by,
                        ap$i.updated_by AS updated_by,
                        ap$i.approved_by AS approved_by,
                        ap$i.created_at AS created_at,
                        ap$i.updated_at AS updated_at,
                        p$i.id AS project_id,
                        p$i.name AS project_name,
                        p$i.name_eng AS project_name_eng,
                        p$i.slug AS project_slug,
                        p$i.slug_eng AS project_slug_eng,
                        p$i.address AS project_address,
                        b$i.id AS block_id,
                        b$i.name AS block_name,
                        b$i.name_eng AS block_name_eng,
                        b$i.slug AS block_slug,
                        b$i.slug_eng AS block_slug_eng
                    FROM `land_apartment` AS ap$i
                    INNER JOIN `land_block` AS b$i ON b$i.id = ap$i.block_id
                    INNER JOIN `land_project` AS p$i ON p$i.id = b$i.project_id
                    INNER JOIN `land_apartment_attribute` AS aa$i ON aa$i.apartment_id = ap$i.id
                    INNER JOIN `land_attribute` AS a$i ON a$i.id = aa$i.attribute_id
                    WHERE ";

                $where[] = "p$i.status = '" . \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE . "'";
                $where[] = "b$i.status = '" . \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE . "'";

                if (isset($params['conditions']['q'])) {
                    $q = array();
                    $q[] = "ap$i.slug LIKE '%" . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . "%'";
                    $q[] = "b$i.slug LIKE '%" . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . "%'";
                    $q[] = "p$i.slug LIKE '%" . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . "%'";

                    $where[] = implode(' OR ', $q);
                }

                if (isset($params['conditions']['status'])) {
                    $where[] = "ap$i.status = '" . $params['conditions']['status'] . "'";
                } else {
                    $where[] = "ap$i.status = '" . \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE . "'";
                }

                if (isset($params['conditions']['total_area'])) {
                    $where[] = "ap$i.total_area = '" . $params['conditions']['total_area'] . "'";
                }

                if (isset($params['conditions']['floor'])) {
                    $where[] = "ap$i.floor = '" . $params['conditions']['floor'] . "'";
                }

                if (isset($params['conditions']['bedroom_count'])) {
                    $where[] = "ap$i.bedroom_count = '" . $params['conditions']['bedroom_count'] . "'";
                }

                if (isset($params['conditions']['bedroom_min'])) {
                    $where[] = "ap$i.bedroom_count >= '" . $params['conditions']['bedroom_min'] . "'";
                }

                if (isset($params['conditions']['bedroom_max'])) {
                    $where[] = "ap$i.bedroom_count <= '" . $params['conditions']['bedroom_max'] . "'";
                }

                if (isset($params['conditions']['bathroom_count'])) {
                    $where[] = "ap$i.bathroom_count = '" . $params['conditions']['bathroom_count'] . "'";
                }

                if (isset($params['conditions']['directions_id'])) {
                    $directionId = array();
                    foreach ($params['conditions']['directions_id'] as $direction) {
                        $directionId[] = "'" . $direction . "'";
                    }

                    $directionId = implode(', ', $directionId);
                    $where[] = "ap$i.direction IN (" . $directionId . ")";
                }

                if (isset($params['conditions']['price_min'])) {
                    $where[] = "ap$i.price >= '" . $params['conditions']['price_min'] . "'";
                }

                if (isset($params['conditions']['price_max'])) {
                    $where[] = "ap$i.price <= '" . $params['conditions']['price_max'] . "'";
                }

                if (isset($params['conditions']['type'])) {
                    $where[] = "ap$i.type <= '" . $params['conditions']['type'] . "'";
                }

                if (isset($params['conditions']['direction'])) {
                    if (isset($params['conditions']['user_membership'])) {
                        if (is_array($params['conditions']['user_membership'])) {
                            $subQuery = array();
                            foreach ($params['conditions']['direction'] as $item) {
                                $subQuery[] = "ap$i.direction = '" . $item . "'";
                            }

                            $where[] = implode(' OR ', $subQuery);
                        } else {
                            $where[] = "ap$i.direction = '" . $params['conditions']['direction'] . "'";
                        }
                    }
                }

                if (isset($params['conditions']['block_id'])) {
                    $where[] = "b$i.id = '" . $params['conditions']['block_id'] . "'";
                }

                if (isset($params['conditions']['project_id']) && count($params['conditions']['project_id'])) {
                    $projectIds = array();
                    foreach ($params['conditions']['project_id'] as $p) {
                        $projectIds[] = "'" . $p . "'";
                    }

                    $projectIds = implode(', ', $projectIds);
                    $where[] = "p$i.id IN (" . $projectIds . ")";
                }

                if (isset($params['conditions']['project_ids'])) {
                    $where[] = "p$i.id = '" . $params['conditions']['project_id'] . "'";
                }

                if (isset($params['conditions']['location'])) {
                    $where[] = "p$i.province_id = '" . $params['conditions']['location'] . "'";
                }

                if (isset($params['conditions']['province_id'])) {
                    $where[] = "p$i.id = '" . $params['conditions']['province_id'] . "'";
                }

                if (isset($params['conditions']['district_id'])) {
                    $where[] = "d$i.id = '" . $params['conditions']['district_id'] . "'";
                }

                $where[] = "a$i.id = '" . $value . "'";
                $where[] = "a$i.status = '" . \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE . "'";

                if (isset($params['conditions']['daytime'])) {
                    $where[] = "DATE_FORMAT(a$i.updated_at, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime('-' . $params['conditions']['daytime'] . ' days')) . "'";
                }

                $q .= implode(' AND ', $where);
                $q .= " GROUP BY id";

                if (isset($params['order'])) {
                    $q .= ' ORDER BY ' . $params['order'];
                } else {
                    $q .= ' ORDER BY updated_at DESC';
                }

                $query[] = $q;
                $i++;
            }

            $q = '';
            $q_select = '';
            $q_count = '';

            if (count($query) == 1) {
                $q = $query[0];

                $q_select = $q;
                $q_count = 'SELECT COUNT(*) AS count FROM (' . $q . ') AS a_count';
            } elseif (count($query) > 1) {
                $process_query = array();
                $i = 1;

                foreach ($query as $item) {
                    if ($i == 1) {
                        $process_query[] = "($item) AS t$i";
                    } elseif ($i > 1) {
                        $j = $i - 1;
                        $process_query[] = "($item) AS t$i ON t$i.id = t$j.id";
                    }

                    $i++;
                }

                $q = implode(' INNER JOIN ', $process_query);
                $q_select = 'SELECT * FROM (' . $q . ')';
                $q_count = 'SELECT COUNT(*) AS count FROM (' . $q . ')';
            }

            if ($q_select != '' && $q_count != '') {
                if (isset($params['limit'])) {
                    $q_select .= ' LIMIT ' . $params['limit'];
                }

                if (isset($params['page'])) {
                    $q_select .= ' OFFSET ' . abs($params['page'] - 1);
                }

                $b = \ITECH\Data\Model\ApartmentModel::getReadConnection()->query($q_count);
                $r = $b->fetch(\PDO::FETCH_ASSOC);
                if (isset($r['count'])) {
                    $result->total_items = (int)$r['count'];
                }

                $b = \ITECH\Data\Model\ApartmentModel::getReadConnection()->query($q_select);
                $r = $b->fetchAll(\PDO::FETCH_ASSOC);
                if (count($r)) {
                    $result->items = $r;
                }
            }
        }

        return $result;
    }

    public function getFull(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.user_id',
            'a1.name',
            'a1.name_eng',
            'a1.slug',
            'a1.description',
            'a1.description_eng',
            'a1.slug_eng',
            'a1.condition',
            'a1.type',
            'a1.price',
            'a1.price_eng',
            'a1.price_sale_off',
            'a1.price_sale_off_eng',
            'a1.default_image',
            'a1.gallery',
            'a1.position',
            'a1.position_eng',
            'a1.floor',
            'a1.room_count',
            'a1.bedroom_count',
            'a1.bathroom_count',
            'a1.adults_count',
            'a1.children_count',
            'a1.direction',
            'a1.total_area',
            'a1.green_area',
            'a1.rose',
            'a1.ordering',
            'a1.view_count',
            'a1.status',
            'a1.created_by',
            'a1.updated_by',
            'a1.approved_by',
            'a1.created_at',
            'a1.updated_at',
            'a1.meta_title',
            'a1.meta_title_eng',
            'a1.meta_keywords',
            'a1.meta_keywords_eng',
            'a1.meta_description',
            'a1.meta_description_eng',
            'p1.id AS project_id',
            'p1.name AS project_name',
            'p1.name_eng AS project_name_eng',
            'p1.slug AS project_slug',
            'p1.slug_eng AS project_slug_eng',
            'p1.address AS project_address',
            'p1.address_eng AS project_address_eng',
            'b1.id AS block_id',
            'b1.name AS block_name',
            'b1.name_eng AS block_name_eng',
            'b1.slug AS block_slug',
            'b1.slug_eng AS block_slug_eng'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
        $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

        $b->andWhere('a1.status = :status:', array('status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));

        if (isset($params['conditions']['id'])) {
            $b->andWhere('a1.id = :id:', array('id' => $params['conditions']['id']));
        }

        if (isset($params['conditions']['price'])) {
            if ($params['conditions']['price'] == 0) {
                $b->andWhere('a1.price IS NULL');
            }
        }

        if (isset($params['conditions']['min_price'])) {
            $b->andWhere('a1.price >= :min_price:', array('min_price' => $params['conditions']['min_price']));
        }

        if (isset($params['conditions']['max_price'])) {
            $b->andWhere('a1.price <= :max_price:', array('max_price' => $params['conditions']['max_price']));
        }

        if (isset($params['conditions']['not_id'])) {
            $b->andWhere('a1.id <> :not_id:', array('not_id' => $params['conditions']['not_id']));
        }

        return $b->getQuery()->execute();
    }

    public function getAllListByProject($projectId)
    {
        $b = \ITECH\Data\Model\ApartmentModel::getModelsManager()->createBuilder();
        $b->columns(array(
            'a1.id',
            'a1.user_id',
            'a1.name',
            'a1.name_eng',
            'a1.slug',
            'a1.slug_eng',
            'a1.condition',
            'a1.type',
            'a1.price',
            'a1.price_eng',
            'a1.price_sale_off',
            'a1.price_sale_off_eng',
            'a1.default_image',
            'a1.gallery',
            'a1.position',
            'a1.position_eng',
            'a1.floor',
            'a1.room_count',
            'a1.bedroom_count',
            'a1.bathroom_count',
            'a1.adults_count',
            'a1.children_count',
            'a1.direction',
            'a1.total_area',
            'a1.green_area',
            'a1.rose',
            'a1.ordering',
            'a1.view_count',
            'a1.status',
            'a1.created_by',
            'a1.updated_by',
            'a1.approved_by',
            'a1.created_at',
            'a1.updated_at',
            'a1.meta_title',
            'a1.meta_title_eng',
            'a1.meta_keywords',
            'a1.meta_keywords_eng',
            'a1.meta_description',
            'a1.meta_description_eng',
            'p1.id AS project_id',
            'p1.name AS project_name',
            'p1.name_eng AS project_name_eng',
            'p1.slug AS project_slug',
            'p1.slug_eng AS project_slug_eng',
            'p1.address AS project_address',
            'p1.address_eng AS project_address_eng',
            'b1.id AS block_id',
            'b1.name AS block_name',
            'b1.name_eng AS block_name_eng',
            'b1.slug AS block_slug',
            'b1.slug_eng AS block_slug_eng'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
        $b->leftJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
        $b->leftJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

        $b->andWhere('a1.status = :status:', array('status' => \ITECH\Data\Lib\Constant::APARTMENT_STATUS_ACTIVE));
        $b->andWhere('p1.id = :projectID:', array('projectID' => $projectId));

        $result = $b->getQuery()->execute();

        return $result;
    }
}
