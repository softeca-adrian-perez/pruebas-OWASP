<?php

App::uses('Agn', 'Lib');
class LanguageWebNetwork extends AppModel
{
    public $useTable = 'languages_webs_networks';

    public $validate = array(
        'network_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_network'
            ),
        ),
		'language_web_flag_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_flag'
            ),
        ),
		'code' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_locale'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
		'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        )
    );

	public function getLanguagesWebsNetwork($networkId, $all = true)
    {
		$conditions = array();
		$conditions[] = array('LanguageWebNetwork.network_id' => $networkId);
		if (!$all) {
			$conditions[] = array('LanguageWebNetwork.active' => ConstantsBooleans::ACTIVE);
		}
        return $this->find(
			'all',
			array(
				'joins' => array(
                    array(
                        'alias' => 'LanguageWebFlag',
                        'table' => 'languages_webs_flags',
                        'type' => 'INNER',
                        'conditions' => 'LanguageWebFlag.id = LanguageWebNetwork.language_web_flag_id'
                    ),
                ),
				'conditions' => $conditions,
				'fields' => array('LanguageWebNetwork.*', 'LanguageWebFlag.language_code', 'LanguageWebFlag.url')
			)
		);
    }

    public function getListLanguagesIdNameByNetwork($networkId)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'id',
                    'name'
                ),
                'conditions' => array('LanguageWebNetwork.network_id' => $networkId)
            )
        );
    }

	public function saveLanguageWebNetwork($data)
    {
		$languageWebFlag = ClassRegistry::init('LanguageWebFlag');
		$network = ClassRegistry::init('Network');
		$fields = array(
            'LanguageWebNetwork' => array(
                'network_id',
				'language_web_flag_id',
				'code',
				'name'
            )
        );

		$flag = $languageWebFlag->findByUrl($data['flag_loco']);
		if (!isset($flag)) {
			return false;
		}

        $languageWebNetwork['LanguageWebNetwork']['network_id'] = $data['network_id'];
		$languageWebNetwork['LanguageWebNetwork']['language_web_flag_id'] = $flag['LanguageWebFlag']['id'];
		$languageWebNetwork['LanguageWebNetwork']['code'] = $data['code_loco'];
		$languageWebNetwork['LanguageWebNetwork']['name'] = $data['name_loco'];

		if (isset($data['id']) && !empty($data['id'])) {
			$languageWebNetwork['LanguageWebNetwork']['id'] = $data['id'];
		} else{
			// Save in LOCO
			$agn = new Agn();
			$network = $network->findById($data['network_id']);
			$result = $agn->createLocoLanguage($network['Network']['guid'], $data['code_loco'] . '-' . $network['Network']['country_code_language']);
            if ($result['success'] == 'true') {
				$this->create();
			} else {
				return [
					'success' => false,
					'error' => $result['error']
				];
			}

		}
		return [
			'success' => $this->guardar($languageWebNetwork, $fields)
		];
    }
}
