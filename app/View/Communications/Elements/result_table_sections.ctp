<?php echo $this->element('../Communications/Elements/search_sections'); ?>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('CommunicationSection.name_en', __t('Communication.English_name')); ?></th>
            <th><?php echo $this->Paginator->sort('CommunicationSection.name_fr', __t('Communication.French_name')); ?></th>
            <th><?php echo $this->Paginator->sort('CommunicationSection.name_de', __t('Communication.German_name')); ?></th>
            <th><?php echo $this->Paginator->sort('CommunicationSection.scrolling', __t('Section.Scrolling')); ?></th>
            <th><?php echo $this->Paginator->sort('CommunicationSection.visual', __t('Section.Visual')); ?></th>
            <th><?php echo __t('Section.Image'); ?></th>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
            <?php } ?>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($communications_section as $communication_section) { ?>
            <tr>
                <td>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                    echo $this->Html->link(
                        $communication_section['CommunicationSection']['name_en'],
                        array(
                            'controller' => 'communications',
                            'action' => 'edit_section',
                            $communication_section['CommunicationSection']['id']
                        ),
                        array(
                            'class' => 'c-primary'
                        )
                    );} else {
                        echo h($communication_section['CommunicationSection']['name_en']);
                    } ?>
                </td>
                <td>
                    <?php echo h($communication_section['CommunicationSection']['name_fr']); ?>
                </td>
                <td>
                    <?php echo h($communication_section['CommunicationSection']['name_de']); ?>
                </td>
                <td>
                    <?php if($communication_section['CommunicationSection']['scrolling']){
                        echo __t('General.Yes');
                    } else {
                        echo __t('General.No');
                    }?>
                </td>
                <td>
                    <?php if($communication_section['CommunicationSection']['visual']){
                        echo __t('General.Yes');
                    } else {
                        echo __t('General.No');
                    }?>
                </td>
                <td>
                    <?php
                    if( $communication_section['CommunicationSection']['image'] ){
                        echo $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication_section['CommunicationSection']['image']),array('class' => 'img-communication-min',) );
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
                                'class' => 'delete-communication_sections-js',
                                'data-confirmmsg' => __t('Communication.Confirm_delete'),
                                'data-yes' => __t('General.Yes'),
                                'data-no' => __t('General.No'),
                                'data-section_id' => $communication_section['CommunicationSection']['id'],
                                'data-url_delete' => Router::url(array(
                                    'controller' => 'communications',
                                    'action' => 'ajax_delete_section',
                                )),
                                'data-url_redirect' => Router::url(array(
                                    'controller' => 'communications',
                                    'action' => 'maintenance_communications_sections',
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