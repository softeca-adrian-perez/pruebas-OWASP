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
            $this->Html->link(
                __t('Order.Orders'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_orders',
                    $garage_id
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php 
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'garages',
                'action' => 'add_orders',
                $garage_id
            ),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'orders',
                'action' => 'edit',
                $order['Order']['id']
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
                'style' => 'margin-top:0 !important;'
            )
        ); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('General.View'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Order.Order_number') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($order['Order']['order_number']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Order.Invoice_date') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $order['Order']['invoice_date'] ? h($order['Order']['invoice_date']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Order.Order_date') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $order['Order']['order_date'] ? h($order['Order']['order_date']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Order.Completed_date') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $order['Order']['completed_date'] ? h($order['Order']['completed_date']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Order.Invoce_number') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $order['Order']['invoce_number'] ? h($order['Order']['invoce_number']) : ''; ?>
            </div>
        </div>
        <div>
            <strong>
                <?php
                    $msg = isset($country['Country']['symbol']) ? __t('Order.Invoice_amount') . ' ' . $country['Country']['symbol'] : __t('Order.Invoice_amount');
                    echo $msg;
                ?>:
            </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $order['Order']['invoice_amount'] ? h($order['Order']['invoice_amount']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Order.Notes') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $order['Order']['notes'] ? h($order['Order']['notes']) : ''; ?>
            </div>
        </div>
    </div>
    <div class="o-auto p-top-1">
        <table class="table-tracking tabla-responsive" id="product-table">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Campaign_entries') ?></th>
                    <th><?php echo __t('Garage.Quantity') ?></th>
                </tr>
            </thead>
            <tbody id="product-tbody">
                <?php if(!empty($products_array)){ foreach ($products_array as $key => $product) { ?>
                    <tr>
                        <td><?php echo $product['OrderProduct']['name_' . __l()] ?></td>
                        <td><?php echo $product['Product']['quantity'] ?></td>
                    </tr>
                <?php } }?>
            </tbody>
        </table>
    </div>
</div>