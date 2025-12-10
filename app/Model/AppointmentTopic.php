<?php

class AppointmentTopic extends AppModel {
    public $useTable = 'appointments_topics';

    public function add_appointment_topic($topic) {
        $fields = array(
            'AppointmentTopic' => array(
                'task_id',
                'topic_id'
            )
        );

        $this->create();
        $appointment_bd = $this->guardar($topic, $fields);
        if (!$appointment_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function add_appointment_topics( $appointments_topics , $appointment_id ){
        $fields = array(
            'AppointmentTopic' => array(
                'appointment_id',
                'topic_id',
            )
        );

        foreach($appointments_topics as $appointments_topic){
            $this->create();
            $appointment_topic_bd['appointment_id'] = $appointment_id;
            $appointment_topic_bd['topic_id'] = $appointments_topic;

            if(!$this->guardar( $appointment_topic_bd, $fields )){
                return false;
            }
        }

        $this->commit();
        return true;
    }

    public function getListByAppointmentId($appointment_id){
        return $this->find('list',array(
            'conditions' => array(
                'appointment_id' => $appointment_id
            ),
            'fields' => array(
                'id',
                'topic_id'
            )
        ));
    }
    public function getListTopicsByAppointmentId($appointment_id){
        return $this->find('list',array(
            'conditions' => array(
                'appointment_id' => $appointment_id
            ),
            'fields' => array(
                'topic_id',
                'topic_id'
            )
        ));
    }

    public function remove_appointment_topics($appointment_id){
        $appointment_topics = $this->findAllByAppointmentId( $appointment_id );
        foreach ($appointment_topics as $appointment_topic) {
            $this->delete($appointment_topic['AppointmentTopic']['id']);
        }
    }

	/**
	 * T001 SECURITY - It is not changed because it does not receive variable parameters per call.
	*/
    public function resetAutoIncrement(){
        $sql = "ALTER TABLE `appointments_topics` AUTO_INCREMENT = 1;";
        $this->query($sql);
    }

}
