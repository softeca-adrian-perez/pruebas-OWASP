<?php
echo $this->Html->script('product_orders_form.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Order',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'order-form-id'
    )
);

echo $this->Form->hidden('Order.id');
echo $this->Form->hidden('Order.garage_id');
echo $this->Form->hidden('Order.order_type_id', array('id' => 'order_type_id'));
$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Order.Orders'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_orders',
                        $garage_id
                    )
                ),
                __t('Order.New_order'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Order.Orders'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_orders',
                        $garage_id
                    )
                ),
                CakeSession::read('Auth.User.edit_enabled') ? __t('General.Edit') : __t('General.View'),
            ));
        }
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php echo $this->element(
            'Comun/form_actions_garage',
            $cancel_action
        ); ?>
        <?php if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
            <div class="f-right">
                <button type="button" id="edit-btn-disable" value="1" class="aag-button medium"
                    data-url="<?php echo Router::url(
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'ajax_update_edit',
                                    )
                                ); ?>"
                    data-edit="<?php echo __t('General.Edit'); ?>"
                    data-view="<?php echo __t('General.View'); ?>"
                    data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>">
                    <?php echo __t('General.Edit'); ?>
                </button>
            </div>
        <?php } ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title m-bottom-1">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Order.New_order');
        } else {
            echo __t('General.Order') . ' ' . $orders['Order']['order_number'];
        }
        ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'order_number',
            array(
                'type' => 'text',
                'label' => __t('Order.Order_number'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'invoice_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js from-js input-disabled',
                'id' => 'invoice_date',
                'data-to' => '#end_date',
                'label' => __t('Order.Invoice_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'order_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js from-js input-disabled',
                'id' => 'order_date',
                'data-to' => '#end_date',
                'label' => __t('Order.Order_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'completed_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js from-js input-disabled',
                'id' => 'completed_date',
                'data-to' => '#end_date',
                'label' => __t('Order.Completed_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'invoce_number',
            array(
                'type' => 'text',
                'label' => __t('Order.Invoce_number'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
                'required' => false,
            )
        );
        echo $this->Form->input(
            'invoice_amount',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Order.Invoice_amount') . ' ' . $country['Country']['symbol'] : __t('Order.Invoice_amount'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'notes',
            array(
                'type' => 'text',
                'label' => __t('Order.Notes'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'order_type_id',
            array(
                'label' => __t('General.Order_type'),
                'type' => 'select',
                'class' => 'select2-multiple input-disabled',
                'options' => $order_types,
                'empty' => true,
                'multiple' => false,
                'disabled' => $this->request->action == 'add' ? false : true,
                'id' => 'order_type',
                'data-url' => Router::url(array(
                    'controller' => 'orders',
                    'action' => 'ajax_get_products',
                )),
            )
        );
        ?>
    </div>

    <div class="medium-12 columns ta-right">
        <?php
        if (isset($garage_equipment)) {
            echo $this->Html->link(
                __t('General.Delete') . '<span class="ion-trash-b c-fallo"></span>',
                array(),
                array(
                    'escape' => false,
                    'title' => __t('General.Delete'),
                    'class' => 'button-general cuatro conImg swal-msg',
                    'data-confirmmsg' => __t('Equipment.Equipment_delete?'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                    'data-type' => 'warning',
                    'data-url' => Router::url(array(
                        'controller' => 'garages_equipments',
                        'action' => 'delete',
                        $garage_equipment['GarageEquipment']['garage_id'],
                        $garage_equipment['GarageEquipment']['id'],
                    )),
                )
            );
        }
        ?>
    </div>
    <br />
    <hr style="background: var(--container-elements-color); border: none; min-height: 2px; min-width: 100%; clear: both;" />
    <br />
    <div class="btn-hide" <?php echo $this->request->action == ConstantsActionsNames::EDIT ? 'hidden' : '' ?> ?>
        <div class="cnt-form-inputs required">
            <?php echo $this->Form->input(
                'array_product_id',
                array(
                    'label' => __t('Order.Products'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'id' => 'select-product',
                    'multiple' => false,
                    'empty' => true
                )
            );
            ?>
            <div class="flex" style="flex-direction: row; gap: 29px;">
                <?php
                echo $this->Form->input(
                    'array_quantity',
                    array(
                        'label' => __t('Garage.Quantity'),
                        'id' => 'input-quantity',
                        'type' => 'number',
                        'min' => 1
                    )
                );
                ?>
                <div class="flex" style="justify-content: flex-start; align-items: flex-end; flex-direction: row; padding-bottom: 1px;">
                    <?php
                    echo $this->Html->link(
                        __t('Orders.Add_product'),
                        'javascript:void(0)',
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green',
                            'id' => 'add-product',
                            'data-url_info' => Router::url(array(
                                'controller' => 'orders',
                                'action' => 'ajax_get_info_distributor',
                            )),
                        )
                    ); ?>
                </div>
            </div>
        </div>
        <div class="row btn-hide" hidden>
            <div hidden>
                <?php echo $this->Form->input(
                    'GaragesProducts',
                    array(
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'multiple' => true,
                        'empty' => false,
                        'id' => 'garages-products',
                    )
                );
                ?>
            </div>
            <div hidden>
                <?php echo $this->Form->input(
                    'DeleteGaragesProducts',
                    array(
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'multiple' => true,
                        'empty' => false,
                        'id' => 'garages-products-delete',
                    )
                );
                ?>
            </div>
        </div>
        <div class="o-auto m-top-1">
            <table class="table-tracking tabla-responsive" id="product-table">
                <thead>
                    <tr>
                        <th><?php echo __t('Order.Products') ?></th>
                        <th class="ta-center" width="200"><?php echo __t('Garage.Quantity') ?></th>
                        <th width="125" class="ta-center btn-hide" <?php echo $this->request->action == ConstantsActionsNames::EDIT ? 'hidden' : '' ?> ?><?php echo __t('General.Actions') ?></th>
                    </tr>
                </thead>
                <tbody id="product-tbody">
                    <?php if (!empty($products_array)) {
                        foreach ($products_array as $key => $product) { ?>
                            <tr>
                                <td class="item-product" data-id="<?php echo $product['Product']['id']; ?>" data-product_id="<?php echo $product['Product']['product_id']; ?>">
                                    <?php echo $product['OrderProduct']['name_' . __l()] ?>
                                </td>
                                <td class="ta-center"><?php echo $product['Product']['quantity'] ?></td>
                                <td class="ta-center btn-hide" <?php echo $this->request->action == ConstantsActionsNames::EDIT ? 'hidden' : '' ?> ?>
                                    <span class="aag-icon-papelera c-fallo cursor-pointer delete-garage-product btn-hide" hidden></span>
                                </td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>