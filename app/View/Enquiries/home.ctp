<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('GarageNetwork.Garage_network'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'view',
                    $garage_network_id
                )
            ),
            __t('Enquiry.Enquiries')
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_my_garage')); ?>
<div class="cnt-data">
    <div class="aag-padding">
        <?php echo $this->element('../GaragesNetworks/tabs_my_garage', array('selected' => 'enquiries')); ?>
    </div>
    <?php echo $this->element('../Enquiries/Elements/search'); ?>
    <div class="o-auto m-top-1">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('General.Name'); ?></th>
                    <?php if ($has_child_networks) { ?>
                        <th><?php echo $this->Paginator->sort('Enquiry.child_network_id', __t('ChildNetworks.secondary_networks')); ?></th>
                    <?php } ?>
                    <th><?php echo $this->Paginator->sort('Enquiry.description', __t('General.Description')); ?></th>
                    <th><?php echo $this->Paginator->sort('Enquiry.answered', __t('Enquiry.Answered')); ?></th>
                    <th><?php echo $this->Paginator->sort('Enquiry.creation_date', __t('General.Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Enquiry.network_name', __t('Network.Network')); ?></th>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enquiries as $enquiry) : ?>
                    <tr>
                        <td><?php echo Texto::encryptDecryptText($enquiry['Enquiry']['name'], false); ?></td>
                        <?php if ($has_child_networks) { ?>
                            <td>
                                <?php if ($has_child_networks && isset($enquiry['Enquiry']['child_network_id'])) {
                                    echo $child_networks[$enquiry['Enquiry']['child_network_id']];
                                } ?>
                            </td>
                        <?php } ?>
                        <td><?php echo $enquiry['Enquiry']['description']; ?></td>
                        <td><?php echo $enquiry['Enquiry']['answered'] ? __t('General.Yes') : __t('General.No'); ?></td>
                        <td><?php echo Fecha::toFormatoVista($enquiry['Enquiry']['creation_date']); ?></td>
                        <td><?php echo $enquiry['Enquiry']['network_name']; ?></td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                            <td>
                                <?php
                                echo $this->HTML->image(
                                    "edit.png",
                                    array("url" => "/enquiries/edit_enquiry/" . $garage_network_id . "/" . $enquiry["Enquiry"]["id"])
                                );
                                ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>