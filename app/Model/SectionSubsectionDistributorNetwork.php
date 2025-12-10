<?php

class SectionSubsectionDistributorNetwork extends AppModel{
    public $useTable = 'sections_subsections_distributors_networks';

    public function add( $section_subsection_id , $distributor_network_id ){

        $fields = array(
            'SectionSubsectionDistributorNetwork' => array(
                'section_subsection_id',
                'distributor_network_id',
            )
        );
        $section_subsection_distributor_network['SectionSubsectionDistributorNetwork']['section_subsection_id'] = $section_subsection_id;
        $section_subsection_distributor_network['SectionSubsectionDistributorNetwork']['distributor_network_id'] = $distributor_network_id;

        $this->create();
        if(!$this->guardar( $section_subsection_distributor_network, $fields )){
            return false;
        }

        return true;
    }

    public function getSectionsSubsectionsDistributorsNetworksBySectionSubsectionId( $section_subsection_id ){
        return $this->find('list',
        array(
            'joins' => array(
                array(
                    'alias' => 'DistributorNetwork',
                    'table' => 'distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorNetwork.id = SectionSubsectionDistributorNetwork.distributor_network_id'
                    )
                )
            ),
            'conditions' => array(
                'section_subsection_id' => $section_subsection_id
            ),
            'fields' => array(
                'DistributorNetwork.id',
                'DistributorNetwork.name'
            )
        )
        );
    }


}