<?php
class LeavingReasonComment extends AppModel
{
    public $useTable = 'leaving_reason_comments';

    public $validate = array(
		'comment' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Comment_is_too_long',
            ),
		),
    );

    public function add($garageNetworkId, $comment)
    {
		$fields = array(
			'LeavingReasonComment' => array(
				'garage_network_id',
				'comment',
                'date'
			)
		);
		$this->create();

        $data = array(
            'LeavingReasonComment' => array(
                'garage_network_id' => $garageNetworkId,
                'comment' => $comment,
                'date' => date('Y-m-d H:i:s')
            )
        );

		$leaving_reason_comment_bd = $this->guardar($data, $fields);
		if (!$leaving_reason_comment_bd) {
			return false;
		}

		$this->commit();
		return $leaving_reason_comment_bd;
	}

}