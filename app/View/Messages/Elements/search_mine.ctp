<?php

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'style' => 'margin: 0px',
        'type' => 'get',
        'url' => array(
            'controller' => 'messages',
            'action' => 'home',

        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Message.Recieved_messages');?>
    </div>
    <div class="cnt-form-inputs">
        <?php echo $this->Form->input(
            'subject',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Message.Subject'),
            )
        );
        echo $this->Form->input(
            'body',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Message.Body'),
            )
        );
        echo $this->Form->input(
            'sent-from',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'required' => true,
                'id' => 'sent-from',
                'data-to' => '#sent-to',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Message.Sent_date') .' '. strtolower(__t('General.From')),
            )
        );
        echo $this->Form->input(
            'sent-to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'id' => 'sent-to',
                'data-from' => '#sent-from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Message.Sent_date') .' '. strtolower(__t('General.To')),
            )
        ); ?>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('General.Search'),
            array(
                'type' => 'submit',
                'class' => 'aag-button medium',
            )
        );
        echo $this->Form->button(
            "<span class='aag-icon-escoba'></span>",
            array(
                'id' => 'clear_field',
                'class' => 'aag-button medium four outlined',
                'escape' => false,
                'title' => __t('General.Clean_search')
            )
        );
        ?>
    </div>
<?php echo $this->Form->end(); ?>