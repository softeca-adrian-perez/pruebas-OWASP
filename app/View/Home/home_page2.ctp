<?php
echo $this->Html->script('lib/owlcarousel/owl.carousel.min.js', array('block' => 'script'));
echo $this->Html->script('owlcarousel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/owlcarousel/assets/owl.carousel.min.css',array('block' => 'script'));
echo $this->Html->css('../js/lib/owlcarousel/assets/owl.theme.default.min.css',array('block' => 'script'));
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
            __t('Communication.Communications'),
        ));
        ?>
    </div>
</div>
<?php
echo $this->element('../Home/Elements/home_bullets');
?>
<div class="cnt-data fg-0">
    <div class="header-articles">
        <?php echo $this->element('../Home/Elements/home_header'); ?>
        <?php echo $this->element('../Elements/Comun/article_searcher'); ?>
    </div>
</div>

<div class="cnt-page-communications">
    <?php echo $this->element('../Home/Elements/communications');?>
</div>

<div style="display: none;" id="myModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-popup="<?php echo count($pop_ups); ?>" >
    <div id="myModal_view">
        <?php
        echo $this->requestAction(
            array(
                'controller' => 'home',
                'action' => 'pop_ups',
            ),
            array('return')
        );
        ?>
    </div>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>