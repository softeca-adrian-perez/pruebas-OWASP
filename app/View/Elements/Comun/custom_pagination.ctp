<?php
$last_page = ceil($pagination_count / ConstantsPagination::SIZE_PAGE_SMALL);
$start_page = ConstantsPagination::FIRST_PAGE + ConstantsPagination::NUMBER_PAGES;
$end_page = $last_page - ConstantsPagination::NUMBER_PAGES;
if (isset($this->request->named['sort'])) {
    $order_paginator = $this->request->named['sort'];
} else {
    $order_paginator = '';
}
if (isset($this->request->named['direction'])) {
    $direction_paginator = $this->request->named['direction'];
} else {
    $direction_paginator = '';
}
echo $this->Form->create(
    'Paginator',
    array(
        'type' => 'post',
        'url' => array(
            'controller' => 'app',
            'action' => 'paginator_size',
            $this->params['controller'],
            $this->params['action'],
            '?' => $this->request->query
        ),
    )
); ?>
<?php if ($pagination_count / ConstantsPagination::SIZE_PAGE_SMALL > 1) { ?>
    <div class="paginacion">
        <ul>
            <?php if ($pagination_page != ConstantsPagination::FIRST_PAGE) { ?>
                <li>
                    <?php if (!empty($order_paginator)) { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => ConstantsPagination::FIRST_PAGE, '?' => $this->request->query)); ?>"
                            rel="first">&lt;&lt;</a>
                    <?php } else { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => ConstantsPagination::FIRST_PAGE, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"
                            rel="first">&lt;&lt;</a>
                    <?php } ?>
                </li>
                <li>
                    <?php if (!empty($order_paginator)) { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => ($pagination_page - 1), 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"
                            rel="prev">&lt;</a>
                    <?php } else { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => ($pagination_page - 1), '?' => $this->request->query)); ?>"
                            rel="prev">&lt;</a>
                    <?php } ?>
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
                            <?php if (!empty($order_paginator)) { ?>

                                <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                            <?php } else { ?>
                                <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                            <?php } ?>
                        </li>
                    <?php } ?>
                <?php } ?>
                <?php } elseif ($pagination_page >= $start_page && $pagination_page <= $end_page) {
                for ($i = $pagination_page - 4; $i < $pagination_page; $i++) { ?>
                    <li>
                        <?php if (!empty($order_paginator)) { ?>
                            <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                        <?php } else { ?>
                            <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                        <?php } ?>
                    </li>
                <?php } ?>
                <li class="current"><?php echo $pagination_page; ?></li>
                <?php for ($i = $pagination_page + 1; $i <= $pagination_page + 4; $i++) { ?>
                    <li>
                        <?php if (!empty($order_paginator)) { ?>
                            <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                        <?php } else { ?>
                            <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, '?' => $this->request->query,)); ?>"><?php echo $i ?></a>
                        <?php } ?>
                    </li>
                <?php } ?>
            <?php } ?>

            <?php if ($pagination_page < $start_page && $last_page >= 10) {
                for ($i = 1; $i < 10; $i++) {
                    if ($i == $pagination_page) { ?>
                        <li class="current"><?php echo $i ?></li>
                    <?php } else { ?>
                        <li>
                            <?php if (!empty($order_paginator)) { ?>

                                <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                            <?php } else { ?>
                                <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                            <?php } ?>
                        </li>
            <?php }
                }
            } ?>

            <?php if ($pagination_page > $end_page && $last_page >= 10) {
                for ($i = $last_page - 9; $i <= $last_page; $i++) {
                    if ($i == $pagination_page) { ?>
                        <li class="current"><?php echo $i ?></li>
                    <?php } else { ?>
                        <li>
                            <?php if (!empty($order_paginator)) { ?>

                                <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                            <?php } else { ?>
                                <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $i, '?' => $this->request->query)); ?>"><?php echo $i ?></a>
                            <?php } ?>
                        </li>
            <?php }
                }
            } ?>

            <?php if ($pagination_page != $last_page) { ?>
                <li>
                    <?php if (!empty($order_paginator)) { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => ($pagination_page + 1), 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"
                            rel="next">&gt;</a>
                    <?php } else { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => ($pagination_page + 1), '?' => $this->request->query)); ?>"
                            rel="next">&gt;</a>
                    <?php } ?>
                </li>
                <li>
                    <?php if (!empty($order_paginator)) { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $last_page, 'sort' => $order_paginator, 'direction' => $direction_paginator, '?' => $this->request->query)); ?>"
                            id="last_paginator" rel="last">&gt;&gt;</a>
                    <?php } else { ?>
                        <a href="<?php echo Router::url(array('controller' => $this->request->controller, 'action' => $this->request->action, 'page' => $last_page, '?' => $this->request->query)); ?>"
                            id="last_paginator" rel="last">&gt;&gt;</a>
                    <?php } ?>
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
<?php } ?>