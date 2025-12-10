<?php
$enquiryAnswered = $enquiry['Enquiry']['answered'];
echo $this->Form->create("Enquiries.enquiry");

$hasVehicleInfo = !empty($enquiry['Enquiry']['brand']) ||
    !empty($enquiry['Enquiry']['model']) ||
    !empty($enquiry['Enquiry']['version']) ||
    !empty($enquiry['Enquiry']['mot_exp_date']) ||
    !empty($enquiry['Enquiry']['fuel']) ||
    !empty($enquiry['Enquiry']['registered_on']) ||
    !empty($enquiry['Enquiry']['mileage']) ||
    !empty($enquiry['Enquiry']['work_id']);

$user = $this->Acceso->user();
?>
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
            __t('Enquiry.Enquiries'),
        ));
        ?>
    </div>
    <div>
        <?php
        if ($enquiryAnswered) {
            $cancel_action['hide_save'] = $enquiryAnswered;
        }
        echo $this->element(
            'Comun/form_actions',
            $cancel_action
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Enquiry.Answer_enquiry'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'Enquiry.name',
            array(
                'value' => Texto::encryptDecryptText($enquiry['Enquiry']['name'], false),
                'type' => 'text',
                'readonly' => true,
                'placeholder' => '',
                'label' => __t('General.Name'),
            )
        );
        echo $this->Form->input(
            'Enquiry.phone',
            array(
                'value' => Texto::encryptDecryptText($enquiry['Enquiry']['phone'], false),
                'type' => 'text',
                'readonly' => true,
                'placeholder' => '',
                'label' => __t('Contact.Phone'),
            )
        );
        echo $this->Form->input(
            'Enquiry.email',
            array(
                'value' => Texto::encryptDecryptText($enquiry['Enquiry']['email'], false),
                'type' => 'text',
                'readonly' => true,
                'placeholder' => '',
                'label' => __t('Email.Email'),
            )
        ); ?>
    </div>
    <?php
    if ($hasVehicleInfo) { ?>
        <div class="aag-subtitle">
            <?php echo __t('General.Vehicle_info'); ?>
        </div>
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'Enquiry.plate',
                array(
                    'value' => Texto::encryptDecryptText($enquiry['Enquiry']['plate'], false),
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Plate'),
                )
            );
			echo $this->Form->input(
                'Enquiry.vin',
                array(
                    'value' => Texto::encryptDecryptText($enquiry['Enquiry']['vin'], false),
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Vin'),
                )
            );
            echo $this->Form->input(
                'Enquiry.registered_on',
                array(
                    'value' => Fecha::toFormatoVistaFecha($enquiry['Enquiry']['registered_on']),
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Registered_on'),
                )
            );
            echo $this->Form->input(
                'Enquiry.mot_exp_date',
                array(
                    'value' => Fecha::toFormatoVistaFecha($enquiry['Enquiry']['mot_exp_date']),
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Mot_due_on'),
                )
            );
            echo $this->Form->input(
                'Enquiry.brand',
                array(
                    'value' => $enquiry['Enquiry']['brand'],
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('Brands.Brand'),
                )
            );
            echo $this->Form->input(
                'Enquiry.model',
                array(
                    'value' => $enquiry['Enquiry']['model'],
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Model'),
                )
            );
            echo $this->Form->input(
                'Enquiry.version',
                array(
                    'value' => $enquiry['Enquiry']['version'],
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Version'),
                )
            );
            echo $this->Form->input(
                'Enquiry.mileage',
                array(
                    'value' => $enquiry['Enquiry']['mileage'],
                    'readonly' => true,
                    'type' => 'number',
                    'label' => __t('General.Mileage'),
                )
            );
            echo $this->Form->input(
                'Enquiry.fuel',
                array(
                    'value' => $enquiry['Enquiry']['fuel'],
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Fuel'),
                )
            );
            echo $this->Form->input(
                'Enquiry.work_id',
                array(
                    'value' => isset($enquiry['Enquiry']['work_name']) ? $enquiry['Enquiry']['work_name'] : null,
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('General.Work'),
                )
            ); ?>
        </div>
    <?php } ?>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'Enquiry.description',
            array(
                'value' => $enquiry['Enquiry']['description'],
                'disabled' => true,
                'type' => 'textarea',
                'label' => __t('General.Description'),
            )
        );
        echo $this->Form->input(
            'Enquiry.answer',
            array(
                'value' => $enquiry['Enquiry']['answer'],
                'type' => 'textarea',
                'readonly' => $enquiryAnswered,
                'required' => true,
                'label' => __t('Enquiry.Answer'),
            )
        );
        if ($enquiryAnswered) {
            $date = $enquiry['Enquiry']['date_answered'];
            if ($user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                $date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
            }
            echo $this->Form->input(
                'Enquiry.date_answered',
                array(
                    'value' => Fecha::toFormatoVistaFechaHora($date),
                    'readonly' => true,
                    'type' => 'text',
                    'label' => __t('Enquiry.Date_answered'),
                )
            ); ?>
    </div>
<?php } ?>
</div>
<?php echo $this->Form->end(); ?>
