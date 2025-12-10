<?php
echo $this->Html->script('venues.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Venue.Venues'),
                array(
                    'controller' => 'venues',
                    'action' => 'home'
                )
            ),
            __t('Venue.Venues_list'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Form->button(
            __t('Venues.Export_venues'),
            array(
                'class' => 'aag-button medium gd-export-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => VENUES_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_venues',
                'data-url_ajax_count' => '/venues/ajax_count_venues',
                'data-url' => Router::url(
                    array(
                        'controller' => 'venues',
                        'action' => 'venue_excel',
                    )
                )
            )
        );
        ?>
        <div style="display: none;" id="modal_send_csv_email_venues" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false">
            <div class="aag-subtitle">
                <?php
                echo sprintf(__t('General.Export_limit_exceeded'), VENUES_EXPORT_LIMIT . ' ' . strtolower(__t('Venue.Venues')));
                ?>
            </div>
            <?php
            $user = $this->Acceso->user();
            echo $this->Form->input(
                'email',
                array(
                    'label' => __t('Contact.Email'),
                    'value' => $user['Contact']['email'],
                    'id' => 'contact-email-js',
                )
            );
            ?>
            <div class="ta-right p-top-1">
                <?php
                echo $this->Form->button(
                    __t('General.Send'),
                    array(
                        'id' => 'send-data-export-js',
                        'class' => 'aag-button medium green send-data-export-js',
                        'type' => 'submit',
                        'escape' => false,
                        'data-url_ajax' => '/venues/ajax_get_csv_data',
                        'data-url' => Router::url(
                            array(
                                'controller' => 'app',
                                'action' => 'ajax_verify_email',
                            )
                        ),
                    )
                );
                ?>
            </div>
            <a class="close-modal" data-close aria-label="Close">&#215;</a>
        </div>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Venue.New_venue'),
                array(
                    'controller' => 'venues',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Venues/Elements/search_venue'); ?>
    <div class="o-auto">
        <table id="venues_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Venue.name', __t('Venue.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.address_1', __t('Venue.Address_1')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.address_2', __t('Venue.Address_2')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.town', __t('Garage.Town') . '/' . __t('Garage.City')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.post_code', __t('Venue.Post_code')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.telephone', __t('Venue.Telephone')); ?></th>
                    <th><?php echo $this->Paginator->sort('VenueType.name_' . __l() , __t('Venue.Venue_type')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.active', __t('General.Active')); ?></th>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue) { ?>
                    <tr>
                        <td>
                            <?php
                                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                    '<span class="c-primary">' . h($venue['Venue']['name']) . '</span>',
                                    array(
                                        'controller' => 'venues',
                                        'action' => 'edit',
                                        $venue['Venue']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                    );
                                }else {
                                    echo h($venue['Venue']['name']);
                                }
                            ?>
                        </td>
                        <td>
                            <?php echo h($venue['Venue']['address_1']); ?>
                        </td>
                        <td>
                            <?php echo h($venue['Venue']['address_2']); ?>
                        </td>
                        <td>
                            <?php echo h($venue['Venue']['town']); ?>
                        </td>
                        <td>
                            <?php echo h($venue['Venue']['post_code']); ?>
                        </td>
                        <td>
                            <?php echo h($venue['Venue']['telephone']); ?>
                        </td>
                        <td>
                            <?php echo h($venue['VenueType']['name_' . __l()]); ?>
                        </td>
                        <td>
                            <?php echo Booleano::toString($venue['Venue']['active']); ?>
                        </td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                            <td class="ta-center">
                                <span class="aag-icon-papelera c-fallo delete-venue" style="cursor: pointer;" data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'venues',
                                            'action' => 'ajax_delete',
                                            $venue['Venue']['id'],
                                        )
                                    ); ?>
                                    ">
                                </span>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>