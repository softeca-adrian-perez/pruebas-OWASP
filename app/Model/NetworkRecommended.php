<?php

class NetworkRecommended extends AppModel
{
    public $useTable = 'networks_recommended';

    public function createNetworkRecommended(
        $network_id,
        $internal_network_id,
        $recommended,
        $recommended_label = 0,
        $image_recommended = null,
        $image_recommended_list = null
    ) {
        $fields = array(
            'NetworkRecommended' => array(
                'network_id' => $network_id,
                'internal_network_id' => $internal_network_id,
                'recommended' => $recommended,
                'recommended_label' => $recommended_label,
                'image_recommended' => $image_recommended,
                'image_recommended_list' => $image_recommended_list,
                'creation_date' => date('Y-m-d H:i:s'),
                'modification_date' => date('Y-m-d H:i:s')
            )
        );

        $this->create();
        $result = $this->save($fields);

        if ($result) {
            return $result['NetworkRecommended'];
        } else {
            return false;
        }
    }

    public function getRecommendedStatus($original_network_id, $internal_network_id)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'network_id' => $original_network_id,
                'internal_network_id' => $internal_network_id
            ),
            'fields' => array('recommended')
        ));

        if ($result) {
            return $result['NetworkRecommended']['recommended'];
        } else {
            return 0;
        }
    }

    public function getRecommendedLabel($original_network_id, $internal_network_id)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'network_id' => $original_network_id,
                'internal_network_id' => $internal_network_id
            ),
            'fields' => array('recommended_label')
        ));
        if ($result) {
            return $result['NetworkRecommended']['recommended_label'];
        } else {
            return 0;
        }
    }

    public function deleteImageRecommended($network_recommended_id)
    {
        $network_recommended = $this->findById($network_recommended_id);
        $image_recommended = $network_recommended['NetworkRecommended']['image_recommended'];
        if (isset($image_recommended) && !empty($image_recommended) && FileManager::delete_file(WWW_ROOT, FilePaths::NETWORKS_RECOMMENDED_IMAGES . $image_recommended)) {
            $network_recommended['NetworkRecommended']['image_recommended'] = null;
            $this->save($network_recommended);
            return true;
        } else {
            return false;
        }
    }

    public function deleteImageRecommendedList($network_recommended_id)
    {
        $network_recommended = $this->findById($network_recommended_id);
        $image_recommended_list = $network_recommended['NetworkRecommended']['image_recommended_list'];
        if (isset($image_recommended_list) && !empty($image_recommended_list) && FileManager::delete_file(WWW_ROOT, FilePaths::NETWORKS_RECOMMENDED_IMAGES . $image_recommended_list)) {
            $network_recommended['NetworkRecommended']['image_recommended_list'] = null;
            $this->save($network_recommended);
            return true;
        } else {
            return false;
        }
    }

    public function getInternalRecommendedNetworks($networkId, $internalNetworkId)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id' => $internalNetworkId,
                        'Network.internal' => ConstantsBooleans::YES,
                    ),
                ),
            ),
            'conditions' => array(
                'network_id' => $networkId,
                'internal_network_id' => $internalNetworkId,
                'recommended_label' => ConstantsBooleans::YES
            ),
            'fields' => array(

                'NetworkRecommended.*'
            )
        ));
    }
}
