<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Menu.Fleets'),
                array(
                    'controller' => 'fleets',
                    'action' => 'home'
                )
            ),
            __t('Menu.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Fleet.Add'),
                array(
                    'controller' => 'fleets',
                    'action' => 'add',
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green',
                )
            );
        }
        ?>
    </div>
</div>

<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Menu.Fleets') ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Fleet.name', __t('AagService.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.address', __t('Garage.Address')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.postcode', __t('Venue.Post_code')); ?></th>
                    <th><?php echo $this->Paginator->sort('Country.name', __t('Menu.Country')); ?></th>
                    <th><?php echo $this->Paginator->sort('Province.name', __t('Garage.Province')); ?></th>
                    <th><?php echo $this->Paginator->sort('Erp.erp_code', __t('Garage.ERP')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.ref_code', __t('Garage.Ref_code')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.payment_terms', __t('Fleet.Payment_terms')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.tax_code', __t('Fleet.Tax_code')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.company_code', __t('Fleet.Company_code')); ?></th>
                    <th><?php echo $this->Paginator->sort('Fleet.company_name', __t('Fleet.Company_name')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Fleet.active', __t('Fleet.Status')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fleets as $fleet) { ?>
                    <tr>
                        <td>
                        <?php
                        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                            echo $this->Html->link(
                                $fleet['Fleet']['name'],
                                array(
                                    'controller' => 'fleets',
                                    'action' => 'edit',
                                    $fleet['Fleet']['guid']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        } else {
                            echo $fleet['Fleet']['name'];
                        }
                        ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['address']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['postcode']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Country']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Province']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Erp']['erp_code']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['ref_code']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['payment_terms']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['tax_code']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['company_code']); ?>
                        </td>
                        <td>
                            <?php echo h($fleet['Fleet']['company_name']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $fleet['Fleet']['active'] == ConstantsBooleans::ACTIVE ?
                             __t('Brands.Active') : __t('Training.Inactive'); ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
