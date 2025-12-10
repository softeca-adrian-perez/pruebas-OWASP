<?php $config = CakeSession::read('Auth.User.Config'); ?>
<div class="menu_garage aag-tabs">
    <ul>
        <li <?php if( $selected == 'datas_distributor'){ echo 'class="active"'; }?>>
            <?php
            // If exist garage id, is called to edit, otherwise, is called to add
            if (isset($distributor_id)) {
                echo $this->Html->link(
                    __t('Distributor.Distributor'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'edit',
                        $distributor_id,
                    )
                );
            }
            else{
                echo $this->Html->link(
                    __t('Distributor.Distributor'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add',
                    )
                );
            }
            ?>
        </li>
        <?php if ( isset( $distributor_id ) ) { ?>
            <li <?php if( $selected == 'network_distributor'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Network.Networks'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_networks_distributor',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'opening_distributor'){ echo 'class="active"'; }?>>
                <?php
                echo $this->Html->link(
                    __t('Garage.Opening_times'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_opening_distributor',
                        $distributor_id,
                    )
                );
                ?>
            </li>
            <li <?php if( $selected == 'activity_distributor'){ echo 'class="active"'; }?>>
                <?php
                echo $this->Html->link(
                    __t('Distributor.Activity'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_activity',
                        $distributor_id,
                    )
                );
                ?>
            </li>
            <?php if( $config[ConstantsConfig::AAG_SERVICES] ){ ?>
                <li <?php if( $selected == 'services_distributor'){ echo 'class="active"'; }?>>
                    <?php
                    echo $this->Html->link(
                        __t('Distributor.Aag_services'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_services',
                            $distributor_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if( $config[ConstantsConfig::LABEL] ){ ?>
                <li <?php if( $selected == 'label_distributor'){ echo 'class="active"'; }?>>
                    <?php
                    echo $this->Html->link(
                        __t('Label.Label'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_label',
                            $distributor_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <li <?php if( $selected == 'software_distributor'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Distributor.Software'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_software',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'contract_distributor'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Distributor.Contracts'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_contract',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'image_distributor'){ echo 'class="active"'; }?>>
                <?php
                echo $this->Html->link(
                    __t('Distributor.Images'),
                    array(
                        'controller' => 'distributors_images',
                        'action' => 'add_image_distributor',
                        $distributor_id,
                    )
                );
                ?>
            </li>
            <li <?php if( $selected == 'contacts_general_branch_manager'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Contact.General_branch_manager'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_contacts_general_branch_manager',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'contact_distributor_bdm'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Contact.BDM'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_contacts_bdm',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'contact_garage_staff'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Contact.Staff'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_contacts_staff',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'notes_distributor'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Distributor.Comments'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_comments',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'associated'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('Distributor.Garages_associated'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'home_associated',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
            <li <?php if( $selected == 'logs_changes'){ echo 'class="active"'; }?>>
                <?php
                echo $this->Html->link(
                    __t('Logs.Logs_changes'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home_logs_changes',
                        $distributor_id,
                    )
                );
                ?>
            </li>
            <li <?php if( $selected == 'requested_changes'){ echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('RequestedChanges.Requested_changes'),
                        array(
                            'controller' => 'distributors',
                            'action' => 'requested_changes',
                            $distributor_id,
                        )
                    );
                ?>
            </li>
        <?php } ?>
    </ul>
</div>