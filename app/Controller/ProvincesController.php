<?php

class ProvincesController extends AppController
{
    public $uses = array(
        'Province',
        'Country'
    );

    /**
     * AJAX load provinces.
     */
    public function ajax_load_provinces()
    {
        $this->verify_ajax($this->request);

        $countryId = $this->request->data['country_id'];
        $provinceFieldName = $this->request->data['province_field_name'];
        $isConfig = $this->request->data['is_config'];

        $provinces = $this->Province->getProvincesByCountry($countryId);

        $countries = $this->Country->find('list');
        $countCountries = count($countries);

        $this->set(array(
            'provinces_list' => $provinces,
            'province_field_name' => $provinceFieldName,
            'count_countries' => $countCountries,
            'div_cities' => $isConfig ? ".div_cities" : "#div_cities",
            'select_multiple' => $isConfig,
            'is_config' => $isConfig
        ));
        $this->layout = null;
        $this->render('../Provinces/Elements/ajax_load_provinces');
    }
}
