<?php
echo $this->Form->input(
    'Communication.section_subsection_id',
    array(
        'label' => __t('Communication.Communication_subsection'),
        'class' => 'select2-multiple',
        'type' => 'select',
        'empty' => true,
        'options' => $sections_subsections,
        'id' => 'section_subsection_id',
        'data-url' => Router::url(array(
            'controller' => 'communications',
            'action' => 'ajax_load_communication_filters',
        )),
    )
);
