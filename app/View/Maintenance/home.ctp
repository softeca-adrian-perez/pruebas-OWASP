<?php echo $this->Html->script('maintenance.js', array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php echo __t('Maintenance.Maintenance'); ?>
    </div>
    <div class="cnt-data-element">
        <div class="cnt-enlaces-mantenimiento">
            <?php
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE_CUSTOMERS) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    '<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><polyline points="112 160 48 224 112 288" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M64,224H358c58.76,0,106,49.33,106,108v20" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>' .
                        __t('Maintenance.Customers') . '<span class="icon-Recurso-21"></span>',
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion btn-folder maintenance_folder_closed-js',
                        'data-section' => ConstantsMaintenance::CUSTOMER
                    )
                );
            }
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE_CRM) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    '<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><polyline points="112 160 48 224 112 288" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M64,224H358c58.76,0,106,49.33,106,108v20" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>' .
                        __t('Menu.CRM') . '<span class="icon-crm1"></span>',
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion btn-folder maintenance_folder_closed-js',
                        'data-section' => ConstantsMaintenance::CRM
                    )
                );
            }
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE_CONTACT_USER) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    '<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><polyline points="112 160 48 224 112 288" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M64,224H358c58.76,0,106,49.33,106,108v20" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>' .
                        __t('Maintenance.Contact_user') . '<span class="icon-Recurso-6"></span>',
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion btn-folder maintenance_folder_closed-js',
                        'data-section' => ConstantsMaintenance::CONTACT_USER
                    )
                );
            }
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    '<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><polyline points="112 160 48 224 112 288" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M64,224H358c58.76,0,106,49.33,106,108v20" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>' .
                        __t('Maintenance.Shortcuts') . '<span class="icon-Recurso-26"></span>',
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion btn-folder maintenance_folder_closed-js',
                        'data-section' => ConstantsMaintenance::SHORTCUTS
                    )
                );
            }
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    '<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><polyline points="112 160 48 224 112 288" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M64,224H358c58.76,0,106,49.33,106,108v20" style="fill:none;stroke:var(--tertiary-color);stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>' .
                        __t('Maintenance.Communications') . '<span class="icon-articles"></span>',
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion btn-folder maintenance_folder_closed-js',
                        'data-section' => ConstantsMaintenance::COMMUNICATIONS
                    )
                );
            }
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::REGIONS) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    __t('Maintenance.Regions') . '<span class="icon-Recurso-23 cursor-pointer"></span>',
                    array(
                        'controller' => 'regions',
                        'action' => 'maintenance_regions',
                    ),
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion',
                        'data-section' => ConstantsMaintenance::REGIONS
                    )
                );
            }
            if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION) ||
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
            ) {
                echo $this->Html->link(
                    __t('Maintenance.Configuration') . '<span class="icon-settings cursor-pointer"></span>',
                    array(
                        'controller' => 'config',
                        'action' => 'home',
                    ),
                    array(
                        'escape' => false,
                        'class' => 'boton-seccion',
                        'data-section' => ConstantsMaintenance::CONFIGURATION
                    )
                );
            }
            if (
                CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
                (
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING) ||
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                )
            ) {
                echo $this->Html->link(
                    __t('Maintenance.Credits') . '<span class="ion-ios-calculator cursor-pointer"></span>',
                    array(
                        'controller' => 'trainings_credits',
                        'action' => 'home',
                    ),
                    array(
                        'escape' => false,
                        'class' => 'd-inline-block boton-seccion btn-folder',
                    )
                );
            }
            if (
                SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
                (
                    (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN && CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK) ||
                    (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN && CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::BENELUX)
                )
            ) {
                echo $this->Html->link(
                    __t('Email.Emails') . '<span class="aag-icon-correo-aviso cursor-pointer"></span>',
                    array(
                        'controller' => 'emails',
                        'action' => 'maintenance',
                        ConstantsPlatform::GNM
                    ),
                    array(
                        'escape' => false,
                        'class' => 'd-inline-block boton-seccion btn-folder',
                    )
                );
            }
            ?>
            <div class="show-folder" data-section="<?php echo ConstantsMaintenance::CUSTOMER; ?>">
                <div>
                    <?php
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SERVICES) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Services') . '<span class="icon-Recurso-25 cursor-pointer"></span>',
                            array(
                                'controller' => 'services',
                                'action' => 'home',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CUSTOMER
                            )
                        );
                    }
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLES) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Vehicles') . '<span class="icon-Recurso-12 cursor-pointer"></span>',
                            array(
                                'controller' => 'vehicles',
                                'action' => 'home',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CUSTOMER
                            )
                        );
                    }
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLE_TYPES) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Vehicle_types') . '<span class="icon-Recurso-11 cursor-pointer"></span>',
                            array(
                                'controller' => 'vehicle_types',
                                'action' => 'home',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CUSTOMER
                            )
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="show-folder" data-section="<?php echo ConstantsMaintenance::CONTACT_USER; ?>">
                <div>
                    <?php
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Positions') . '<span class="icon-Recurso-21 cursor-pointer"></span>',
                            array(
                                'controller' => 'positions',
                                'action' => 'home',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CONTACT_USER
                            )
                        );
                    }
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Permissions') . '<span class="icon-Recurso-18 cursor-pointer"></span>',
                            array(
                                'controller' => 'groups_permissions',
                                'action' => 'home',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CONTACT_USER,
                            )
                        );
                    }
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Permissions_by_user') . '<span class="icon-Recurso-19 cursor-pointer"></span>',
                            array(
                                'controller' => 'permissions',
                                'action' => 'home',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CONTACT_USER,
                            )
                        );
                    }
                    if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::REASSIGNMENT)) {
                        echo $this->Html->link(
                            __t('Maintenance.Reassignment') . '<span class="icon-Recurso-8 cursor-pointer"></span>',
                            array(
                                'controller' => 'users',
                                'action' => 'reassignments',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CONTACT_USER,
                            )
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="show-folder" data-section="<?php echo ConstantsMaintenance::SHORTCUTS; ?>">
                <div>
                    <?php
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Shortcuts') . '<span class="icon-Recurso-28 cursor-pointer"></span>',
                            array(
                                'controller' => 'shortcuts',
                                'action' => 'maintenance_shortcuts',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::SHORTCUTS
                            )
                        );
                        echo $this->Html->link(
                            __t('Maintenance.Shortcuts_types') . '<span class="icon-Recurso-27 cursor-pointer"></span>',
                            array(
                                'controller' => 'shortcuts',
                                'action' => 'maintenance_shortcuts_types',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::SHORTCUTS
                            )
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="show-folder" data-section="<?php echo ConstantsMaintenance::COMMUNICATIONS; ?>">
                <div>
                    <?php
                    if (
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                        (
                            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                        )
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Communications') . '<span class="icon-articles cursor-pointer"></span>',
                            array(
                                'controller' => 'communications',
                                'action' => 'maintenance_communications',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::COMMUNICATIONS
                            )
                        );
                        echo $this->Html->link(
                            __t('Maintenance.Communications_sections') . '<span class="icon-categories cursor-pointer"></span>',
                            array(
                                'controller' => 'communications',
                                'action' => 'maintenance_communications_sections',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::COMMUNICATIONS
                            )
                        );
                        echo $this->Html->link(
                            __t('Maintenance.Communications_subsections') . '<span class="icon-Recurso-9 cursor-pointer"></span>',
                            array(
                                'controller' => 'communications',
                                'action' => 'maintenance_section_subsection',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::COMMUNICATIONS
                            )
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="show-folder" data-section="<?php echo ConstantsMaintenance::CRM; ?>">
                <div>
                    <?php
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::TASKS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Task.Tasks') . '<span class="icon-Recurso-30 cursor-pointer"></span>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'maintenance_tasks',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CRM
                            )
                        );
                    }
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::TOPICS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Task.Topics') . '<span class="icon-Recurso-31 cursor-pointer"></span>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'maintenance_topics',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CRM
                            )
                        );
                    }
                    if (
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EVENTS) ||
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                    ) {
                        echo $this->Html->link(
                            __t('Maintenance.Event_type') . '<span class="icon-Recurso-31 cursor-pointer"></span>',
                            array(
                                'controller' => 'appointments',
                                'action' => 'maintenance_home_events',
                            ),
                            array(
                                'escape' => false,
                                'class' => 'd-inline-block boton-seccion btn-item',
                                'data-section' => ConstantsMaintenance::CRM
                            )
                        );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>