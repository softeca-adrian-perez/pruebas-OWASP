<?php
$controller = $this->request->controller;
$class = 'input-disabled';
if (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
    $class = '';
}
echo $this->Form->input(
    $city_field_name,
    array(
        'label' => __t('General.Seo_city'),
        'class' => 'select2-multiple ' . $class,
        'type' => 'select',
        'empty' => true,
        'multiple' => $select_multiple,
        'options' => $cities_list,
        'name' => $city_field_name,
        'disabled' => $controller == 'garages' ? true : false,
        'id' => $select_multiple ? 'val-name11' : 'autocomplete-city',
    )
);
