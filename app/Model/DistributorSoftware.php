<?php

class DistributorSoftware extends AppModel
{
    public $useTable = 'distributors_software';

    public $validate = array(
        'software_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_software',
            ),
        ),
        'start_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'end_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'version' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'username' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'password' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function add_distributor_software($distributor_software, $distributor_id)
    {
        $fields = array(
            'DistributorSoftware' => array(
                'distributor_id',
                'software_id',
                'software_type_id',
                'supplier_id',
                'software_manufacture_id',
                'start_date',
                'modification_date',
                'end_date',
                'version',
                'username',
                'password',
            )
        );
        $distributor_software['DistributorSoftware']['distributor_id'] = $distributor_id;
        $distributor_software['DistributorSoftware']['start_date'] = Fecha::toFormatoBd($distributor_software['DistributorSoftware']['start_date']);
        $distributor_software['DistributorSoftware']['end_date'] = Fecha::toFormatoBd($distributor_software['DistributorSoftware']['end_date']);
        $distributor_software['DistributorSoftware']['modification_date'] = date('Y-m-d');

        $this->create();

        $distributor_software_bd = $this->guardar($distributor_software, $fields);
        if (!$distributor_software_bd) {
            return false;
        }

        $this->commit();
        return $distributor_software_bd;
    }

    public function edit_distributor_software($distributor_software)
    {
        $fields = array(
            'DistributorSoftware' => array(
                'software_id',
                'software_type_id',
                'supplier_id',
                'software_manufacture_id',
                'start_date',
                'modification_date',
                'end_date',
                'version',
                'username',
                'password',
            )
        );
        $distributor_software['DistributorSoftware']['start_date'] = Fecha::toFormatoBd($distributor_software['DistributorSoftware']['start_date']);
        $distributor_software['DistributorSoftware']['end_date'] = Fecha::toFormatoBd($distributor_software['DistributorSoftware']['end_date']);
        $distributor_software['DistributorSoftware']['modification_date'] = date('Y-m-d');

        $this->create();

        $distributor_software_bd = $this->guardar($distributor_software, $fields);
        if (!$distributor_software_bd) {
            return false;
        }

        $this->commit();
        return $distributor_software_bd;
    }

    public function removeSoftwareFromDistributors($software_id)
    {
        $distributors = $this->findAllBySoftwareId($software_id);

        foreach ($distributors as $distributor) {
            $this->delete($distributor['DistributorSoftware']['id']);
        }
    }

    public function getSoftwareByDistributorId($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Software',
                        'table' => 'software',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Software.id = DistributorSoftware.software_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorSoftware.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Software.*',
                    'DistributorSoftware.*'
                ),
            )
        );
    }

    public function getSoftwareByDistributorIdLimited($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Software',
                        'table' => 'software',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Software.id = DistributorSoftware.software_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorSoftware.distributor_id' => $distributor_id,
                ),
                'limit' => 5,
                'fields' => array(
                    'Software.*',
                    'DistributorSoftware.*'
                ),
            )
        );
    }

    public function findSoftwareExport($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Software',
                        'table' => 'software',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Software.id = DistributorSoftware.software_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorSoftware.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Software.name_' . __l(),
                    'DistributorSoftware.*',
                ),
            )
        );
    }
}
