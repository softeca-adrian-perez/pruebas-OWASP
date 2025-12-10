<?php

class SectionSubsectionNetwork extends AppModel{
    public $useTable = 'sections_subsections_networks';

    public function add( $section_subsection_id , $network_id ){

        $fields = array(
            'SectionSubsectionNetwork' => array(
                'section_subsection_id',
                'network_id',
            )
        );
        $section_subsection_network['SectionSubsectionNetwork']['section_subsection_id'] = $section_subsection_id;
        $section_subsection_network['SectionSubsectionNetwork']['network_id'] = $network_id;

        $this->create();
        if(!$this->guardar( $section_subsection_network, $fields )){
            return false;
        }

        return true;
    }

    public function getSectionsSubsectionsNetworksBySectionSubsectionId( $section_subsection_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = SectionSubsectionNetwork.network_id'
                        )
                    )
                ),
                'conditions' => array(
                    'section_subsection_id' => $section_subsection_id
                ),
                'fields' => array(
                    'SectionSubsectionNetwork.network_id',
                    'Network.name'
                )
            )
        );
    }


}