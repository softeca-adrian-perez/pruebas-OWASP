<?php
class AppointmentComment extends AppModel
{
    public $useTable = 'appointments_comments';

    public $hasOne = array(
        'Appointment',
    );

    public $validate = array(
        'body' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function new_comment($comment)
    {
        $fields = array(
            'AppointmentComment' => array(
                'appointment_id',
                'body',
                'creation_date',
                'user_id',
            )
        );
        $comment['AppointmentComment']['creation_date'] = date('Y/m/d H:i:s');
        $comment['AppointmentComment']['user_id'] = CakeSession::read('Auth.User.id');
        $this->create();
        $comment_bd = $this->guardar($comment, $fields);
        if (!$comment_bd) {
            return false;
        }

        $this->commit();
        return $comment_bd;
    }

    public function getAllCommentsByGarageId($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Appointment',
                        'table' => 'appointments',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Appointment.id = AppointmentComment.appointment_id'
                        )
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Garage.id = Appointment.garage_id'
                        )
                    )
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id
                ),
                'order' => array('AppointmentComment.creation_date DESC')
            )
        );
    }

    public function getAllCommentsByAppointmentId($appointment_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Appointment',
                        'table' => 'appointments',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Appointment.id = AppointmentComment.appointment_id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Appointment.id' => $appointment_id
                ),
                'order' => array('AppointmentComment.creation_date DESC')
            )
        );
    }
}
