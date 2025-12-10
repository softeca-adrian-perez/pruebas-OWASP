<?php

class LanguageWebFlag extends AppModel
{
    public $useTable = 'languages_webs_flags';

	public function getLanguagesWebsFlags()
    {
        return $this->find(
			'list',
			array(
				'fields' => array('url', 'language_code'),
				'order' => 'language_code'
			)
		);
    }
}
