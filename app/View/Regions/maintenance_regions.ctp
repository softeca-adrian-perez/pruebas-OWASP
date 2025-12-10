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
            __t('Maintenance.Regions'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<div class="cnt-data">
    <div class="aag-title cnt-data-element p-top-1">
        <?php echo __t('Maintenance.Regions'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Region.name', __t('Region.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Region.code', __t('Region.Code')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regions as $region) { ?>
                    <tr>
                        <td>
                            <?php
                            //check if user is superAdmin
                            if ($user['role_id'] == ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    $region['Region']['name'],
                                    array(
                                        'controller' => 'regions',
                                        'action' => 'edit_region',
                                        $region['Region']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo $region['Region']['name'];
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo h($region['Region']['code']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>