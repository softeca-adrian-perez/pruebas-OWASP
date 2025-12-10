<?php echo $this->element('../Communications/Elements/search_subsections'); ?>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('SectionSubsection.name_en', __t('Communication.English_name')); ?></th>
            <th><?php echo $this->Paginator->sort('SectionSubsection.name_fr', __t('Communication.French_name')); ?></th>
            <th><?php echo $this->Paginator->sort('SectionSubsection.name_de', __t('Communication.German_name')); ?></th>
            <th><?php echo __t('Communication.Communication_section'); ?></th>
            <th><?php echo __t('Section.Image'); ?></th>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
            <?php } ?>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($section_subsections as $section_subsection) { ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $section_subsection['SectionSubsection']['name_en'],
                        array(
                            'controller' => 'communications',
                            'action' => 'edit_subsection',
                            $section_subsection['SectionSubsection']['id']
                        ),
                        array(
                            'class' => 'c-primary'
                        )
                    ); ?>
                </td>
                <td>
                    <?php echo h($section_subsection['SectionSubsection']['name_fr']); ?>
                </td>
                <td>
                    <?php echo h($section_subsection['SectionSubsection']['name_de']); ?>
                </td>
                <td>
                    <?php echo h($sections[$section_subsection['SectionSubsection']['communication_section_id']]); ?>
                </td>
                <td>
                    <?php
                        if( $section_subsection['SectionSubsection']['image'] ){
                            echo $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $section_subsection['SectionSubsection']['image']),array('class' => 'img-communication-min',) );
                        }
                    ?>
                </td>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <td class="ta-center">
                        <?php echo $this->Html->link(
                            '<span class="aag-icon-papelera c-fallo"></span>',
                            array(),
                            array(
                                'escape' => false,
                                'class' => 'delete-communication_subsections-js',
                                'data-confirmmsg' => __t('Communication.Confirm_delete'),
                                'data-yes' => __t('General.Yes'),
                                'data-no' => __t('General.No'),
                                'data-subsection_id' => $section_subsection['SectionSubsection']['id'],
                                'data-url_delete' => Router::url(array(
                                    'controller' => 'communications',
                                    'action' => 'ajax_delete_subsection',
                                )),
                                'data-url_redirect' => Router::url(array(
                                    'controller' => 'communications',
                                    'action' => 'maintenance_section_subsection',
                                ))
                            )
                        ); ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php echo $this->element('Comun/paginacion'); ?>