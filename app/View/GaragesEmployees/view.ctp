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
                __t('Employee.Employees'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_employee_garage',
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
            CakeSession::read('url_referer'),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );

        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'garages_employees',
                'action' => 'edit',
                $garage_employees['GarageEmployee']['id']
            ),
            array(
                'escape' => false,
                'title' => __t('General.Edit'),
                'class' => 'aag-button medium',
                'style' => 'margin-top:0 !important;'
            )
        );
        ?>
        </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Employee.Employees_view'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Employee.Employee_type')?>: </strong><br />
            <?php echo $garage_employees['GarageEmployee']['employee_type_id'] ? h($employee_types[$garage_employees['GarageEmployee']['employee_type_id']]) : '';?>
        </div>
        <div>
            <strong><?php echo __t('Employee.Number')?>: </strong><br />
            <?php echo h($garage_employees['GarageEmployee']['number']); ?>
        </div>
    </div>
    <div class="ta-right right-0 cnt-buttons-v2">
        <?php         
            echo $this->Html->link(
            "<span class='aag-icon-papelera'></span>".__t('General.Delete'),
            array(),
            array(
                'escape' => false,
                'title' => __t('General.Delete'),
                'class' => 'aag-button medium red outlined swal-msg',
                'data-confirmmsg' => __t('Employee.Employees_delete?'),
                'data-yes' => __t('General.Yes'),
                'data-no' => __t('General.No'),
                'data-type' => 'warning',
                'data-url' => Router::url(array(
                    'controller' => 'garages_employees',
                    'action' => 'delete',
                    $garage_employees['GarageEmployee']['garage_id'],
                    $garage_employees['GarageEmployee']['id'],
                )),
            )
        );
        ?>
    </div>
</div>