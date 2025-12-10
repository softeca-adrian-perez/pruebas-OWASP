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
                __t('Maintenance.Website'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_marketing_and_image_garage',
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
                'action' => 'add_marketing_and_image_garage',
                $website['GarageWebsite']['garage_id']
            ),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'garages_websites',
                'action' => 'edit',
                $website['GarageWebsite']['id'],
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
        <?php echo __t('Maintenance.Website_view'); ?>
    </div>

    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Maintenance.Website') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($websites[$website['GarageWebsite']['website_id']]); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Maintenance.URL') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($website['GarageWebsite']['url']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Maintenance.Tagline') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($website['GarageWebsite']['tagline']); ?>
            </div>
        </div>
    </div>
    <div class="row p-vertical-1">
        <div>
            <strong><?php echo __t('Maintenance.Description') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($website['GarageWebsite']['description']); ?>
            </div>
        </div>
    </div>
</div>