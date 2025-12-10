    <div class="o-auto p-top-1">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Visit.name', __t('Visit.Route_name'));?></th>
                <th><?php echo $this->Paginator->sort('Visit.type', __t('Visit.Route_type'));?></th>
                <th><?php echo $this->Paginator->sort('Visit.creation_date', __t('General.Creation_date'));?></th>
                <th class="ta-center"><?php echo __t('Visit.Number_customers');?></th>
                <th class="ta-center"><?php echo __t('General.Actions');?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($routes as $route) { ?>
                <tr>
                    <td class="c-defecto">
                        <?php echo h($route['Route']['name']); ?>
                    </td>
                    <td class="c-defecto">
                        <?php echo h($route_options[$route['Route']['type']]); ?>
                    </td>
                    <td class="c-defecto">
                        <?php echo Fecha::toFormatoVistaFecha($route['Route']['creation_date']); ?>
                    </td>
                    <td class="ta-center">
                        <?php echo h($route['Route']['customer_number']); ?>
                    </td>
                    <td class="ta-center">
                        <!-- <div class="columns medium-5 ta-center">
                            <?php if($route['Route']['type'] == ConstantsVisitType::GARAGE){
                                echo $this->Html->link(
                                    '<span class="ion-ios-arrow-thin-right"></span>',
                                    array(
                                        'controller' => 'visits',
                                        'action' => 'home',
                                        $route['Route']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            } else if($route['Route']['type'] == ConstantsVisitType::DISTRIBUTOR){
                                echo $this->Html->link(
                                    '<span class="ion-ios-arrow-thin-right"></span>',
                                    array(
                                        'controller' => 'visits',
                                        'action' => 'home_distributor',
                                        $route['Route']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            } ?>
                        </div>
                        <div class="columns medium-5 ta-center"> -->
                            <?php echo $this->Html->link(
                                '<span class="aag-icon-papelera c-fallo"></span>',
                                'javascript:void(0)',
                                array(
                                    'escape' => false,
                                    'class' => 'delete-route-js',
                                    'data-url' => Router::url(
                                        array(
                                            'controller' => 'routes',
                                            'action' => 'ajax_delete_route',
                                        )
                                    ),
                                    'data-id' => $route['Route']['id'],
                                    'data-type' => $route['Route']['type'],
                                    'data-confirmmsg' => __t('Visit.Delete_route'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No')
                                )
                            ); ?>
                        <!-- </div> -->
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <?php echo $this->element('Comun/paginacion'); ?>
