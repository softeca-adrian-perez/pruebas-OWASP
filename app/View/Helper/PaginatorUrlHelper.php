<?php

App::uses('Helper', 'View');
App::uses('PaginatorHelper', 'View/Helper');

abstract class PaginatorUrlHelper extends PaginatorHelper {
	public function link($title, $url = array(), $options = array()) {
		$options += array('model' => null, 'escape' => true);
		$model = $options['model'];
		unset($options['model']);

		if (!empty($this->options)) {
			$options += $this->options;
		}
		if (isset($options['url'])) {
			$url = array_merge((array)$options['url'], (array)$url);
			unset($options['url']);
		}
		unset($options['convertKeys']);

		$url = $this->url($url, true, $model);

		$obj = isset($options['update']) ? $this->_ajaxHelperClass : 'Html';

        unset($url[0]); // remove possible url param
        $url = array_merge($url, $this->setUrlParams()); // called in the subclass

		return $this->{$obj}->link($title, $url, $options);
	}
}

