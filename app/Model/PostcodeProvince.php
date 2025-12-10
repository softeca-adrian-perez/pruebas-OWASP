<?php
class PostcodeProvince extends AppModel
{
    public $useTable = 'postcode_provinces';
    public $displayField = 'postcode';

    public $validate = array(
		'postcode' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'province_code' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
		)
	);

    public function postal_code_provinces()
    {
        //$file_tmp = "/../Config/Schema/Files/postcodes_fr.xlsx";
        $file_tmp = "/../Config/Schema/Files/postcodes_de.xlsx";
        //$file_tmp = "/../Config/Schema/Files/postcodes_uk.xlsx";
        $path = dirname(__FILE__) . $file_tmp;
        App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));

        $reader = PHPExcel_IOFactory::load($path);
        $file = $reader->setActiveSheetIndex(0)->toArray(null, true, true, true);

        unset($file[1]);

        $province = ClassRegistry::init('Province');
        $postal_code_province = ClassRegistry::init('PostcodeProvince');

        foreach ($file as $row) {
            $province_bd = $province->findByProvinceCode($row['C']);

            $postcode = '';
            if($row['B'] != ''){
                $long_tmp = strlen ($row['B']);
                if($long_tmp < ConstantsLengthPostcode::POSTCODE_FR){
                    $diff = ConstantsLengthPostcode::POSTCODE_FR - $long_tmp;
                    for ($i = 1; $i <= $diff; $i++) {
                        $postcode = $postcode . '0';
                    }
                }
                $postcode = $postcode . $row['B'];
            }

            $postal_code_provinces_tmp = array(
                'PostcodeProvince' => array(
                    'postcode' => $postcode,
                    'province_code' => ($row['C'] != '') ? $row['C'] : null,
                    'province_id' => $province_bd['Province']['id'],
                )
            );

            $postal_code_province->create();
            $postal_code_province->save($postal_code_provinces_tmp);
        }

    }

    public function postal_code_uk_provinces()
    {
        $file_tmp = "/../Config/Schema/Files/postcodes_uk.xlsx";

        $path = dirname(__FILE__) . $file_tmp;
        App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));

        $reader = PHPExcel_IOFactory::load($path);
        $file = $reader->setActiveSheetIndex(0)->toArray(null, true, true, true);

        unset($file[1]);

        $province = ClassRegistry::init('Province');
        $postal_code_province = ClassRegistry::init('PostcodeProvince');

        foreach ($file as $row) {
            $province_bd = $province->findByProvinceCode($row['C']);

            if ($row['B'] != '') {
                $postcode = $row['B'];

                $postal_code_provinces_tmp = array(
                    'PostcodeProvince' => array(
                        'postcode' => $postcode,
                        'province_code' => ($row['C'] != '') ? $row['C'] : null,
                        'province_id' => $province_bd['Province']['id'],
                    )
                );

                $postal_code_province->create();
                $postal_code_province->save($postal_code_provinces_tmp);
            }
        }

    }

    /**
     * Search a PostcodeProvince by the given postcode with 4 digits in Benelux region or all digits in UK region.
     */
	public function findPostcodeProvinceByPostcodeInRegion($postcode, $region)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = PostcodeProvince.province_id',
                        ),
                    ),
                    array(
                        'alias' => 'Country',
                        'table' => 'countries',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Country.id = Province.country_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PostcodeProvince.postcode' => $postcode,
                    'Country.aag_region_id' => $region
                ),
                'fields' => array(
                    'PostcodeProvince.id, PostcodeProvince.province_id'
                ),
            )
        );
    }

    public function findProvinceIdByPostcode ($postcode) {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'PostcodeProvince.postcode ' => $postcode,
                ),
                'fields' => 'PostcodeProvince.province_id'
            )
        );
    }

    /**
     * Searcher to get the province id given a postcode and a country prefix.
     * Country prefix matches with the first two characters we store in province_code field.
     */
    public function findProvinceIdByPostcodeAndCountryCode ($postcode, $countryCode) {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'PostcodeProvince.postcode ' => $postcode,
                    'PostcodeProvince.province_code LIKE' => $countryCode . '-%',
                ),
                'fields' => 'PostcodeProvince.province_id'
            )
        );
    }
}
