<?php

class AppointmentContact extends AppModel{

    public $useTable = 'appointments_contacts';

    public $hasMany = array(
        'Appointment'
    );

    public function add_appointment_contact( $appointments_contact, $appointment_id ){
        $fields = array(
            'AppointmentContact' => array(
                'appointment_id',
                'contact_id',
            )
        );

        foreach($appointments_contact as $contact_id){
            $this->create();
            $appointment_contact = array(
                'AppointmentContact' => array(
                    'appointment_id' => $appointment_id,
                    'contact_id' => $contact_id,
                )
            );

            if(!$this->guardar( $appointment_contact, $fields )){
                return false;
            }
        }

        $this->commit();
        return true;
    }

    public function remove_contact( $appointment_id ){
        $contacts = $this->findAllByAppointmentId( $appointment_id );
        foreach ($contacts as $contact) {
            $this->delete($contact['AppointmentContact']['id']);
        }
    }

	/**
	 * T001 SECURITY - It is not changed because it does not receive variable parameters per call.
	*/
    public function resetAutoIncrement(){
        $sql = "ALTER TABLE `appointments_contacts` AUTO_INCREMENT = 1;";
        $this->query($sql);
    }
}
