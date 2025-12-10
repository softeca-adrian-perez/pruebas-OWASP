<?php
echo $this->Html->script('products_orders.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            __t('Garage.Orders'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit',)); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } else { ?>
            <div class="is_superAdmin-js"></div>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'orders')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Order.Orders'); ?>
    </div>
    <div class="btn-hide" hidden>
        <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php
                echo $this->Html->link(
                    __t('Order.New_order'),
                    array(
                        'controller' => 'orders',
                        'action' => 'add',
                        $garage_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                    )
                );
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking" id="table-orders">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo __t('General.Order_type'); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.order_number', __t('Order.Order_number')); ?></th>
                    <th><?php echo __t('Order.Product'); ?></th>
                    <th><?php echo __t('Order.Quantity'); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.invoice_date', __t('Order.Invoice_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.order_date', __t('Order.Order_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.completed_date', __t('Order.Completed_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.invoce_number', __t('Order.Invoce_number')); ?></th>
                    <th>
                        <?php
                        $msg = isset($country['Country']['symbol']) ? __t('Order.Invoice_amount') . ' ' . $country['Country']['symbol'] : __t('Order.Invoice_amount');
                        echo $this->Paginator->sort('Order.invoice_amount', $msg);
                        ?>
                    </th>
                    <th><?php echo $this->Paginator->sort('Order.order_number', __t('Order.Notes')); ?></th>
                    <th class="ta-center btn-hide" hidden><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order_tmp) { ?>
                    <tr class="tr-show" data-identificator="<?php echo $order_tmp['Order']['id'] ?>" id="<?php echo $order_tmp['Order']['id'] ?>" data-delete="<?php echo Router::url(array('controller' => 'orders', 'action' => 'ajax_delete_products')); ?>" data-url="<?php echo Router::url(array('controller' => 'orders', 'action' => 'ajax_charge_products', $order_tmp['Order']['id'])); ?>">
                        <td>
                            <div class="ampliar-js cursor-pointer">
                                <div class="mostrar-ampliado">
                                    <?php if(isset($order_tmp['Product']['id'])){?>
                                            <div class="flecha abajo not-disabled"></div>
                                    <?php
                                        } else {
                                    ?>
                                            <div class="flecha abajo disabled"></div>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php echo !empty($order_tmp['Order']['order_type_id']) ? $order_types_list[$order_tmp['Order']['order_type_id']] : ''; ?>
                        </td>
                        <td>
                            <?php echo $order_tmp['Order']['order_number']; ?>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($order_tmp['Order']['invoice_date'])); ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($order_tmp['Order']['order_date'])); ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($order_tmp['Order']['completed_date'])); ?>
                        </td>
                        <td>
                            <?php echo $order_tmp['Order']['invoce_number']; ?>
                        </td>
                        <td>
                            <?php echo $order_tmp['Order']['invoice_amount']; ?>
                        </td>
                        <td>
                            <?php echo $order_tmp['Order']['notes']; ?>
                        </td>
                        <td class="ta-center btn-hide" hidden>
                            <?php
                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                echo $this->Html->link(
                                    '<span class="cursor-pointer edit-provider aag-icon-editar"></span>',
                                    array(
                                        'controller' => 'orders',
                                        'action' => 'edit',
                                        $order_tmp['Order']['id']
                                    ),
                                    array(
                                        'class' => 'flex',
                                        'escape' => false,
                                    )
                                );
                            }

                            if (isset($order_tmp)) {
                                echo $this->Html->link(
                                    '<span class="aag-icon-papelera c-fallo cursor-pointer"></span>',
                                    array(),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.Delete'),
                                        'class' => 'swal-msg',
                                        'data-confirmmsg' => __t('Order.Order_delete?'),
                                        'data-yes' => __t('General.Yes'),
                                        'data-no' => __t('General.No'),
                                        'data-type' => 'warning',
                                        'data-url' => Router::url(array(
                                            'controller' => 'orders',
                                            'action' => 'delete',
                                            $order_tmp['Order']['garage_id'],
                                            $order_tmp['Order']['id'],
                                        )),
                                    )
                                );
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->element('Comun/paginacion'); ?>