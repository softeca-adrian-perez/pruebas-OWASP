<?php
class GroupingGenart extends AppModel
{
    public $useTable = 'grouping_genarts';

    public function getAllByAagRegionId($aagRegionId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Network.id = GroupingGenart.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'GroupingGenart.*',
                ),
            )
        );
    }
}