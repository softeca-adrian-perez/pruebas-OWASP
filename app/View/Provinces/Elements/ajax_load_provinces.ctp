<?php
$controller = $this->request->controller;
$class = 'input-disabled';
if (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
    $class = '';
}
echo $this->Form->input(
    $province_field_name,
    array(
        'label' => __t('Garage.Province'),
        'class' => 'select2-multiple select-province-js ' . $class,
        'type' => 'select',
        'empty' => true,
        'multiple' => $select_multiple,
        'options' => $provinces_list,
        'selected' => isset($garage['Garage']['province']) ? $garage['Garage']['province'] : null,
        'name' => $province_field_name,
        'data-url' => Router::url(array(
            'controller' => 'cities',
            'action' => 'ajax_load_cities',
        )),
        'data-div_cities' => $div_cities,
        'data-city_field_name' => 'city_id',
        'data-city_selected' => isset($garage['Garage']['city_id']) ? $garage['Garage']['city_id'] : null,
        'data-province-name' => isset($inactive_province) ? json_encode($inactive_province) : null,
        'disabled' => $controller == 'garages' ? true : false,
        'id' => $select_multiple ? 'val-name10' : 'autocomplete-province',
        'data-is_config' => $is_config ?? 0
    )
);
if (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
    echo $this->Form->input(
        $province_field_name,
        array(
            'type' => 'hidden',
            'required' => true,
            'value' => $garage['Garage']['province'] ?? null
        )
    );
}