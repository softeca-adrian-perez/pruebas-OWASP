<?php
class Supplier extends AppModel
{
    public $useTable = 'suppliers';
    public $order = 'Supplier.name';

    public $hasMany = array(
        'Brand',
        'SupplierFile',
        'SupplierNetwork',
        'SupplierTradingGroup'
    );

    public $hasOne = array(
        'SupplierImage',
        'AagRegion'
    );

    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_name',
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'publication_date' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_publication_date',
            ),
            array(
                'rule' => 'date',
                'dmy',
                'message' => 'Validation.Format_date',
                'allowEmpty' => false
            ),
        ),
        'aag_region_id' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_region',
        ),
        'code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'web' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_website',
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'url_video' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'phone' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'fax' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'email' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_email',
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'town' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'postcode' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address1' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_an_address',
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'VAT_number' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'siret' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['web'])) {
            $conditions[] = $this->_conditionWeb($fields['web']);
        }
        if (!empty($fields['brand'])) {
            $conditions[] = $this->_conditionBrand($fields['brand']);
        }
        if (isset($fields['active'])) {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegion($fields['aag_region_id']);
        }
        return $conditions;
    }

    public function _conditionName($name)
    {
        return array('Supplier.name LIKE' => '%' . $name . '%');
    }

    public function _conditionWeb($web)
    {
        return array('Supplier.web LIKE' => '%' . $web . '%');
    }

    public function _conditionBrand($brand_id)
    {
        return array('Brand.id' => $brand_id);
    }

    private function _conditionActive($active)
    {
        //If you get a 2, recharge as is.
        if ($active == '1' or $active == '0') {
            return array('Supplier.active' => $active);
        }
    }

    public function _conditionAagRegion($aag_region_id)
    {
        return array('Supplier.aag_region_id' => $aag_region_id);
    }

    private $_queries = array(
        'supplier' => array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
                array(
                    'alias' => 'Brand',
                    'table' => 'brands',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = Brand.supplier_id',
                    ),
                ),
            ),
            'group' => array(
                'Supplier.id'
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
                'Brand.*',
            ),
            'order' => 'Supplier.name asc'
        )
    );

    public function query($index)
    {
        return $this->_queries[$index];
    }

    public function add($supplier)
    {
        $fields = array(
            'Supplier' => array(
                'name',
                'code',
                'VAT_number',
                'siret',
                'email',
                'phone',
                'fax',
                'web',
                'town',
                'postcode',
                'address1',
                'address2',
                'description',
                'url_video',
                'url_channel',
                'active',
                'creation_date',
                'edition_date',
                'publication_date',
                'aag_region_id'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        if (!empty($supplier['Supplier']['web']) && substr($supplier['Supplier']['web'], 0, 7) !== "http://" && substr($supplier['Supplier']['web'], 0, 8) !== "https://") {
            $supplier['Supplier']['web'] = 'http://' . $supplier['Supplier']['web'];
        }

        $supplier['Supplier']['creation_date'] = $dateTime;
        $supplier['Supplier']['edition_date'] = $dateTime;
        $supplier['Supplier']['publication_date'] = Fecha::toFormatoBd($supplier['Supplier']['publication_date']);

        $this->create();
        if ($tmp = $this->save($supplier, true, $fields)) {
            return $tmp;
        }
        return false;
    }

    public function edit($supplier)
    {
        $fields = array(
            'Supplier' => array(
                'name',
                'code',
                'VAT_number',
                'siret',
                'email',
                'phone',
                'fax',
                'web',
                'town',
                'postcode',
                'address1',
                'address2',
                'description',
                'url_video',
                'url_channel',
                'active',
                'edition_date',
                'publication_date',
                'aag_region_id'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        if (!empty($supplier['Supplier']['web']) && substr($supplier['Supplier']['web'], 0, 7) !== "http://" && substr($supplier['Supplier']['web'], 0, 8) !== "https://") {
            $supplier['Supplier']['web'] = 'http://' . $supplier['Supplier']['web'];
        }
        $supplier['Supplier']['edition_date'] = $dateTime;
        $supplier['Supplier']['publication_date'] = Fecha::toFormatoBd($supplier['Supplier']['publication_date']);

        if ($tmp = $this->save($supplier, true, $fields)) {
            return $tmp;
        }
        return false;
    }

    public function getAllAboutSuppliers()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
            ),
            'conditions' => array(
                'Supplier.active' => ConstantsBooleans::ACTIVE,
                'Supplier.publication_date <=' => date('Y-m-d'),
            ),
        ));
    }

    public function getAllAboutSuppliersConditions($aag_region_id)
    {

        $conditions = array(
            'Supplier.active' => ConstantsBooleans::ACTIVE,
            'Supplier.publication_date <=' => date('Y-m-d')
        );

        $conditions = array('Supplier.aag_region_id' => $aag_region_id);

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
            ),
            'conditions' => $conditions
        ));
    }

    public function getAllAboutSuppliersByNetwork($network_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
                array(
                    'alias' => 'SupplierNetwork',
                    'table' => 'suppliers_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierNetwork.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
            ),
            'conditions' => array(
                'Supplier.active' => ConstantsBooleans::ACTIVE,
                'Supplier.publication_date <=' => date('Y-m-d'),
                'SupplierNetwork.network_id' => $network_id,
            ),
        ));
    }

    public function getAllAboutSuppliersByTradingGroup($trading_group_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
                array(
                    'alias' => 'SupplierTradingGroup',
                    'table' => 'suppliers_trading_groups',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierTradingGroup.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
            ),
            'conditions' => array(
                'Supplier.active' => ConstantsBooleans::ACTIVE,
                'Supplier.publication_date <=' => date('Y-m-d'),
                'SupplierTradingGroup.trading_group_id' => $trading_group_id,
            ),
        ));
    }

    public function getAllAboutSupplierId($supplier_id, $aagRegionId)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
                array(
                    'alias' => 'Brand',
                    'table' => 'brands',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = Brand.supplier_id',
                    ),
                ),
				array(
                    'alias' => 'GarageSoftware',
                    'table' => 'garages_software',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = GarageSoftware.supplier_id',
                    ),
                ),
				array(
                    'alias' => 'DistributorSoftware',
                    'table' => 'distributors_software',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = DistributorSoftware.supplier_id',
                    ),
                ),
				array(
                    'alias' => 'GarageEquipment',
                    'table' => 'garages_equipments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = GarageEquipment.supplier_id',
                    ),
                ),
				array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = GarageNetwork.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
                'Brand.*',
				'GarageSoftware.id',
				'DistributorSoftware.id',
				'GarageEquipment.id',
				'GarageNetwork.id',
            ),
            'conditions' => array(
                'Supplier.id' => $supplier_id,
                'Supplier.aag_region_id' => $aagRegionId,
            ),
        ));
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function search_list_conditions($aag_region_id)
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'conditions' => array(
                'Supplier.aag_region_id' => $aag_region_id,
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function createSupplierFR($supplier_json)
    { // New supplier comes from FRANCE JSON
        $clear_characters = array("+", "(", ")", " ", ".");
        $supplier_tmp = array(
            'Supplier' => array(
                'id' => ($supplier_json['id_isa']) ? $supplier_json['id_isa'] : null,
                'code' => isset($supplier_json['code']) && $supplier_json['code']['code_aag'] ? $supplier_json['code']['code_aag'] : null,
                'name' => isset($supplier_json['identite']['raison_sociale']) ? $supplier_json['identite']['raison_sociale'] : '--',
                'web' => isset($supplier_json['identite']['url']) ? $supplier_json['identite']['url'] : null,
                'VAT_number' => isset($supplier_json['identite']['TVA']) ? str_replace($clear_characters, '', $supplier_json['identite']['TVA']) : null,
                'siret' => isset($supplier_json['identite']['siret']) ? str_replace($clear_characters, '', $supplier_json['identite']['siret']) : null,
                'email' => isset($supplier_json['identite']['email']) ? $supplier_json['identite']['email'] : null,
                'phone' => isset($supplier_json['identite']['telephone']) && is_numeric(str_replace($clear_characters, '', $supplier_json['identite']['telephone'])) ? str_replace($clear_characters, '', $supplier_json['identite']['telephone']) : null,
                'fax' => isset($supplier_json['identite']['fax']) && is_numeric(str_replace($clear_characters, '', $supplier_json['identite']['fax'])) ? str_replace($clear_characters, '', $supplier_json['identite']['fax']) : null,
                'town' => isset($supplier_json['identite']['ville']) ? $supplier_json['identite']['ville'] : null,
                'postcode' => isset($supplier_json['identite']['code_postal']) ? $supplier_json['identite']['code_postal'] : null,
                'address1' => isset($supplier_json['identite']['adresse1']) ? $supplier_json['identite']['adresse1'] : null,
                'address2' => isset($supplier_json['identite']['adresse2']) ? $supplier_json['identite']['adresse2'] : null,
                'description' => '--',
                'url_video' => null,
                'url_channel' => null,
                'active' => ($supplier_json['actif']) ? $supplier_json['actif'] : ConstantsBooleans::NO_ACTIVE,
                'creation_date' => date('Y-m-d H:i:s'),
                'edition_date' => date('Y-m-d H:i:s'),
                'publication_date' => date('Y-m-d H:i:s'),
            )
        );

        if (isset($supplier_json['identite']['url']) && !empty($supplier_json['identite']['url']) && substr($supplier_json['identite']['url'], 0, 7) !== "http://" && substr($supplier_json['identite']['url'], 0, 8) !== "https://") {
            $supplier_tmp['Supplier']['web'] = 'http://' . $supplier_json['identite']['url'];
        }

        $this->create();
        $this->validator()->remove('publication_date');
        $supplier_bd = $this->save($supplier_tmp);
        if (!$supplier_bd) {
            CakeLog::write('updates-france', 'The supplier with ID ISA ' . $supplier_json['id_isa'] . ' could not be created.' . PHP_EOL);
        } else {

            if (isset($supplier_json['divers']) && isset($supplier_json['divers']['logo'])) {
                $url = $supplier_json['divers']['logo'];
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if (file_put_contents(WWW_ROOT . FilePaths::SUPPLIERS_IMAGES_RELATIVE . $file, $source)) {
                    $fields = array(
                        'SupplierImage' => array(
                            'supplier_id',
                            'creation_date',
                            'file',
                            'type',
                            'ext',
                            'source_name'
                        )
                    );

                    $dateTime = date('Y-m-d H:i:s');

                    $image['SupplierImage']['supplier_id'] = $supplier_bd['Supplier']['id'];
                    $image['SupplierImage']['creation_date'] = $dateTime;

                    $image_name = $file;
                    $image['SupplierImage']['file'] = $file;
                    $image['SupplierImage']['type'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierImage']['source_name'] = $image_name;

                    $this->SupplierImage = ClassRegistry::init('SupplierImage');
                    $this->SupplierImage->Behaviors->disable('Attachment');
                    $this->SupplierImage->Behaviors->disable('FileValidation');
                    $this->SupplierImage->create();
                    if (!$this->SupplierImage->save($image, true, $fields)) {
                        CakeLog::write('updates-france', 'The logo supplier could not be updated.' . PHP_EOL);
                    }
                }
            }

            $this->SupplierTradingGroup = ClassRegistry::init('SupplierTradingGroup');
            if (isset($supplier_json['precisium']) && $supplier_json['precisium']) {
                $fields = array(
                    'SupplierTradingGroup' => array(
                        'supplier_id',
                        'trading_group_id',
                    )
                );

                $supplier_trading_group['SupplierTradingGroup']['supplier_id'] = $supplier_bd['Supplier']['id'];
                $supplier_trading_group['SupplierTradingGroup']['trading_group_id'] = 3; // Precisium

                $this->SupplierTradingGroup->create();
                if (!$this->SupplierTradingGroup->save($supplier_trading_group, $fields)) {
                    CakeLog::write('updates-france', 'The Supplier-Trading group could not be created.' . PHP_EOL);
                }
            }
            if (isset($supplier_json['gefa']) && $supplier_json['gefa']) {
                $fields = array(
                    'SupplierTradingGroup' => array(
                        'supplier_id',
                        'trading_group_id',
                    )
                );

                $supplier_trading_group['SupplierTradingGroup']['supplier_id'] = $supplier_bd['Supplier']['id'];
                $supplier_trading_group['SupplierTradingGroup']['trading_group_id'] = 4; // Gef Auto

                $this->SupplierTradingGroup->create();
                if (!$this->SupplierTradingGroup->save($supplier_trading_group, $fields)) {
                    CakeLog::write('updates-france', 'The Supplier-Trading group could not be created.' . PHP_EOL);
                }
            }

            $this->commit();
        }

        return $supplier_bd;
    }

    public function createDocumentsFR($supplier_id, $supplier_documents)
    { // New document supplier comes from FRANCE JSON

        if (isset($supplier_documents['tarif_public'])) { // 1
            foreach ($supplier_documents['tarif_public'] as $document_tarif_public) {
                $url = $document_tarif_public['url'];
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if ($source) {
                    file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                    $fields = array(
                        'SupplierFile' => array(
                            'id',
                            'supplier_id',
                            'supplier_category_id',
                            'creation_date',
                            'file',
                            'type',
                            'ext',
                            'source_name',
                            'name',
                            'active'
                        )
                    );

                    $dateTime = date('Y-m-d H:i:s');
                    $image['SupplierFile']['id'] =  $document_tarif_public['id_isa'];
                    $image['SupplierFile']['supplier_id'] = $supplier_id;
                    $image['SupplierFile']['supplier_category_id'] = 1;
                    $image['SupplierFile']['creation_date'] = $dateTime;
                    $image['SupplierFile']['name'] = $document_tarif_public['nom'];
                    $image['SupplierFile']['active'] = $document_tarif_public['actif'];

                    $image_name = $file;
                    $image['SupplierFile']['file'] = $file;
                    $image['SupplierFile']['type'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['source_name'] = $image_name;

                    $this->SupplierFile = ClassRegistry::init('SupplierFile');
                    $this->SupplierFile->Behaviors->disable('Attachment');
                    $this->SupplierFile->Behaviors->disable('FileValidation');
                    $this->SupplierFile->create();

                    if (!$this->SupplierFile->save($image, true, $fields)) {
                        CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                    }
                }
            }
        }

        if (isset($supplier_documents['document_divers'])) { //2
            foreach ($supplier_documents['document_divers'] as $document_document_divers) {
                $url = $document_document_divers['url'];
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if ($source) {
                    file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);
                    $fields = array(
                        'SupplierFile' => array(
                            'id',
                            'supplier_id',
                            'supplier_category_id',
                            'creation_date',
                            'file',
                            'type',
                            'ext',
                            'source_name',
                            'name',
                            'active'
                        )
                    );

                    $dateTime = date('Y-m-d H:i:s');

                    $image['SupplierFile']['id'] =  $document_document_divers['id_isa'];
                    $image['SupplierFile']['supplier_id'] = $supplier_id;
                    $image['SupplierFile']['supplier_category_id'] = 2;
                    $image['SupplierFile']['creation_date'] = $dateTime;
                    $image['SupplierFile']['name'] = $document_document_divers['nom'];
                    $image['SupplierFile']['active'] = $document_document_divers['actif'];

                    $image_name = $file;
                    $image['SupplierFile']['file'] = $file;
                    $image['SupplierFile']['type'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['source_name'] = $image_name;

                    $this->SupplierFile = ClassRegistry::init('SupplierFile');
                    $this->SupplierFile->Behaviors->disable('Attachment');
                    $this->SupplierFile->Behaviors->disable('FileValidation');
                    $this->SupplierFile->create();
                    if (!$this->SupplierFile->save($image, true, $fields)) {
                        CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                    }
                }
            }
        }

        if (isset($supplier_documents['conditions'])) { //3
            foreach ($supplier_documents['conditions'] as $document_conditions) {
                $url = $document_conditions['url'];
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if ($source) {
                    file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);
                    $fields = array(
                        'SupplierFile' => array(
                            'id',
                            'supplier_id',
                            'supplier_category_id',
                            'creation_date',
                            'file',
                            'type',
                            'ext',
                            'source_name',
                            'name',
                            'active'
                        )
                    );

                    $dateTime = date('Y-m-d H:i:s');

                    $image['SupplierFile']['id'] =  $document_conditions['id_isa'];
                    $image['SupplierFile']['supplier_id'] = $supplier_id;
                    $image['SupplierFile']['supplier_category_id'] = 3;
                    $image['SupplierFile']['creation_date'] = $dateTime;
                    $image['SupplierFile']['name'] = $document_conditions['nom'];
                    $image['SupplierFile']['active'] = $document_conditions['actif'];

                    $image_name = $file;
                    $image['SupplierFile']['file'] = $file;
                    $image['SupplierFile']['type'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['source_name'] = $image_name;

                    $this->SupplierFile = ClassRegistry::init('SupplierFile');
                    $this->SupplierFile->Behaviors->disable('Attachment');
                    $this->SupplierFile->Behaviors->disable('FileValidation');
                    $this->SupplierFile->create();
                    if (!$this->SupplierFile->save($image, true, $fields)) {
                        CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                    }
                }
            }
        }

        if (isset($supplier_documents['remises_pf'])) { //4
            foreach ($supplier_documents['remises_pf'] as $document_remises_pf) {
                $url = $document_remises_pf['url'];
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if ($source) {
                    file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);
                    $fields = array(
                        'SupplierFile' => array(
                            'id',
                            'supplier_id',
                            'supplier_category_id',
                            'creation_date',
                            'file',
                            'type',
                            'ext',
                            'source_name',
                            'name',
                            'active'
                        )
                    );

                    $dateTime = date('Y-m-d H:i:s');

                    $image['SupplierFile']['id'] =  $document_remises_pf['id_isa'];
                    $image['SupplierFile']['supplier_id'] = $supplier_id;
                    $image['SupplierFile']['supplier_category_id'] = 4;
                    $image['SupplierFile']['creation_date'] = $dateTime;
                    $image['SupplierFile']['name'] = $document_remises_pf['nom'];
                    $image['SupplierFile']['active'] = $document_remises_pf['actif'];

                    $image_name = $file;
                    $image['SupplierFile']['file'] = $file;
                    $image['SupplierFile']['type'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierFile']['source_name'] = $image_name;

                    $this->SupplierFile = ClassRegistry::init('SupplierFile');
                    $this->SupplierFile->Behaviors->disable('Attachment');
                    $this->SupplierFile->Behaviors->disable('FileValidation');
                    $this->SupplierFile->create();
                    debug($image);
                    if (!$this->SupplierFile->save($image, true, $fields)) {
                        CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                    }
                }
            }
        }

        if (isset($supplier_documents['fiche_condition'])) {
            foreach ($supplier_documents['fiche_condition'] as $document_fiche_condition) {
                $url = $document_fiche_condition;
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if ($source) {
                    file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);
                }
            }
        }

        return true;
    }

    public function updateSupplierFR($supplier_json, $exist_supplier)
    { // New supplier comes from FRANCE JSON
        $clear_characters = array("+", "(", ")", " ", ".");
        $supplier_tmp = array(
            'Supplier' => array(
                'id' => $exist_supplier['Supplier']['id'],
                'code' => isset($supplier_json['code']) && $supplier_json['code']['code_aag'] ? $supplier_json['code']['code_aag'] : $exist_supplier['Supplier']['code'],
                'name' => isset($supplier_json['identite']) && isset($supplier_json['identite']['raison_sociale']) ? $supplier_json['identite']['raison_sociale'] : $exist_supplier['Supplier']['name'],
                'web' => isset($supplier_json['identite']) && isset($supplier_json['identite']['url']) ? $supplier_json['identite']['url'] : $exist_supplier['Supplier']['web'],
                'VAT_number' => isset($supplier_json['identite']) && isset($supplier_json['identite']['TVA']) ? str_replace($clear_characters, '', $supplier_json['identite']['TVA']) : $exist_supplier['Supplier']['VAT_number'],
                'siret' => isset($supplier_json['identite']) && isset($supplier_json['identite']['siret']) ? str_replace($clear_characters, '', $supplier_json['identite']['siret']) : $exist_supplier['Supplier']['siret'],
                'email' => isset($supplier_json['identite']) && isset($supplier_json['identite']['email']) ? $supplier_json['identite']['email'] : $exist_supplier['Supplier']['email'],
                'phone' => isset($supplier_json['identite']) && isset($supplier_json['identite']['telephone']) && is_numeric(str_replace($clear_characters, '', $supplier_json['identite']['telephone'])) ? str_replace($clear_characters, '', $supplier_json['identite']['telephone']) : $exist_supplier['Supplier']['phone'],
                'fax' => isset($supplier_json['identite']) && isset($supplier_json['identite']['fax']) && is_numeric(str_replace($clear_characters, '', $supplier_json['identite']['fax'])) ? str_replace($clear_characters, '', $supplier_json['identite']['fax']) : $exist_supplier['Supplier']['fax'],
                'town' => isset($supplier_json['identite']) && isset($supplier_json['identite']['ville']) ? $supplier_json['identite']['ville'] : $exist_supplier['Supplier']['town'],
                'postcode' => isset($supplier_json['identite']) && isset($supplier_json['identite']['code_postal']) ? $supplier_json['identite']['code_postal'] : $exist_supplier['Supplier']['postcode'],
                'address1' => isset($supplier_json['identite']) && isset($supplier_json['identite']['adresse1']) ? $supplier_json['identite']['adresse1'] : $exist_supplier['Supplier']['address1'],
                'address2' => isset($supplier_json['identite']) && isset($supplier_json['identite']['adresse2']) ? $supplier_json['identite']['adresse2'] : $exist_supplier['Supplier']['address2'],
                'description' => '--',
                'url_video' => null,
                'url_channel' => null,
                'active' => ($supplier_json['actif']) ? $supplier_json['actif'] : ConstantsBooleans::NO_ACTIVE,
                'edition_date' => date('Y-m-d H:i:s'),
                'publication_date' => date('Y-m-d H:i:s'),
            )
        );

        if (isset($supplier_json['identite']) && isset($supplier_json['identite']['url']) && !empty($supplier_json['identite']['url']) && substr($supplier_json['identite']['url'], 0, 7) !== "http://" && substr($supplier_json['identite']['url'], 0, 8) !== "https://") {
            $supplier_tmp['Supplier']['web'] = 'http://' . $supplier_json['identite']['url'];
        }

        $this->validator()->remove('publication_date');
        $supplier_bd = $this->save($supplier_tmp);
        if (!$supplier_bd) {
            CakeLog::write('updates-france', 'The supplier with ID ISA ' . $supplier_json['id_isa'] . ' could not be updated.' . PHP_EOL);
        } else {
            if (isset($supplier_json['divers']) && isset($supplier_json['divers']['logo']) && $supplier_json['divers']['logo']) {
                $this->SupplierImage = ClassRegistry::init('SupplierImage');

                $exist_images = $this->SupplierImage->findAllBySupplierId($supplier_bd['Supplier']['id']);
                if ($exist_images) {
                    foreach ($exist_images as $exist_image) {
                        FileManager::delete_file(WWW_ROOT, FilePaths::SUPPLIERS_IMAGES_RELATIVE . $exist_image['SupplierImage']['source_name']);
                        $this->SupplierImage->delete($exist_image['SupplierImage']['id']);
                    }
                }

                $url = $supplier_json['divers']['logo'];
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if (file_put_contents(WWW_ROOT . FilePaths::SUPPLIERS_IMAGES_RELATIVE . $file, $source)) {
                    $fields = array(
                        'SupplierImage' => array(
                            'supplier_id',
                            'creation_date',
                            'file',
                            'type',
                            'ext',
                            'source_name'
                        )
                    );

                    $dateTime = date('Y-m-d H:i:s');

                    $image['SupplierImage']['supplier_id'] = $supplier_bd['Supplier']['id'];
                    $image['SupplierImage']['creation_date'] = $dateTime;

                    $image_name = $file;
                    $image['SupplierImage']['file'] = $file;
                    $image['SupplierImage']['type'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
                    $image['SupplierImage']['source_name'] = $image_name;


                    $this->SupplierImage->Behaviors->disable('Attachment');
                    $this->SupplierImage->Behaviors->disable('FileValidation');
                    $this->SupplierImage->create();
                    if (!$this->SupplierImage->save($image, true, $fields)) {
                        CakeLog::write('updates-france', 'The logo supplier could not be updated.' . PHP_EOL);
                    }
                }
            }

            $this->SupplierTradingGroup = ClassRegistry::init('SupplierTradingGroup');
            $precisium_exist = $this->SupplierTradingGroup->findBySupplierIdAndTradingGroupId($supplier_bd['Supplier']['id'], 3);
            if (isset($supplier_json['precisium']) && $supplier_json['precisium'] && !$precisium_exist) {
                $fields = array(
                    'SupplierTradingGroup' => array(
                        'supplier_id',
                        'trading_group_id',
                    )
                );

                $supplier_trading_group['SupplierTradingGroup']['supplier_id'] = $supplier_bd['Supplier']['id'];
                $supplier_trading_group['SupplierTradingGroup']['trading_group_id'] = 3; // Precisium

                $this->SupplierTradingGroup->create();
                if (!$this->SupplierTradingGroup->save($supplier_trading_group, $fields)) {
                    CakeLog::write('updates-france', 'The Supplier-Trading group could not be created.' . PHP_EOL);
                }
            } elseif (isset($supplier_json['precisium']) && !$supplier_json['precisium'] && $precisium_exist) {
                $this->SupplierTradingGroup->delete($precisium_exist['SupplierTradingGroup']['id']);
            }

            $gefa_exist = $this->SupplierTradingGroup->findBySupplierIdAndTradingGroupId($supplier_bd['Supplier']['id'], 4);
            if (isset($supplier_json['gefa']) && $supplier_json['gefa'] && !$gefa_exist) {
                $fields = array(
                    'SupplierTradingGroup' => array(
                        'supplier_id',
                        'trading_group_id',
                    )
                );

                $supplier_trading_group['SupplierTradingGroup']['supplier_id'] = $supplier_bd['Supplier']['id'];
                $supplier_trading_group['SupplierTradingGroup']['trading_group_id'] = 4; // Gef Auto

                $this->SupplierTradingGroup->create();
                if (!$this->SupplierTradingGroup->save($supplier_trading_group, $fields)) {
                    CakeLog::write('updates-france', 'The Supplier-Trading group could not be created.' . PHP_EOL);
                }
            } elseif (isset($supplier_json['gefa']) && !$supplier_json['gefa'] && $gefa_exist) {
                $this->SupplierTradingGroup->delete($gefa_exist['SupplierTradingGroup']['id']);
            }

            $this->commit();
        }

        return $supplier_bd;
    }

    public function updateDocumentsFR($supplier_id, $supplier_documents)
    { // New document supplier comes from FRANCE JSON
        $this->SupplierFile = ClassRegistry::init('SupplierFile');

        if (isset($supplier_documents['tarif_public'])) { // 1
            foreach ($supplier_documents['tarif_public'] as $document_tarif_public) {
                $document_exist = $this->SupplierFile->findById($document_tarif_public['id_isa']);
                if (!$document_exist) {
                    $url = $document_tarif_public['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_tarif_public['id_isa'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_tarif_public['nom'];
                        $image['SupplierFile']['active'] = $document_tarif_public['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        $this->SupplierFile->create();
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                } else {
                    $url = $document_tarif_public['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_exist['SupplierFile']['id'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_tarif_public['nom'];
                        $image['SupplierFile']['active'] = $document_tarif_public['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        if (isset($supplier_documents['document_divers'])) { // 2
            foreach ($supplier_documents['document_divers'] as $document_document_divers) {
                $document_exist = $this->SupplierFile->findById($document_document_divers['id_isa']);
                if (!$document_exist) {
                    $url = $document_document_divers['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_document_divers['id_isa'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_document_divers['nom'];
                        $image['SupplierFile']['active'] = $document_document_divers['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        $this->SupplierFile->create();
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                } else {
                    $url = $document_document_divers['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_exist['SupplierFile']['id'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_document_divers['nom'];
                        $image['SupplierFile']['active'] = $document_document_divers['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        if (isset($supplier_documents['conditions'])) { // 3
            foreach ($supplier_documents['conditions'] as $document_conditions) {
                $document_exist = $this->SupplierFile->findById($document_conditions['id_isa']);
                if (!$document_exist) {
                    $url = $document_conditions['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_conditions['id_isa'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_conditions['nom'];
                        $image['SupplierFile']['active'] = $document_conditions['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        $this->SupplierFile->create();
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                } else {
                    $url = $document_conditions['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_exist['SupplierFile']['id'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_conditions['nom'];
                        $image['SupplierFile']['active'] = $document_conditions['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        if (isset($supplier_documents['remises_pf'])) { // 4
            foreach ($supplier_documents['remises_pf'] as $document_remises_pf) {
                $document_exist = $this->SupplierFile->findById($document_remises_pf['id_isa']);
                if (!$document_exist) {
                    $url = $document_remises_pf['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_remises_pf['id_isa'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_remises_pf['nom'];
                        $image['SupplierFile']['active'] = $document_remises_pf['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        $this->SupplierFile->create();
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                } else {
                    $url = $document_remises_pf['url'];
                    $file_tmp = explode("/", $url);
                    $file = end($file_tmp);
                    $source = file_get_contents($url);
                    if ($source) {
                        file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);

                        $fields = array(
                            'SupplierFile' => array(
                                'id',
                                'supplier_id',
                                'supplier_category_id',
                                'creation_date',
                                'file',
                                'type',
                                'ext',
                                'source_name',
                                'name',
                                'active'
                            )
                        );

                        $dateTime = date('Y-m-d H:i:s');
                        $image['SupplierFile']['id'] =  $document_exist['SupplierFile']['id'];
                        $image['SupplierFile']['supplier_id'] = $supplier_id;
                        $image['SupplierFile']['supplier_category_id'] = 1;
                        $image['SupplierFile']['creation_date'] = $dateTime;
                        $image['SupplierFile']['name'] = $document_remises_pf['nom'];
                        $image['SupplierFile']['active'] = $document_remises_pf['actif'];
                        $image['SupplierFile']['file'] = $file;
                        $image['SupplierFile']['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['ext'] = pathinfo($file, PATHINFO_EXTENSION);
                        $image['SupplierFile']['source_name'] = $file;

                        $this->SupplierFile = ClassRegistry::init('SupplierFile');
                        $this->SupplierFile->Behaviors->disable('Attachment');
                        $this->SupplierFile->Behaviors->disable('FileValidation');
                        if (!$this->SupplierFile->save($image, true, $fields)) {
                            CakeLog::write('updates-france', 'The file supplier could not be updated.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        if (isset($supplier_documents['fiche_condition'])) {
            foreach ($supplier_documents['fiche_condition'] as $document_fiche_condition) {
                $url = $document_fiche_condition;
                $file_tmp = explode("/", $url);
                $file = end($file_tmp);
                $source = file_get_contents($url);
                if ($source) {
                    file_put_contents(ConstantsPath::DIR_SUPPLIERS_FILES . DS . $file, $source);
                }
            }
        }

        return true;
    }

    public function getSupplierNameById($supplier_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Supplier.id' => $supplier_id,
            ),
        ));
    }

    public function getSuppliersByRegion($aag_region_id)
    {

        $conditions = array(
            'Supplier.aag_region_id' => $aag_region_id,
        );
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'conditions' => $conditions
        ));
    }

    public function getSuppliersAjax($conditions, $aag_region_id)
    {
        $suppliers = $this->getSuppliersQuery($conditions, $aag_region_id);
        $suppliers = Hash::combine($suppliers, '{n}.Supplier.id', array('%s', '{n}.Supplier.name'));

        return $suppliers;
    }

    public function getSuppliersQuery($conditions_ajax = array(), $aag_region_id)
    {
        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Supplier.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    $conditions_ajax,
                ),
                'fields' => array(
                    'Supplier.id',
                    'Supplier.name'
                ),
                'group' => array(
                    'Supplier.id'
                ),
            )
        );
    }

    public function getAllAboutSuppliersByTradingGroupAndRegion($trading_group_id, $aag_region_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
                array(
                    'alias' => 'SupplierTradingGroup',
                    'table' => 'suppliers_trading_groups',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = SupplierTradingGroup.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
            ),
            'conditions' => array(
                'Supplier.active' => ConstantsBooleans::ACTIVE,
                'Supplier.publication_date <=' => date('Y-m-d'),
                'SupplierTradingGroup.trading_group_id' => $trading_group_id,
                'Supplier.aag_region_id' => $aag_region_id
            ),
        ));
    }

    public function getAllAboutSuppliersByRegion($aag_region_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Supplier.id = SupplierImage.supplier_id',
                    ),
                ),
            ),
            'fields' => array(
                'Supplier.*',
                'SupplierImage.*',
            ),
            'conditions' => array(
                'Supplier.active' => ConstantsBooleans::ACTIVE,
                'Supplier.publication_date <=' => date('Y-m-d'),
                'Supplier.aag_region_id' => $aag_region_id
            ),
        ));
    }
}
