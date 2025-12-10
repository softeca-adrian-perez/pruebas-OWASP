<div class="columns medium-6" data-equalizer-watch>
    <div class="aag-title">
        <?php echo h($distributor['Distributor']['name']).' - '.h($distributor['Distributor']['account_number']); ?>
    </div>
</div>
<div class="columns medium-6 ta-right right-0" data-equalizer-watch>
    <?php echo $this->Html->link(
        "<span class='ion-android-textsms'></span> " . __t('CRM.About'),
        array(
            'controller' => 'distributors',
            'action' => 'view',
            $distributor['Distributor']['id']
        ),
        array(
            'class' => 'old_new_link ',
            'escape' => false,
        )
    ); ?>
</div>
