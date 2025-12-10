<div>
    <?php
    echo $this->Form->create(
        'Search',
        array(
            'class' => 'search',
            'type' => 'get',
            'url' => array(
                'plugin' => false,
                'controller' => 'communications',
                'action' => 'communication_searcher',
            ),
        )
    );
    ?>
    <div class="flex ai-center gap-1">
        <?php
        echo $this->Form->input(
            'search',
            array(
                'label' => false,
                'div' => false,
                'placeholder' => __t('General.Search'),
                'type' => 'text'
            )
        );
        echo $this->Form->button(__t('General.Search'), array('type' => 'submit', 'class' => 'aag-button small m-0', 'id' => 'search_homepage'));
        ?>
    </div>
    <?php
    echo $this->Form->end();
    ?>
</div>