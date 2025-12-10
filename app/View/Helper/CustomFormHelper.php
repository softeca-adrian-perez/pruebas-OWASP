<?php

App::uses('FormHelper', 'View/Helper');

class CustomFormHelper extends FormHelper {

    public function create($model = null, $options = array()) {
        if (is_array($model) && empty($options)) {
            $options = $model;
            $model = null;
        }

        if (!isset($options['autocomplete'])) {
            $options['autocomplete'] = 'off';
        }

        if (!isset($options['novalidate'])) {
            $options['novalidate'] = true;
        }
        return parent::create($model, $options);
    }
}