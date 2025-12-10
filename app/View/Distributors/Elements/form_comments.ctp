<?php
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                $distributor['Distributor']['name'],
                array(
                    'controller' => 'distributors',
                    'action' => $action,
                    $distributor['Distributor']['id']
                )
            ),
            __t('Distributor.Add_comments'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors',
                'action' => 'home_associated',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium two'
            )
        ); ?>
    </div>
</div>
<?php
echo $this->element('../Distributors/tabs', array('selected' => 'notes_distributor',));
echo $this->Form->create('Distributor', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'));
echo $this->Form->hidden('Distributor.id');
?>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title m-bottom-1">
            <?php echo __t('Distributor.Comments'); ?>
            <?php
            echo $this->Html->link(
                __t('Distributor.Comments_new'),
                array(
                    'controller' => 'distributors_comments',
                    'action' => 'add',
                    $distributor_id
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green',
                )
            );
            ?>
        </div>
    </div>
    <div class="cnt-data-element m-top-1">
        <div class="o-auto p-top-1">
            <table class="table-tracking tabla-responsive">
                <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.name', __t('Distributor.Username')); ?></th>
                        <th><?php echo $this->Paginator->sort('DistributorComment.body', __t('Distributor.Body')); ?></th>
                        <th width="150" class="ta-center"><?php echo $this->Paginator->sort('DistributorComment.creation_date', __t('Distributor.Creation_date')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distributor_comments as $comment_tmp) { ?>
                        <tr>
                            <td>
                                <?php echo $this->Html->link(
                                    $comment_tmp['User']['name'],
                                    array(
                                        'controller' => 'distributors_comments',
                                        'action' => 'view',
                                        $comment_tmp['DistributorComment']['distributor_id'],
                                        $comment_tmp['DistributorComment']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                ); ?>
                            </td>
                            <td>
                                <?php
                                $str = h($comment_tmp['DistributorComment']['body']);
                                if (strlen($str) > 250) {
                                    $str = substr($str, 0, 245) . '...';
                                }
                                echo $str;
                                ?>
                            </td>
                            <td class="ta-center">
                                <?php echo Fecha::toFormatoVistaFecha(h($comment_tmp['DistributorComment']['creation_date'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <br>
        <?php echo $this->element('Comun/paginacion'); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>