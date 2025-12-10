<?php

class SectionSubsectionPosition extends AppModel{
    public $useTable = 'sections_subsections_positions';

    public function add( $section_subsection_id , $position_id ){

        $fields = array(
            'SectionSubsectionPosition' => array(
                'section_subsection_id',
                'position_id',
            )
        );
        $section_subsection_position['SectionSubsectionPosition']['section_subsection_id'] = $section_subsection_id;
        $section_subsection_position['SectionSubsectionPosition']['position_id'] = $position_id;

        $this->create();
        if(!$this->guardar( $section_subsection_position, $fields )){
            return false;
        }

        return true;
    }

    public function getPositionsBySectionSubsectionId( $section_subsection_id ){
        return $this->find('all',
            array(
                'conditions' => array(
                    'section_subsection_id' => $section_subsection_id
                ),
                'fields' => array(
                    'SectionSubsectionPosition.*',
                )
            )
        );
    }

    public function getListPositionsBySectionSubsectionId( $section_subsection_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'section_subsection_id' => $section_subsection_id
                ),
                'fields' => array(
                    'SectionSubsectionPosition.id',
                    'SectionSubsectionPosition.position_id',
                )
            )
        );
    }

}