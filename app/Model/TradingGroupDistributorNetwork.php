<?php

class TradingGroupDistributorNetwork extends AppModel
{
    public $useTable = 'trading_groups_distributors_networks';

    public function new_trading_groups_networks($trading_groups_network)
    {
        $fields = array(
            'TradingGroupDistributorNetwork' => array(
                'trading_group_id',
                'network_id',
            )
        );
        $this->create();
        if (!$this->guardar($trading_groups_network, $fields)) {
            return false;
        }
        return true;
    }

    public function removeTradingGroupDistributorNetwork($netowrk_id)
    {
        $trading_group_networks = $this->findAllByNetworkId($netowrk_id);

        foreach ($trading_group_networks as $trading_group_network) {
            $this->delete($trading_group_network['TradingGroupDistributorNetwork']['id']);
        }
        $this->commit();
    }

    public function getNetworksByTradingGroup($trading_group_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'TradingGroupDistributorNetwork.trading_group_id' => $trading_group_id,
                ),
                'fields' => array(
                    'TradingGroupDistributorNetwork.network_id'
                ),
            )
        );
    }

    public function getTradingGroupsByRegionAndNetwork($aag_region_id, $network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'trading_groups',
                        'alias' => 'TradingGroup',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = TradingGroupDistributorNetwork.trading_group_id',
                            'TradingGroup.aag_region_id' => $aag_region_id,
                        ),
                    )
                ),
                'conditions' => array(
                    'TradingGroupDistributorNetwork.network_id' => $network_id,
                ),
                'fields' => array(
                    'TradingGroupDistributorNetwork.network_id',
                    'TradingGroupDistributorNetwork.trading_group_id',
                ),
                'group' => array(
                    'group' => 'TradingGroupDistributorNetwork.trading_group_id',
                )
            )
        );
    }

    public function getListTradingGroupsByRegionAndNetwork($aag_region_id, $network_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'trading_groups',
                        'alias' => 'TradingGroup',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.trading_group_id = TradingGroup.id',
                            'TradingGroup.aag_region_id' => $aag_region_id,
                        ),
                    )
                ),
                'conditions' => array(
                    'TradingGroupDistributorNetwork.network_id' => $network_id,
                ),
                'fields' => array(
                    'TradingGroup.id',
                    'TradingGroup.name'
                )
            )
        );
    }
}
