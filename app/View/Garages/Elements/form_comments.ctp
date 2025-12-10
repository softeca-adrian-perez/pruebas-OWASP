<?php
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
            __t('Garage.Comments')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) {
            $url = Router::url(
                array(
                    'controller' => 'garages',
                    'action' => 'ajax_update_edit',
                )
            );
        ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo $url; ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'admin')); ?>

<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="p-vertical-1 ">
        <?php echo $this->element('../Garages/Elements/admin_tabs', array('active' => ConstantsLogType::GARAGE)); ?>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Garage.Comments'); ?>
    </div>
    <div class="btn-hide" hidden>
        <div class="cnt-buttons-v2 d-inline f-right">
            <?php echo $this->Html->link(
                __t('Garage.Comments_new'),
                array(
                    'controller' => 'garages_comments',
                    'action' => 'add',
                    $garage_id
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button small green'
                )
            );
            ?>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Username'); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageComment.body', __t('Garage.Body')); ?></th>
                    <th class="ta-center" width="200"><?php echo $this->Paginator->sort('GarageComment.creation_date', __t('Garage.Creation_date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_comments as $comment_tmp) { ?>
                    <tr>
                        <td class="link-text">
                            <?php
                            echo $this->Html->link(
                                isset($comment_tmp['GarageComment']['created_by_name']) ? $comment_tmp['GarageComment']['created_by_name'] : $comment_tmp['User']['name'],
                                array(
                                    'controller' => 'garages_comments',
                                    'action' => 'edit',
                                    $comment_tmp['GarageComment']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            ); ?>
                        </td>
                        <td>
                            <?php
                            $str = h($comment_tmp['GarageComment']['body']);
                            if (strlen($str) > 145) {
                                $str = substr($str, 0, 140) . '...';
                            }
                            echo $str;
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php echo h(date("d-m-Y / H:i", strtotime($comment_tmp['GarageComment']['creation_date']))); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>