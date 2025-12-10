<?php

App::uses('HtmlHelper', 'View/Helper');

class CustomHtmlHelper extends HtmlHelper {

    public function breadcrumb($items) {
        $count = count($items);
        $li = array();
        for ($i = 0; $i < $count - 1; $i++) {
            $li[] = parent::tag('li', $items[$i]);
        }
        $li[] = parent::tag('li', end($items), array('class' => 'current'));
        return parent::tag('ul', implode("\n", $li), array('class' => 'breadcrumbs'));
    }

}

