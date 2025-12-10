<?php
// params needed: $pagination_size, $pagination_count

$pagination_page = isset($this->request->named['page']) ? $this->request->named['page'] : 1;

$pagination_url_array = array(
    'controller' => $this->request->controller, 
    'action' => $this->request->action, 
    '?' => $this->request->query
);
$pagination_url_array = array_merge($pagination_url_array, $this->request->pass);

$last_page = ceil($pagination_count / $pagination_size);
$start_page = ConstantsPagination::FIRST_PAGE + ConstantsPagination::NUMBER_PAGES;
$end_page = $last_page - ConstantsPagination::NUMBER_PAGES;
?>
<div class="row">
    <div class="columns">
        <div class="paginacion">
            <ul style="display: inline-flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                <?php if ($pagination_page != ConstantsPagination::FIRST_PAGE) { ?>
                    <li>
                        <a href="<?php echo Router::url($pagination_url_array); ?>"
                            rel="first">&lt;&lt;</a>
                    </li>
                    <li>
                        <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => ($pagination_page - 1)))); ?>"
                            rel="prev">&lt;</a>
                    </li>
                <?php } else { ?>
                    <li class="prev">&lt;</li>
                <?php } ?>

                <?php if ($last_page < 10) {
                    for ($i = 1; $i <= $last_page; $i++) {
                        if ($i == $pagination_page) { ?>
                            <li class="current"><?php echo $i ?></li>
                        <?php } else { ?>
                            <li>
                                <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => $i))); ?>"><?php echo $i ?></a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                <?php } else if ($pagination_page >= $start_page && $pagination_page <= $end_page) {
                    for ($i = $pagination_page - 4; $i < $pagination_page; $i++) { ?>
                        <li>
                            <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => $i))); ?>"><?php echo $i ?></a>
                        </li>
                    <?php } ?>
                    <li class="current"><?php echo $pagination_page; ?></li>
                    <?php for ($i = $pagination_page + 1; $i <= $pagination_page + 4; $i++) { ?>
                        <li>
                            <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => $i))); ?>"><?php echo $i ?></a>
                        </li>
                    <?php } ?>
                <?php } ?>

                <?php if ($pagination_page < $start_page && $last_page >= 10) {
                    for ($i = 1; $i < 10; $i++) {
                        if ($i == $pagination_page) { ?>
                            <li class="current"><?php echo $i ?></li>
                        <?php } else { ?>
                            <li>
                                <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => $i))); ?>"><?php echo $i ?></a>
                            </li>
                        <?php }
                    }
                } ?>

                <?php if ($pagination_page > $end_page && $last_page >= 10) {
                    for ($i = $last_page - 9 ; $i <= $last_page; $i++) {
                        if ($i == $pagination_page) { ?>
                            <li class="current"><?php echo $i ?></li>
                        <?php } else { ?>
                            <li>
                                <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => $i))); ?>"><?php echo $i ?></a>
                            </li>
                        <?php }
                    }
                } ?>

                <?php if ($pagination_page != $last_page) { ?>
                    <li>
                        <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => ($pagination_page + 1)))); ?>"
                            rel="next">&gt;</a>
                    </li>
                    <li>
                        <a href="<?php echo Router::url(array_merge($pagination_url_array, array('page' => $last_page))); ?>"
                            id="last_paginator" rel="last">&gt;&gt;</a>
                    </li>
                <?php } else { ?>
                    <li class="next">&gt;</li>
                <?php } ?>

                <li>
                    <span><?php echo __t('General.Results'); ?>: </span>
                    <strong id="total_paginator"><?php echo $pagination_count; ?></strong>
                </li>
            </ul>
        </div>
    </div>
    <div class="columns medium-4 large-3 ta-right change_rows f-right">
    </div>
</div>
