<?php
echo $this->Html->script('communications.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));

$params = $this->request->pass;

if (isset($params[2])) {
    echo $this->Form->hidden(
        '',
        array(
            'id' => 'modal-value',
            'value' => $params[2]
        )
    );
}
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'controller' => 'home',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Communication.Communications'),
                array(
                    'controller' => 'home',
                    'action' => 'home_page2'
                )
            ),
            __t('Communication.Communication_section'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            '<span class="ion-ios-home-outline fs-xx-large"></span>',
            array(
                'controller' => 'home',
                'action' => 'home_page2',
            ),
            array(
                'escape' => false,
                'title' => __t('General.Home'),
            )
        );
        ?>
    </div>
</div>

<div class="cnt-data aag-padding">
    <?php
    echo $this->Form->create(
        'Search',
        array(
            'class' => 'cnt-form-search',
            'type' => 'get',
            'url' => array(
                'controller' => 'communications',
                'action' => 'section',
                $section['CommunicationSection']['id']
            ),
        )
    );
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('TradingGroup.Trading_groups'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php echo $this->Form->input(
            'title',
            array(
                'label' => false,
                'placeholder' => __t('General.Search'),
                'value' => '',
                'type' => 'text',
                'id' => 'search-communications',
                'class' => 'search_ajax clear_field',
                'data-url' => Router::url(
                    array(
                        'controller' => 'communications',
                        'action' => 'ajax_section_list',
                        $section['CommunicationSection']['id']
                    )
                ),
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
    <div class="medium-12 columns background-color-primary p-top-1">
        <?php
        $tres = 'tres';
        if (count($params) > 1) {
            $tres = '';
        } ?>
        <div class="medium-2 columns ta-center end link-section button-general m-right-1 <?php echo $tres ?>"
            data-subsection_id="<?php echo ''?>"
            data-url="
        <?php
            echo Router::url(
                array(
                    'controller' => 'communications',
                    'action' => 'ajax_section_list',
                    $section['CommunicationSection']['id'],
                )
            );
            ?>
        ">
            <?php
            echo __t('Communication.See_all');
            ?>
        </div>
        <?php
        foreach ($subsections as $subsection) {
            $tres = '';
            if (isset($params[1]) && $subsection['SectionSubsection']['id'] == $params[1]) {
                $tres = 'tres';
            } ?>
            <div class="medium-2 columns ta-center end link-section button-general <?php echo $tres ?>"
                data-subsection_id="<?php echo $subsection['SectionSubsection']['id'] ?>"
                data-url="
        <?php
                echo Router::url(
                    array(
                        'controller' => 'communications',
                        'action' => 'ajax_section_list',
                        $subsection['SectionSubsection']['communication_section_id'],
                        $subsection['SectionSubsection']['id']
                    )
                );
                ?>
        ">
                <?php
                echo $subsection['SectionSubsection']['name' . __s()]
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="medium-12 columns background-color-primary ">
        <div class="row p-top-1" id="ajax_search_communications"
            data-title="<?php echo $title ?>"
            data-id="<?php echo $section['CommunicationSection']['id'] ?>"
            data-url="<?php echo Router::url(
                array(
                    'controller' => 'communications',
                    'action' => 'ajax_section_list'
                )
            ); ?>">
        </div>
    </div>
