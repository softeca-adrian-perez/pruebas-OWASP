<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'search',
        'type' => 'get',
        'url' => array(
            'controller' => 'communications',
            'action' => 'communication_searcher',
        ),
    )
);

$page_1 = '';
if ($active_page == ConstantsActiveHomePage::PAGE_1) {
    $page_1 = 'active';
}
$page_2 = '';
if ($active_page == ConstantsActiveHomePage::PAGE_2) {
    $page_2 = 'active';
}
$page_3 = '';
if ($active_page == ConstantsActiveHomePage::PAGE_3) {
    $page_3 = 'active';
}
$page_4 = '';
if ($active_page == ConstantsActiveHomePage::PAGE_4) {
    $page_4 = 'active';
}
$page_5 = '';
if ($active_page == ConstantsActiveHomePage::PAGE_5) {
    $page_5 = 'active';
}
$page_6 = '';
if ($active_page == ConstantsActiveHomePage::PAGE_6) {
    $page_6 = 'active';
}

if ($page_1 != 'active' && $page_5 != 'active' && $page_6 != 'active' && (isset($day_week) || isset($date))) {
?>
    <div class="cnt-date">
        <span class="aag-icon-calendario"></span>
        <strong>
            <?php echo h($day_week); ?>
        </strong>
        <div>
            <?php echo h($date); ?>
        </div>
    </div>
<?php
}
if (isset($communication_section_list)) {
?>
    <div class="columns medium-3">
        <?php echo $this->Form->input(
            'section',
            array(
                'label' => 'Go to:  ' . __t('Communication.Communication_section'),
                'type' => 'select',
                'class' => 'select2-multiple select-section-js clear_field',
                'options' => $communication_sections_top,
                'empty' => true,
                'id' => 'section-id',
                'data-url' => Router::url(array(
                    'controller' => 'communications',
                    'action' => 'section',
                )),
            )
        ); ?>
    </div>
    <div class="columns medium-3">
        <?php echo $this->Form->input(
            'param',
            array(
                'label' => false,
                'placeholder' => __t('General.Search'),
                'type' => 'text',
                'id' => 'search-page2',
                'class' => 'clear_field'
            )
        ); ?>
    </div>
    <div class="medium-2 columns end cnt-buttons-search">
        <?php
        echo $this->Form->button(
            "<span class='icon-search'></span>",
            array(
                'type' => 'submit',
                'class' => 'button-search',
                'escape' => false,
                'title' =>  __t('General.Search')
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
        ); ?>
    </div>
<?php
}
echo $this->Form->end();
?>