<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class ImportExcelController extends AppController
{
    public $uses = array(
        'TrainingProvider',
        'Venue',
        'TrainingCourse',
        'CourseType',
        'TrainingPlannedCourse',
        'TrainingTrainer',
        'Garage',
        'Contact',
        'GarageContactStaff',
        'User',
        'TrainingDelegate',
        'GarageNetwork',
        'Network',
        'AnnexDetail',
        'GarageComment',
        'Software',
        'GarageSoftware',
        'Order',
        'OrderType',
        'GarageImage',
        'GarageNetworkImage',
        'GarageContactStaff',
        'TrainingPlannedCourse',
        'ReasonDelegate',
        'Booking',
        'Vehicle',
        'Work',
        'GarageNetworkWork',
        'GarageNetworkWorkLabour',
        'ServiceDriver',
        'GarageNetworkServiceDriver',
        'Language',
        'UserRecoverPassword',
        'Email',
    );

    /**
     * Update garages loaded by UK JSON with the Excel info.
     */
    // public function updateUkGarages()
    // {
    //     try {
    //         ini_set('memory_limit', '3G');
    //         set_time_limit(30 * 60);

    //         CakeLog::config('script-crm', array(
    //             'engine' => 'FileLog',
    //             'types' => array('info', 'error', 'warning'),
    //             'scopes' => array('script-crm'),
    //             'file' => 'script-crm.log',
    //         ));

    //         $file_path = APP . 'Files/pcu.xlsx';
    //         App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
    //         $reader = PHPExcel_IOFactory::load($file_path);

    //         // Trining Providers
    //         // $this->loadTrainingProvidersInfo($reader);

    //         // Venues
    //         // $this->loadVenuesInfo($reader);

    //         // Training Courses
    //         // $this->loadTrainingCoursesInfo($reader);

    //         // Planned Courses
    //         // $this->loadPlannedCoursesInfo($reader);

    //         // Garage Contact
    //         // $this->loadGarageContactsInfo($reader);

    //         // Delegate Training
    //         //$this->loadDelegateTrainingInfo($reader);

    //         // Garage info
    //         // $this->loadGarageInfo($reader);

    //         // Garage Images
    //         // $this->loadGarageImagesInfo($reader);

    //         // Garage Networks
    //         // $this->loadGarageNetworksInfo($reader);

    //         // Garage Comments
    //         // $this->loadGarageNotesInfo($reader);

    //         // Garage Orders
    //         // $this->loadGarageOrdersInfo($reader, 1, 1000);

    //         // Garage Software
    //         // $this->loadGarageSoftwareInfo($reader, Configure::read('AAG_REGION_ID_UK_IRELAND'););

    //         //Garage Phones
    //         // $this->loadGaragePhonesInfo($reader);

    //         //Garage Bookings
    //         // $this->loadGarageBookingsInfo($reader);

    //         //Garage Bookings UPDATE
    //         //$this->updateGarageBookingsInfo($reader);

    //         //Garage ContactEmails
    //         //$this->updateContactEmails($reader);

    //         //Garage GarageCoordinates
    //         // $this->updateGarageCoordinates($reader);

    //         //Garage Vehicles
    //         //$this->loadVehiclesInfo($reader);

    //         // Garage Comments V2
    //         // $this->loadGarageNotesInfoV2($reader);

    //         // Update Garage Network Start Date
    //         // $this->updateGarageNetworkStartDate($reader);

    //         // Update Garage Network Start Received Date
    //         // $this->updateGarageNetworkReceivedDate($reader);

    //         // Update Garage Network GXPERT
    //         //$this->addGarageNetworkInformation($reader);

    //         // Update Garages ContactsV2
    //         //$this->loadGarageContactsInfoV2($reader);

    //         // Update Garage Contact Staff Priority
    //         // $this->updateGarageContactStaffPriority($reader);

    //         $this->autoRender = false;

    //         echo ('FINALIZADO OK <br>');
    //         return true;
    //     } catch (Exception $e) {
    //         debug($e);
    //         $this->autoRender = false;
    //         echo ('FINALIZADO KO  <br>');
    //         return false;
    //     }
    // }

    // private function loadTrainingProvidersInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Training providers')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             $trainingProvider = array(
    //                 'TrainingProvider' => array(
    //                     'name' => $row['A']
    //                 )
    //             );

    //             $fields = array('TrainingProvider' => array('name'));
    //             $this->TrainingProvider->create();
    //             $trainingProviderBd = $this->TrainingProvider->save($trainingProvider, array('fieldList' => $fields));

    //             if (!$trainingProviderBd) {
    //                 CakeLog::write('script-crm', 'Training providers - Failed insert at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadVenuesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('L Venues')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             $venue = array(
    //                 'Venue' => array(
    //                     'name' => $this->notEmpty($row['B']) ? $row['B'] : null,
    //                     'address_1' => $this->notEmpty($row['C']) ? $row['C'] : null,
    //                     'address_2' => $this->notEmpty($row['D']) ? $row['D'] : null,
    //                     'address_3' => $this->notEmpty($row['E']) ? $row['E'] : null,
    //                     'address_4' => $this->notEmpty($row['F']) ? $row['F'] : null,
    //                     'town' => $this->notEmpty($row['G']) ? $row['G'] : null,
    //                     'post_code' => $this->notEmpty($row['H']) ? $row['H'] : null,
    //                     'telephone' => $this->notEmpty($row['I']) ? $row['I'] : null,
    //                     'active' => ConstantsBooleans::ACTIVE,
    //                     'aag_region_id' => Configure::read('AAG_REGION_ID_UK_IRELAND'),
    //                     'guid' => $row['A']
    //                 )
    //             );

    //             $fields = array('Venue' => array('name', 'address_1', 'address_2', 'address_3', 'address_4', 'town', 'post_code', 'telephone', 'active', 'aag_region_id', 'guid'));
    //             $this->Venue->create();

    //             $this->Venue->validator()->remove('address_1');
    //             $this->Venue->validator()->remove('address_2');
    //             $this->Venue->validator()->remove('town');
    //             $this->Venue->validator()->remove('post_code');
    //             $this->Venue->validator()->remove('telephone');

    //             $venueBd = $this->Venue->save($venue, array('fieldList' => $fields));
    //             if (!$venueBd) {
    //                 CakeLog::write('script-crm', 'L Venues - Failed insert at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadTrainingCoursesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Course')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             $courseTypeId = null;
    //             if ($this->notEmpty($row['C'])) {
    //                 $courseType = $this->CourseType->findByNameEn($row['C']);
    //                 if ($courseType) {
    //                     $courseTypeId = $courseType['CourseType']['id'];
    //                 }
    //             }

    //             // if Training Provider isn't specified, it's set to Other
    //             $trainingProviderName = $this->notEmpty($row['E']) ? $row['E'] : 'Other';
    //             $trainingProvider = $this->TrainingProvider->findByName($trainingProviderName);
    //             $trainingProviderId = $trainingProvider ? $trainingProvider['TrainingProvider']['id'] : null;

    //             $name = $row['B'];

    //             // check if exists any TrainingCourse with the same name, in this case the row number is added to the name
    //             $trainingCourseBd = $this->TrainingCourse->findByName($name);
    //             if ($trainingCourseBd) {
    //                 $name .= " " . $key;
    //             }

    //             $trainingCourse = array(
    //                 'TrainingCourse' => array(
    //                     'name' => $name,
    //                     'course_type_id' => $courseTypeId,
    //                     'training_provider_id' => $trainingProviderId,
    //                     'duration' => $row['D'],
    //                     'price' => $row['F'],
    //                     'price_credit' => $row['G'],
    //                     'description' => $this->notEmpty($row['I']) ? $row['I'] : null,
    //                     'part_number' => $this->notEmpty($row['H']) ? $row['H'] : null,
    //                     'active' => $row['J'] == 1 ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE,
    //                     'guid' => $row['A']
    //                 )
    //             );

    //             $fields = array(
    //                 'TrainingCourse' => array(
    //                     'name',
    //                     'course_type_id',
    //                     'training_provider_id',
    //                     'duration', 'price',
    //                     'price_credit',
    //                     'description',
    //                     'part_number',
    //                     'active',
    //                     'guid'
    //                 )
    //             );

    //             $this->TrainingCourse->create();
    //             $trainingCourseBd = $this->TrainingCourse->save($trainingCourse, array('fieldList' => $fields));

    //             if (!$trainingCourseBd) {
    //                 CakeLog::write('script-crm', 'Course - Failed insert at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadPlannedCoursesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Planned Course ')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $trainingTrainerDefault = $this->TrainingTrainer->findByName('Default');

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             $trainingCourse = $this->TrainingCourse->findByGuid($row['A']);
    //             $venue = $this->Venue->findByGuid($row['G']);

    //             if ($trainingCourse && $venue) {
    //                 $startDate = null;
    //                 if ($this->notEmpty($row['D'])) {
    //                     $startDate = explode("-", $row['D']);
    //                     $startDate = date('Y-m-d', mktime(0, 0, 0, $startDate[0], $startDate[1], $startDate[2]));
    //                 }

    //                 $endDate = null;
    //                 if ($this->notEmpty($row['E'])) {
    //                     $endDate = explode("-", $row['E']);
    //                     $endDate = date('Y-m-d', mktime(0, 0, 0, $endDate[0], $endDate[1], $endDate[2]));
    //                 }

    //                 $status = null;
    //                 switch ($row['H']) {
    //                     case 0:
    //                         $status = ConstantsPlannnedCourseStatus::ACTIVE;
    //                         break;
    //                     case 1:
    //                         $status = ConstantsPlannnedCourseStatus::CANCELED;
    //                         break;
    //                     case 'NULL':
    //                     default:
    //                         $status = ConstantsPlannnedCourseStatus::INACTIVE;
    //                         break;
    //                 }

    //                 $trainingPlannedCourse = array(
    //                     'TrainingPlannedCourse' => array(
    //                         'training_course_id' => $trainingCourse['TrainingCourse']['id'],
    //                         'training_trainer_id' => $trainingTrainerDefault['TrainingTrainer']['id'],
    //                         'venue_id' => $venue['Venue']['id'],
    //                         'date_from' => $startDate,
    //                         'date_to' => $endDate,
    //                         'duration' => $this->notEmpty($row['F']) || $row['F'] == '0' ? $row['F'] : null,
    //                         'status' => $status,
    //                         'new_imported_name' => $this->notEmpty($row['C']) ? $row['C'] : null,
    //                         'full' => $row['I'] == 1 ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE,
    //                         'guid' => $row['B']
    //                     )
    //                 );

    //                 $fields = array(
    //                     'TrainingPlannedCourse' => array(
    //                         'training_course_id',
    //                         'training_trainer_id',
    //                         'venue_id',
    //                         'date_from',
    //                         'date_to',
    //                         'duration',
    //                         'status',
    //                         'new_imported_name',
    //                         'full',
    //                         'guid'
    //                     )
    //                 );

    //                 $this->TrainingPlannedCourse->create();
    //                 $trainingPlannedCourseBd = $this->TrainingPlannedCourse->save($trainingPlannedCourse, array('fieldList' => $fields));

    //                 if (!$trainingPlannedCourseBd) {
    //                     CakeLog::write('script-crm', 'Planned Course - Failed insert at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 CakeLog::write('script-crm', 'Planned Course - Failed insert at line (course or venue not found)' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageContactsInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('T Garage Contacts')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $contNewContact = 1;
    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // if column B isn't empty and isn't 'NULL
    //             if ($this->notEmpty($row['B'])) {
    //                 // search Garage by GNumber
    //                 $garage = $this->Garage->findByGNumberId($row['A']);

    //                 if ($garage) {
    //                     $contactFirstName = $this->notEmpty($row['C']) ? $row['C'] : '';
    //                     $contactLastName = $this->notEmpty($row['D']) ? $row['D'] : '';

    //                     $garageId = $garage['Garage']['id'];
    //                     $contact = $this->Contact->findByFirstNameAndLastNameAndGarageId($contactFirstName, $contactLastName, $garageId);

    //                     $phone = $this->notEmpty($row['E']) ? $row['E'] : null;
    //                     $mobile_phone = $this->notEmpty($row['F']) ? $row['F'] : null;
    //                     $email = $this->notEmpty($row['G']) ? $row['G'] : '';

    //                     if ($contact) {
    //                         $contact['Contact']['first_name'] = $contactFirstName;
    //                         $contact['Contact']['last_name'] = $contactLastName;
    //                         $contact['Contact']['phone'] = $phone;
    //                         $contact['Contact']['mobile_phone'] = $mobile_phone;
    //                         $contact['Contact']['email'] = $email;
    //                         $contact['Contact']['guid'] = $row['B'];

    //                         $fields = array('Contact' => array('first_name', 'last_name', 'phone', 'mobile_phone', 'email', 'guid'));
    //                     } else {
    //                         $contact = array(
    //                             'Contact' => array(
    //                                 'first_name' => empty($contactFirstName) ? 'Default' : $contactFirstName,
    //                                 'last_name' => empty($contactLastName) ? 'Default' : $contactLastName,
    //                                 'garage_id' => $garageId,
    //                                 'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
    //                                 'phone' => $phone,
    //                                 'mobile_phone' => $mobile_phone,
    //                                 'identification_number' => "IMPORT_" . $contNewContact,
    //                                 'email' => $email,
    //                                 'creation_date' => date('Y-m-d H:i:s'),
    //                                 'guid' => $row['B']
    //                             )
    //                         );
    //                         $fields = array('Contact' => array('first_name', 'last_name', 'garage_id', 'position_id', 'phone', 'mobile_phone', 'identification_number', 'email', 'creation_date', 'guid'));
    //                         $this->Contact->create();
    //                     }

    //                     $this->Contact->validator()->remove('phone');
    //                     $this->Contact->validator()->remove('mobile_phone');
    //                     $this->Contact->validator()->remove('email');
    //                     $contact = $this->Contact->save($contact, array('fieldList' => $fields));

    //                     if ($contact) {
    //                         $contNewContact++;

    //                         $username = $this->notEmpty($row['I']) ? $row['I'] : null;
    //                         $password = $this->notEmpty($row['J']) ? $row['J'] : null;

    //                         // only if username and password aren't empty, user is created
    //                         if (!empty($username) && !empty($password)) {
    //                             // check if user already exists
    //                             $user = $this->User->findByUsername($username);
    //                             if (!$user) {
    //                                 $user = array(
    //                                     'User' => array(
    //                                         'contact_id' => $contact['Contact']['id'],
    //                                         'name' => $contact['Contact']['first_name'],
    //                                         'surname' => $contact['Contact']['last_name'],
    //                                         'username' => str_pad($username, 5), // fill up to 5 characters
    //                                         'password' => $password,
    //                                         'role_id' => ConstantsRoles::GARAGE,
    //                                         'language_id' => ConstantsLanguages::ENGLISH,
    //                                         'garage_id' => $garageId,
    //                                         'active' => ConstantsBooleans::ACTIVE,
    //                                         'creation_date' => date('Y-m-d H:i:s'),
    //                                         'aag_region_id' => Configure::read('AAG_REGION_ID_UK_IRELAND'),
    //                                         'guid' => CakeText::uuid()
    //                                     )
    //                                 );

    //                                 $fields = array(
    //                                     'User' => array(
    //                                         'contact_id',
    //                                         'name',
    //                                         'surname',
    //                                         'username',
    //                                         'password',
    //                                         'role_id',
    //                                         'language_id',
    //                                         'garage_id',
    //                                         'active',
    //                                         'creation_date',
    //                                         'aag_region_id',
    //                                         'guid'
    //                                     )

    //                                 );
    //                                 $this->User->create();
    //                                 $this->User->validator()->remove('password');
    //                                 $userBd = $this->User->save($user, array('fieldList' => $fields));

    //                                 if (!$userBd) {
    //                                     $result = false;
    //                                     CakeLog::write('script-crm', 'T Garage Contacts - Failed saving user info at line ' . $key . PHP_EOL);
    //                                 }
    //                             }
    //                         }

    //                         $garageContactStaff = $this->GarageContactStaff->findByGarageIdAndContactId($garageId, $contact['Contact']['id']);

    //                         if ($garageContactStaff) {
    //                             $garageContactStaff['GarageContactStaff']['main_contact'] = $row['H'];
    //                             $fields = array('GarageContactStaff' => array('main_contact'));
    //                         } else {
    //                             $garageContactStaff = array(
    //                                 'GarageContactStaff' => array(
    //                                     'garage_id' => $garageId,
    //                                     'contact_id' => $contact['Contact']['id'],
    //                                     'main_contact' => $row['H']
    //                                 )
    //                             );
    //                             $fields = array('GarageContactStaff' => array('garage_id', 'contact_id', 'main_contact'));
    //                         }
    //                         $garageContactStaffBd = $this->GarageContactStaff->save($garageContactStaff, array('fieldList' => $fields));

    //                         if (!$garageContactStaffBd) {
    //                             CakeLog::write('script-crm', 'T Garage Contacts - Failed insert at line ' . $key . PHP_EOL);
    //                         }
    //                     } else {
    //                         CakeLog::write('script-crm', 'T Garage Contacts - Failed saving/updating contact info at line ' . $key . PHP_EOL);
    //                     }
    //                 } else {
    //                     // is only saved in the log and the saving process is continued
    //                     CakeLog::write('script-crm', 'T Garage Contacts - Garage not found at line ' . $key . PHP_EOL);
    //                 }
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadDelegateTrainingInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Delegates')->toArray(null, true, true, true);
    //     unset($file[1]); //Quitamos la cabecera

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {

    //         if (!empty($row['C'])) {

    //             $garage = $this->Garage->findByGNumberId($row['C']);

    //             if ($garage) {

    //                 if ($this->notEmpty($row['A'])) {

    //                     $contact = $this->Contact->findByGuid($row['A']);

    //                     if ($contact) {

    //                         $garageContactStaff = $this->GarageContactStaff->findByGarageIdAndContactId($garage['Garage']['id'], $contact['Contact']['id']);
    //                         $garageContactStaffId = null;

    //                         if (!$garageContactStaff) {

    //                             $garageContactStaffSave = array(
    //                                 'GarageContactStaff' => array(
    //                                     'garage_id' => $garage['Garage']['id'],
    //                                     'contact_id' => $contact['Contact']['id']
    //                                 )
    //                             );

    //                             $fields = array('GarageContactStaff' => array('garage_id', 'contact_id'));
    //                             $this->GarageContactStaff->create();
    //                             $garageContactStaffBd = $this->GarageContactStaff->save($garageContactStaffSave, array('fieldList' => $fields));
    //                             $garageContactStaffId =  $garageContactStaffBd['GarageContactStaff']['id'];
    //                         } else {
    //                             $garageContactStaffId =  $garageContactStaff['GarageContactStaff']['id'];
    //                         }

    //                         $trainingPlannedCourseId = null;
    //                         if ($this->notEmpty($row['B'])) {
    //                             $trainingPlannedCourse = $this->TrainingPlannedCourse->findByGuid($row['B']);
    //                             if ($trainingPlannedCourse) {
    //                                 $trainingPlannedCourseId = $trainingPlannedCourse['TrainingPlannedCourse']['id'];
    //                             } else {
    //                                 CakeLog::write('script-crm', 'Delegate Training - Planned course not found at line ' . $key . PHP_EOL);
    //                             }
    //                         }

    //                         $reasonDelegateId = null;
    //                         if ($this->notEmpty($row['J'])) {
    //                             $reasonDelegate = $this->ReasonDelegate->findByNameEn($row['J']);
    //                             if (!$reasonDelegate) {
    //                                 $reasonDelegateSave = array(
    //                                     'ReasonDelegate' => array(
    //                                         'name_en' => $row['J'],
    //                                         'name_fr' => $row['J'],
    //                                         'name_de' => $row['J'],
    //                                         'name_nl' => $row['J'],
    //                                         'name_lc' => 'Bd.ReasonsDelegates'
    //                                     )
    //                                 );

    //                                 $fields = array('ReasonDelegate' => array('name_en', 'name_fr', 'name_de', 'name_nl', 'name_lc'));

    //                                 $this->ReasonDelegate->create();
    //                                 $reasonDelegateBd = $this->ReasonDelegate->save($reasonDelegateSave, array('fieldList' => $fields));
    //                                 $reasonDelegateId = $reasonDelegateBd['ReasonDelegate']['id'];
    //                             } else {
    //                                 $reasonDelegateId = $reasonDelegate['ReasonDelegate']['id'];
    //                             }
    //                         }

    //                         $trainingDelegate = array(
    //                             'TrainingDelegate' => array(
    //                                 'training_planned_course_id' => $trainingPlannedCourseId,
    //                                 'garage_contact_staff_id' => $garageContactStaffId,
    //                                 'reason_delegate_id' => $reasonDelegateId,
    //                                 'order_number' => $row['D'],
    //                                 'invoice_number' => $this->notEmpty($row['E']) ? $row['E'] : null,
    //                                 'cancelled' => $this->notEmpty($row['F']) ? $row['F'] : 0,
    //                                 'refund_credits' => $this->notEmpty($row['G']) ? $row['G'] : null,
    //                                 'manual_credits_calculation' => $this->notEmpty($row['H']) ? $row['H'] : null,
    //                                 'paid_separately' => $this->notEmpty($row['I']) ? $row['I'] : null,
    //                             )
    //                         );

    //                         $fields = array(
    //                             'TrainingDelegate' =>
    //                             array(
    //                                 'training_planned_course_id',
    //                                 'garage_contact_staff_id',
    //                                 'reason_delegate_id',
    //                                 'order_number',
    //                                 'invoice_number',
    //                                 'cancelled',
    //                                 'refund_credits',
    //                                 'manual_credits_calculation',
    //                                 'paid_separately'
    //                             )
    //                         );

    //                         $this->TrainingDelegate->create();

    //                         $this->TrainingDelegate->validator()->remove('training_planned_course_id');
    //                         $this->TrainingDelegate->validator()->remove('garage_contact_staff_id');

    //                         $trainingDelegateBd = $this->TrainingDelegate->save($trainingDelegate, array('fieldList' => $fields));

    //                         if (!$trainingDelegateBd) {
    //                             CakeLog::write('script-crm', 'Delegate Training - Failed insert at line ' . $key . PHP_EOL);
    //                         }
    //                     } else {
    //                         // is only saved in the log and the saving process is continued
    //                         CakeLog::write('script-crm', 'Delegate Training - Contact not found at line ' . $key . PHP_EOL);
    //                     }
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'Delegate Training - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column C is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('T GarageCMSDelta')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {
    //                 $garage['Garage']['ref_code'] = $this->notEmpty($row['B']) ? $row['B'] : null; //In some cases multiple rows have the same value and it has unique validator
    //                 $garage['Garage']['email'] = $this->notEmpty($row['C']) ? @array_shift(array_filter(explode(';',  $row['C']))) : null; //In some cases there are multiple emails
    //                 $garage['Garage']['foundation_year'] = $this->notEmpty($row['F']) ? $row['F'] : null;
    //                 $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

    //                 $fields = array('Garage' => array('ref_code', 'email', 'foundation_year', 'modification_date'));
    //                 $garageBd = $this->Garage->save($garage, array('fieldList' => $fields));

    //                 //Remove email validation
    //                 $this->Garage->validator()->remove('email');

    //                 // only if Garage is well saved and Garage Description is not empty, AGN GarageNetwork is searched and updated
    //                 if ($garageBd && $this->notEmpty($row['D'])) { //REVISAR
    //                     // search AGN GarageNetwork
    //                     $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garageBd['Garage']['id'], NETWORK_ID_AGN);

    //                     if ($garageNetwork) {
    //                         $garageNetwork['GarageNetwork']['about'] = $row['D'];
    //                         $garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');
    //                         $fields = array('GarageNetwork' => array('about', 'modification_date'));
    //                         $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));

    //                         if (!$garageNetworkBd) {
    //                             CakeLog::write('script-crm', 'T GarageCMSDelta - Failed saving/updating garage network info at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 } else {
    //                     CakeLog::write('script-crm', 'T GarageCMSDelta - Failed insert at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T GarageCMSDelta - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageImagesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('T Garage Images')->toArray(null, true, true, true);
    //     unset($file[1]);
    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 // Remote image URL
    //                 $urlImage = $row['B'];
    //                 $imageName = $key . "_" . @array_pop(array_filter(explode('/',  $urlImage)));
    //                 $type = @array_pop(array_filter(explode('.',  $imageName)));
    //                 $imageType = '';

    //                 switch ($type) {
    //                     case "jpg":
    //                         $imageType = 'image/jpeg';
    //                         break;
    //                     case "jpeg":
    //                         $imageType = 'image/jpeg';
    //                         break;
    //                     case "JPG":
    //                         $imageType = 'image/jpeg';
    //                         break;
    //                     case "png":
    //                         $imageType = 'image/png';
    //                         break;
    //                     case "PNG":
    //                         $imageType = 'image/png';
    //                         break;
    //                     case "webp":
    //                         $imageType = 'image/webp';
    //                         break;
    //                     case "bmp":
    //                         $imageType = 'image/bmp';
    //                         break;
    //                     case "BMP":
    //                         $imageType = 'image/bmp';
    //                         break;
    //                     case "gif":
    //                         $imageType = 'image/gif';
    //                         break;
    //                     case "GIF":
    //                         $imageType = 'image/gif';
    //                         break;
    //                     case "jfif":
    //                         $imageType = 'image/jfif';
    //                         break;
    //                 }

    //                 //Garage images
    //                 $img = ConstantsPath::DIR_GARAGE_IMAGES . '/' . $imageName;
    //                 $resultGarageImage = file_put_contents($img, file_get_contents($urlImage));

    //                 $garageImageSave = array(
    //                     'GarageImage' => array(
    //                         'garage_id' => $garage['Garage']['id'],
    //                         'file' => $imageName,
    //                         'source_name' => $imageName,
    //                         'type' => $imageType,
    //                         'ext' => $type,
    //                         'principal' => $row['C'],
    //                         'creation_date' => date('Y-m-d H:i:s')
    //                     )
    //                 );

    //                 $fields = array('GarageImage' => array('garage_id', 'file', 'source_name', 'type', 'ext', 'principal', 'creation_date'));
    //                 $this->GarageImage->create();
    //                 $garageImageBd = $this->GarageImage->save($garageImageSave, array('fieldList' => $fields));

    //                 if (!$garageImageBd) {
    //                     $result = false;
    //                     CakeLog::write('script-crm', 'T Garage Images - Failed insert at line ' . $key . PHP_EOL);
    //                     break;
    //                 }

    //                 $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garage['Garage']['id'], NETWORK_ID_AGN);

    //                 $resultGarageNetworkImage = null;

    //                 if (!empty($garageNetwork)) {

    //                     //Garage Networks images
    //                     $imgNetwork = ConstantsPath::DIR_GARAGE_NETWORK_IMAGES . '/' . $imageName;
    //                     $resultGarageNetworkImage = file_put_contents($imgNetwork, file_get_contents($urlImage));

    //                     $garageNetworkImageSave = array(
    //                         'GarageNetworkImage' => array(
    //                             'garage_network_id' =>  $garageNetwork['GarageNetwork']['id'],
    //                             'file' => $imageName,
    //                             'source_name' => $imageName,
    //                             'type' => $imageType,
    //                             'ext' => $type,
    //                             'principal' => $row['C'],
    //                             'creation_date' => date('Y-m-d H:i:s')
    //                         )
    //                     );

    //                     $fields = array('GarageNetworkImage' => array('garage_network_id', 'file', 'source_name', 'type', 'ext', 'principal', 'creation_date'));
    //                     $this->GarageNetworkImage->create();
    //                     $garageNetworkImageBd = $this->GarageNetworkImage->save($garageNetworkImageSave, array('fieldList' => $fields));

    //                     if (!$garageNetworkImageBd) {
    //                         $result = false;
    //                         CakeLog::write('script-crm', 'T Garage Images - Failed insert at line ' . $key . PHP_EOL);
    //                         break;
    //                     }
    //                 }

    //                 if (!$resultGarageImage) {
    //                     CakeLog::write('script-crm', 'T Garage Images - Dont have permissions to get image from url at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T Garage Images - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageNetworksInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('T GarageNetworks')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {
    //                 // search Network by name
    //                 $network = $this->Network->findByName($row['B']);

    //                 // if Network doesn't exists create new line on log
    //                 if (!$network) {
    //                     CakeLog::write('script-crm', 'T GarageNetworks - Network not found at line ' . $key . PHP_EOL);
    //                 }

    //                 $status = null;
    //                 switch ($row['C']) {
    //                     case 'Live':
    //                         $status = ConstantsNetworksStatus::LIVE;
    //                         break;
    //                     case 'Left':
    //                         $status = ConstantsNetworksStatus::LEFT;
    //                         break;
    //                     case 'On hold':
    //                         $status = ConstantsNetworksStatus::ON_HOLD;
    //                         break;
    //                 }

    //                 $contractSentDate = null;
    //                 if ($this->notEmpty($row['D'])) {
    //                     $contractSentDate = explode("-", $row['D']);
    //                     $contractSentDate = date('Y-m-d', mktime(0, 0, 0, $contractSentDate[0], $contractSentDate[1], $contractSentDate[2]));
    //                 }

    //                 $contractReceivedDate = null;
    //                 if ($this->notEmpty($row['E'])) {
    //                     $contractReceivedDate = explode("-", $row['E']);
    //                     $contractReceivedDate = date('Y-m-d', mktime(0, 0, 0, $contractReceivedDate[0], $contractReceivedDate[1], $contractReceivedDate[2]));
    //                 }

    //                 $contractStartDate = null;
    //                 if ($this->notEmpty($row['F'])) {
    //                     $contractStartDate = explode("-", $row['F']);
    //                     $contractStartDate = date('Y-m-d', mktime(0, 0, 0, $contractStartDate[0], $contractStartDate[1], $contractStartDate[2]));
    //                 }

    //                 $dateOnHold = null;
    //                 if ($this->notEmpty($row['G'])) {
    //                     $dateOnHold = explode("-", $row['G']);
    //                     $dateOnHold = date('Y-m-d', mktime(0, 0, 0, $dateOnHold[0], $dateOnHold[1], $dateOnHold[2]));
    //                 }

    //                 $contractEndDate = null;
    //                 if ($this->notEmpty($row['H'])) {
    //                     $contractEndDate = explode("-", $row['H']);
    //                     $contractEndDate = date('Y-m-d', mktime(0, 0, 0, $contractEndDate[0], $contractEndDate[1], $contractEndDate[2]));
    //                 }

    //                 $reasonLeaving = $this->notEmpty($row['I']) ? $row['I'] : null;

    //                 $annexDetailId = null;
    //                 if ($this->notEmpty($row['J'])) {
    //                     $annexDetail = $this->AnnexDetail->findByNameEn($row['J']);
    //                     if ($annexDetail) {
    //                         $annexDetailId = $annexDetail['AnnexDetail']['id'];
    //                     }
    //                 }

    //                 $ddActive = $this->notEmpty($row['K']) ? $row['K'] : ConstantsBooleans::NO_ACTIVE;
    //                 $currentCharge = $this->notEmpty($row['L']) ? $row['L'] : null;
    //                 $garagePays = $this->notEmpty($row['M']) ? $row['M'] : null;
    //                 $memberPays = $this->notEmpty($row['N']) ? $row['N'] : null;

    //                 // search GarageNetwork

    //                 $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garage['Garage']['id'], $network['Network']['id']);

    //                 if ($garageNetwork) {
    //                     $garageNetwork['GarageNetwork']['status'] = $status;
    //                     $garageNetwork['GarageNetwork']['contract_sent_date'] = $contractSentDate;
    //                     $garageNetwork['GarageNetwork']['contract_received_date'] = $contractReceivedDate;
    //                     $garageNetwork['GarageNetwork']['contract_start_date'] = $contractStartDate;
    //                     $garageNetwork['GarageNetwork']['date_on_hold'] = $dateOnHold;
    //                     $garageNetwork['GarageNetwork']['contract_end_date'] = $contractEndDate;
    //                     $garageNetwork['GarageNetwork']['reason_leaving'] = $reasonLeaving;
    //                     $garageNetwork['GarageNetwork']['annex_detail_id'] = $annexDetailId;
    //                     $garageNetwork['GarageNetwork']['dd_active'] = $ddActive;
    //                     $garageNetwork['GarageNetwork']['current_charge'] = $currentCharge;
    //                     $garageNetwork['GarageNetwork']['garage_pays'] = $garagePays;
    //                     $garageNetwork['GarageNetwork']['member_pays'] = $memberPays;
    //                     $garageNetwork['GarageNetwork']['imported'] = ConstantsBooleans::YES;
    //                     $garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');
    //                 } else {
    //                     $garageNetwork = array(
    //                         'GarageNetwork' => array(
    //                             'garage_id' => $garage['Garage']['id'],
    //                             'network_id' => $network['Network']['id'],
    //                             'status' => $status,
    //                             'contract_sent_date' => $contractSentDate,
    //                             'contract_received_date' => $contractReceivedDate,
    //                             'contract_start_date' => $contractStartDate,
    //                             'date_on_hold' => $dateOnHold,
    //                             'contract_end_date' => $contractEndDate,
    //                             'reason_leaving' => $reasonLeaving,
    //                             'annex_detail_id' => $annexDetailId,
    //                             'dd_active' => $ddActive,
    //                             'current_charge' => $currentCharge,
    //                             'garage_pays' => $garagePays,
    //                             'member_pays' => $memberPays,
    //                             'imported' => ConstantsBooleans::YES,
    //                             'creation_date' => date('Y-m-d H:i:s')
    //                         )
    //                     );
    //                 }

    //                 $fields = array('GarageNetwork' => array('garage_id', 'network_id', 'status', 'contract_sent_date', 'contract_received_date', 'contract_start_date', 'date_on_hold', 'contract_end_date', 'reason_leaving', 'annex_detail_id', 'dd_active', 'current_charge', 'garage_pays', 'member_pays', 'creation_date', 'imported', 'modification_date'));

    //                 //Remove validators
    //                 $this->GarageNetwork->validator()->remove('garage_pays');
    //                 $this->GarageNetwork->validator()->remove('contract_sent_date');
    //                 $this->GarageNetwork->validator()->remove('contract_start_date');
    //                 $this->GarageNetwork->validator()->remove('current_charge');

    //                 $this->GarageNetwork->create();
    //                 $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));
    //                 if (!$garageNetworkBd) {
    //                     CakeLog::write('script-crm', 'T GarageNetworks - Failed insert at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T GarageNetworks - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();

    //         //LOG OF GARAGES NETWORKS NOT IMPORTED
    //         foreach ($file as $key => $row) {
    //             if (!empty($row['A'])) {

    //                 // search Garage by GNumber
    //                 $garage = $this->Garage->findByGNumberId($row['A']);

    //                 //Find all garageNetworks associated to garage from excel
    //                 $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);

    //                 foreach ($garageNetworks as $garageNetwork) {

    //                     //If imported == 0 create new line on log
    //                     if ($garageNetwork['GarageNetwork']['imported'] == ConstantsBooleans::NO) {
    //                         CakeLog::write('script-crm', 'Alert Garage - ' . $garage['Garage']['g_number_id'] . ' - Network');
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }

    // private function loadGarageNotesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('L Garage Notes')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 $createdDate = null;
    //                 if ($this->notEmpty($row['C'])) {
    //                     $createdDate = explode(" ", $row['C']);
    //                     $date = explode("/", $createdDate[0]);
    //                     $time = explode(":", $createdDate[1]);
    //                     $createdDate = date('Y-m-d h:i:s', mktime($time[0], $time[1], $time[2], $date[1], $date[0], $date[2]));
    //                 }

    //                 // if NoteText isn't "NULL"
    //                 if ($this->notEmpty($row['D'])) {
    //                     $garageComment = array(
    //                         'GarageComment' => array(
    //                             'garage_id' => $garage['Garage']['id'],
    //                             'body' => $row['D'],
    //                             'creation_date' =>  $createdDate,
    //                             'created_by_name' => $row['B']
    //                         )
    //                     );

    //                     $fields = array('GarageComment' => array('garage_id', 'body', 'creation_date', 'created_by_name'));
    //                     $this->GarageComment->create();
    //                     $garageCommentBd = $this->GarageComment->save($garageComment, array('fieldList' => $fields));
    //                     if (!$garageCommentBd) {
    //                         CakeLog::write('script-crm', 'T Garage Notes - Failed insert at line ' . $key . PHP_EOL);
    //                     }
    //                 } else {
    //                     // is only saved in the log and the saving process is continued
    //                     CakeLog::write('script-crm', 'T Garage Notes - NoteText is NULL at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T Garage Notes - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageOrdersInfo($reader, $min, $max)
    // {
    //     $file = $reader->setActiveSheetIndexByName('L Garage Orders')->toArray(null, true, true, true);
    //     unset($file[1]);
    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if ($key >= $min && $key <= $max) {
    //             if (!empty($row['A'])) {
    //                 // search Garage by GNumber
    //                 $garage = $this->Garage->findByGNumberId($row['A']);

    //                 if ($garage) {
    //                     // if OrderType isn't "NULL"
    //                     if ($this->notEmpty($row['B'])) {

    //                         $orderType = $this->OrderType->findByNameEn($row['B']);
    //                         $orderTypeId = null;

    //                         if ($orderType) {
    //                             $orderTypeId =  $orderType['OrderType']['id'];
    //                         } else {
    //                             $orderTypeSave = array(
    //                                 'OrderType' => array(
    //                                     'name_en' => $row['B'],
    //                                     'name_fr' => $row['B'],
    //                                     'name_de' => $row['B'],
    //                                     'name_nl' => $row['B'],
    //                                     'name_lc' => 'Bd.OrderType'
    //                                 )
    //                             );

    //                             $fields = array('OrderType' => array('name_en', 'name_fr', 'name_de', 'name_nl', 'name_lc'));
    //                             $this->OrderType->create();
    //                             $orderTypeBd = $this->OrderType->save($orderTypeSave, array('fieldList' => $fields));
    //                             $orderTypeId =  $orderTypeBd['OrderType']['id'];
    //                         }

    //                         $completedDate = null;
    //                         if ($this->notEmpty($row['E'])) {
    //                             $completedDate = explode("-", $row['E']);
    //                             $completedDate = date('Y-m-d', mktime(0, 0, 0, $completedDate[0], $completedDate[1], $completedDate[2]));
    //                         }

    //                         $orderDate = null;
    //                         if ($this->notEmpty($row['F'])) {
    //                             $orderDate = explode("-", $row['F']);
    //                             $orderDate = date('Y-m-d', mktime(0, 0, 0, $orderDate[0], $orderDate[1], $orderDate[2]));
    //                         }

    //                         $orderSave = array(
    //                             'Order' => array(
    //                                 'garage_id' => $garage['Garage']['id'],
    //                                 'order_type_id' => $orderTypeId,
    //                                 'invoice_number' => $this->notEmpty($row['C']) ? $row['C'] : null,
    //                                 'invoice_amount' => $this->notEmpty($row['D']) ? $row['D'] : null,
    //                                 'completed_date' => $completedDate,
    //                                 'order_date' => $orderDate,
    //                                 'signage_sign_A_q' => $this->notEmpty($row['G']) ? $row['G'] : null,
    //                                 'signage_sign_B1_q' => $this->notEmpty($row['H']) ? $row['H'] : null,
    //                                 'signage_sign_B2_q' => $this->notEmpty($row['I']) ? $row['I'] : null,
    //                                 'signage_sign_C1_q' => $this->notEmpty($row['J']) ? $row['J'] : null,
    //                                 'signage_sign_C2_q' => $this->notEmpty($row['K']) ? $row['K'] : null,
    //                                 'signage_sign_C3_q' => $this->notEmpty($row['L']) ? $row['L'] : null,
    //                                 'signage_sign_D1_q' => $this->notEmpty($row['M']) ? $row['M'] : null,
    //                                 'signage_sign_D2_q' => $this->notEmpty($row['N']) ? $row['N'] : null,
    //                                 'signage_sign_bespoke_q' => $this->notEmpty($row['O']) ? $row['O'] : null,
    //                                 'rebranding_signage' => $this->notEmpty($row['P']) ? $row['P'] : null,
    //                             )
    //                         );

    //                         $fields = array(
    //                             'Order' => array(
    //                                 'garage_id',
    //                                 'order_type_id',
    //                                 'invoice_number',
    //                                 'invoice_amount',
    //                                 'completed_date',
    //                                 'order_date',
    //                                 'signage_sign_A_q',
    //                                 'signage_sign_B1_q',
    //                                 'signage_sign_B2_q',
    //                                 'signage_sign_C1_q',
    //                                 'signage_sign_C2_q',
    //                                 'signage_sign_C3_q',
    //                                 'signage_sign_D1_q',
    //                                 'signage_sign_D2_q',
    //                                 'signage_sign_bespoke_q',
    //                                 'rebranding_signage',
    //                             )
    //                         );

    //                         $this->Order->create();
    //                         $orderBd = $this->Order->save($orderSave, array('fieldList' => $fields));

    //                         if (!$orderBd) {
    //                             $result = false;
    //                             CakeLog::write('script-crm', 'T Garage Orders - Failed insert at line ' . $key . PHP_EOL);
    //                             break;
    //                         }
    //                     }
    //                 } else {
    //                     // is only saved in the log and the saving process is continued
    //                     CakeLog::write('script-crm', 'T Garage Orders - Garage not found at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // if column A is empty, stop iteration
    //                 break;
    //             }
    //         } else if ($key == $max) {
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageSoftwareInfo($reader, $aag_region_id)
    // {
    //     $file = $reader->setActiveSheetIndexByName('L Garage Software')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {

    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {
    //                 // if SoftwareType isn't "NULL"
    //                 if ($this->notEmpty($row['B'])) {

    //                     $software = $this->Software->findByNameEn($row['B']);
    //                     $softwareId = null;

    //                     if ($software) {
    //                         $softwareId = $software['Software']['id'];
    //                     } else {
    //                         $softwareSave = array(
    //                             'Software' => array(
    //                                 'name_en' => $row['B'],
    //                                 'name_fr' => $row['B'],
    //                                 'name_de' => $row['B'],
    //                                 'name_nl' => $row['B'],
    //                                 'name_es' => $row['B'],
    //                                 'name_lc' => 'Bd.Software',
    //                                 'aag_region_id' => $aag_region_id
    //                             )
    //                         );
    //                         $fields = array('Software' => array('name_en', 'name_fr', 'name_de', 'name_nl', 'name_es', 'name_lc', 'aag_region_id'));
    //                         $this->Software->create();
    //                         $softwareBd = $this->Software->save($softwareSave, array('fieldList' => $fields));
    //                         $softwareId = $softwareBd['Software']['id'];
    //                     }

    //                     $garageSoftwareSave = array(
    //                         'GarageSoftware' => array(
    //                             'garage_id' => $garage['Garage']['id'],
    //                             'software_id' => $softwareId,
    //                             'username' => $row['C'],
    //                             'version' => $row['D'],
    //                             'amount' => $this->notEmpty($row['E']) ? $row['E'] : null,
    //                             'start_date' => $this->notEmpty($row['F']) ? $row['F'] : null,
    //                             'end_date' =>  $this->notEmpty($row['G']) ? $row['G'] : null,
    //                         )
    //                     );

    //                     $fields = array('GarageSoftware' => array('garage_id', 'software_id', 'username', 'version', 'amount'));
    //                     $this->GarageSoftware->create();
    //                     $garageSoftwareBd = $this->GarageSoftware->save($garageSoftwareSave, array('fieldList' => $fields));

    //                     if (!$garageSoftwareBd) {
    //                         CakeLog::write('script-crm', 'T Garage Software - Failed insert at line ' . $key . PHP_EOL);
    //                     }
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T Garage Software - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGaragePhonesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 $garage['Garage']['phone'] = $this->notEmpty($row['B']) ? $row['B'] : null;
    //                 $garage['Garage']['mobile'] = $this->notEmpty($row['C']) ? $row['C'] : null;

    //                 $fields = array('Garage' => array('phone', 'mobile'));
    //                 $garageBd = $this->Garage->save($garage, array('fieldList' => $fields));

    //                 if (!$garageBd) {
    //                     CakeLog::write('script-crm', 'GarageMobilePhone - Failed saving/updating garage mobiles info at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'GarageMobilePhone - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageBookingsInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 $customerName = $row['C'] . ' ' . $row['D'] . ' ' . $row['E'];

    //                 $serviceDate = null;
    //                 if ($this->notEmpty($row['G'])) {
    //                     $serviceDate = explode("-", $row['G']);
    //                     $serviceDate = date('Y-m-d', mktime(0, 0, 0, $serviceDate[0], $serviceDate[1], $serviceDate[2]));
    //                 }

    //                 $status = null;
    //                 switch ($row['J']) {
    //                     case 'Cancelled':
    //                         $status = ConstantsBookingsStatus::CANCELLED;
    //                         break;
    //                     case 'Confirmed':
    //                         $status = ConstantsBookingsStatus::CONFIRMED;
    //                         break;
    //                     case 'Pending':
    //                         $status = ConstantsBookingsStatus::PENDING;
    //                         break;
    //                 }

    //                 $additionalInfo = null;
    //                 if ($this->notEmpty($row['H']) || $this->notEmpty($row['I'])) {
    //                     $additionalInfo = $row['H'] . ' - ' . $row['I'];
    //                 }

    //                 $bookingSave = array(
    //                     'Booking' => array(
    //                         'garage_id' => $garage['Garage']['id'],
    //                         'network_id' => NETWORK_ID_AGN,
    //                         'plate' => $this->notEmpty($row['B']) ? Texto::encryptDecryptText($row['B'], true) : null,
    //                         'customer_name' => Texto::encryptDecryptText($customerName, true),
    //                         'customer_email' => Texto::encryptDecryptText($row['F'], true),
    //                         'date' => $serviceDate,
    //                         'additional_info' => $additionalInfo,
    //                         'booking_status' => $status,
    //                         'creation_date' => date('Y-m-d H:i:s')
    //                     )
    //                 );

    //                 $fields = array(
    //                     'Booking' => array(
    //                         'garage_id',
    //                         'network_id',
    //                         'plate',
    //                         'customer_name',
    //                         'customer_email',
    //                         'date',
    //                         'additional_info',
    //                         'booking_status',
    //                         'creation_date'
    //                     )
    //                 );

    //                 $this->Booking->create();
    //                 $bookingBd = $this->Booking->save($bookingSave, array('fieldList' => $fields));

    //                 if (!$bookingBd) {
    //                     CakeLog::write('script-crm', 'GarageBookings - Failed saving/updating garage booking info at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'GarageBookings - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function updateGarageBookingsInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 $customerName = $row['C'] . ' ' . $row['D'] . ' ' . $row['E'];
    //                 $booking = $this->Booking->findByCustomerNameAndGarageId(Texto::encryptDecryptText($customerName, true), $garage['Garage']['id']);
    //                 if ($booking && $booking['Booking']['id'] <= 104) {
    //                     $booking['Booking']['customer_phone'] = Texto::encryptDecryptText($row['K'], true);
    //                     $fields = array('Booking' => array('customer_phone'));
    //                     $bookingBd = $this->Booking->save($booking, array('fieldList' => $fields));

    //                     if (!$bookingBd) {
    //                         CakeLog::write('script-crm', 'GarageBookings - Failed saving/updating garage booking info at line ' . $key . PHP_EOL);
    //                     }
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'GarageBookings - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function updateContactEmails($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A']) && !empty($row['A'])) {
    //             $user = $this->User->findByUsername($row['B']);
    //             if ($user) {

    //                 $contact = $this->Contact->findById($user['User']['contact_id']);
    //                 $contact['Contact']['email'] = $this->notEmpty($row['A']) ? $row['A'] : null;
    //                 $fields = array('Contact' => array('email'));
    //                 $contactBd = $this->Contact->save($contact, array('fieldList' => $fields));

    //                 if (!$contactBd) {
    //                     CakeLog::write('script-crm', 'UpdateEmails - Failed updating contacts emails info at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 CakeLog::write('script-crm', 'UpdateEmails - User not found at line ' . $key . PHP_EOL);
    //             }
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function updateGarageCoordinates($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {

    //             debug($key);
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 $garage['Garage']['longitude'] = $this->notEmpty($row['B']) ? $row['B'] : null;
    //                 $garage['Garage']['latitude'] = $this->notEmpty($row['C']) ? $row['C'] : null;

    //                 $fields = array('Garage' => array('longitude', 'latitude'));
    //                 $garageBd = $this->Garage->save($garage, array('fieldList' => $fields));
    //                 debug($garageBd);
    //                 if (!$garageBd) {
    //                     CakeLog::write('script-crm', 'GarageCoordinates - Failed saving/updating garage coordinates info at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'GarageCoordinates - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadVehiclesInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Vehicles')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {

    //             $vehicleSave = array(
    //                 'Vehicle' => array(
    //                     'name_en' => $row['A'],
    //                     'name_fr' => $row['A'],
    //                     'name_de' => $row['A'],
    //                     'name_nl' => $row['A'],
    //                     'name_lc' => 'Bd.Vehicles'
    //                 )
    //             );

    //             $fields = array('Vehicle' => array('name_en', 'name_fr', 'name_de', 'name_nl', 'name_lc'));
    //             $this->Vehicle->create();
    //             $vehicleBd = $this->Vehicle->save($vehicleSave, array('fieldList' => $fields));
    //             if (!$vehicleBd) {
    //                 CakeLog::write('script-crm', 'T Vahicles - Failed insert at line ' . $key . PHP_EOL);
    //             }
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageNotesInfoV2($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {

    //                 $createdDate = null;
    //                 if ($this->notEmpty($row['C'])) {
    //                     $createdDate = explode(" ", $row['C']);
    //                     $date = explode("/", $createdDate[0]);
    //                     $time = explode(":", $createdDate[1]);
    //                     $createdDate = date('Y-m-d h:i:s', mktime($time[0], $time[1], $time[2], $date[1], $date[0], $date[2]));
    //                 }

    //                 // if NoteText isn't "NULL"
    //                 if ($this->notEmpty($row['D'])) {
    //                     $garageComment = array(
    //                         'GarageComment' => array(
    //                             'garage_id' => $garage['Garage']['id'],
    //                             'body' => $row['D'],
    //                             'creation_date' => $createdDate,
    //                             'created_by_name' => $row['B']
    //                         )
    //                     );

    //                     $fields = array('GarageComment' => array('garage_id', 'body', 'creation_date', 'created_by_name'));
    //                     $this->GarageComment->create();
    //                     $garageCommentBd = $this->GarageComment->save($garageComment, array('fieldList' => $fields));
    //                     if (!$garageCommentBd) {
    //                         CakeLog::write('script-crm', 'T Garage Notes - Failed insert at line ' . $key . PHP_EOL);
    //                     }
    //                 } else {
    //                     // is only saved in the log and the saving process is continued
    //                     CakeLog::write('script-crm', 'T Garage Notes - NoteText is NULL at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T Garage Notes - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function updateGarageNetworkStartDate($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('startDate')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {
    //                 // search Network by name
    //                 $network = $this->Network->findByName($row['B']);

    //                 // if Network doesn't exists create new line on log
    //                 if (!$network) {
    //                     CakeLog::write('script-crm', 'T GarageNetworks - Network not found at line ' . $key . PHP_EOL);
    //                 }

    //                 $contractStartDate = null;
    //                 if ($this->notEmpty($row['C'])) {
    //                     $contractStartDate = explode("-", $row['C']);
    //                     $contractStartDate = date('Y-m-d', mktime(0, 0, 0, $contractStartDate[0], $contractStartDate[1], $contractStartDate[2]));
    //                 }

    //                 // search GarageNetwork
    //                 if (!empty($network)) {
    //                     $garageNetworks = $this->GarageNetwork->findAllByGarageIdAndNetworkId($garage['Garage']['id'], $network['Network']['id']);
    //                 }

    //                 if (isset($garageNetworks)) {
    //                     foreach ($garageNetworks as $key => $garageNetwork) {
    //                         if ($garageNetwork['GarageNetwork']['contract_start_date'] == '1970-01-01') {
    //                             unset($garageNetworks[$key]);
    //                         }
    //                     }

    //                     if (isset($garageNetworks[0])) {
    //                         $garageNetworks[0]['GarageNetwork']['contract_start_date'] = $contractStartDate;
    //                         $garageNetworks[0]['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

    //                         $fields = array('GarageNetwork' => array('contract_start_date', 'modification_date'));

    //                         $garageNetworkBd = $this->GarageNetwork->save($garageNetworks[0], array('fieldList' => $fields));
    //                         if (!$garageNetworkBd) {
    //                             CakeLog::write('script-crm', 'T GarageNetworks - Failed insert at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T GarageNetworks - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function updateGarageNetworkReceivedDate($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('receivedDate')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {
    //                 // search Network by name
    //                 $network = $this->Network->findByName($row['B']);

    //                 // if Network doesn't exists create new line on log
    //                 if (!$network) {
    //                     CakeLog::write('script-crm', 'T GarageNetworks - Network not found at line ' . $key . PHP_EOL);
    //                 }

    //                 $contractReceivedDate = null;
    //                 if ($this->notEmpty($row['C'])) {
    //                     $contractReceivedDate = explode("-", $row['C']);
    //                     $contractReceivedDate = date('Y-m-d', mktime(0, 0, 0, $contractReceivedDate[0], $contractReceivedDate[1], $contractReceivedDate[2]));
    //                 }

    //                 // search GarageNetwork
    //                 if (!empty($network)) {
    //                     $garageNetworks = $this->GarageNetwork->findAllByGarageIdAndNetworkId($garage['Garage']['id'], $network['Network']['id']);
    //                 }

    //                 if (isset($garageNetworks)) {
    //                     foreach ($garageNetworks as $key => $garageNetwork) {
    //                         if ($garageNetwork['GarageNetwork']['contract_received_date'] == '1970-01-01') {
    //                             unset($garageNetworks[$key]);
    //                         }
    //                     }

    //                     if (isset($garageNetworks[0])) {
    //                         $garageNetworks[0]['GarageNetwork']['contract_received_date'] = $contractReceivedDate;
    //                         $garageNetworks[0]['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

    //                         $fields = array('GarageNetwork' => array('contract_received_date', 'modification_date'));

    //                         $garageNetworkBd = $this->GarageNetwork->save($garageNetworks[0], array('fieldList' => $fields));
    //                         if (!$garageNetworkBd) {
    //                             CakeLog::write('script-crm', 'T GarageNetworks - Failed insert at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T GarageNetworks - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function addGarageNetworkInformation($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Networks')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // search Garage by GNumber
    //             $garage = $this->Garage->findByGNumberId($row['A']);

    //             if ($garage) {
    //                 // search Network by name
    //                 $network = $this->Network->findByName($row['B']);

    //                 // if Network doesn't exists create new line on log
    //                 if (!$network) {
    //                     CakeLog::write('script-crm', 'T GarageNetworks - Network not found at line ' . $key . PHP_EOL);
    //                 }

    //                 $status = null;
    //                 switch ($row['C']) {
    //                     case 'Live':
    //                         $status = ConstantsNetworksStatus::LIVE;
    //                         break;
    //                     case 'Left':
    //                         $status = ConstantsNetworksStatus::LEFT;
    //                         break;
    //                     case 'On hold':
    //                         $status = ConstantsNetworksStatus::ON_HOLD;
    //                         break;
    //                 }

    //                 // search GarageNetwork

    //                 $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garage['Garage']['id'], $network['Network']['id']);

    //                 if ($garageNetwork) {
    //                     $garageNetwork['GarageNetwork']['status'] = $status;
    //                     $garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');
    //                 } else {
    //                     $garageNetwork = array(
    //                         'GarageNetwork' => array(
    //                             'garage_id' => $garage['Garage']['id'],
    //                             'network_id' => $network['Network']['id'],
    //                             'status' => $status,
    //                             'creation_date' => date('Y-m-d H:i:s'),
    //                             'modification_date' => date('Y-m-d H:i:s')
    //                         )
    //                     );
    //                 }

    //                 $fields = array('GarageNetwork' => array('garage_id', 'network_id', 'status', 'creation_date', 'modification_date'));

    //                 //Remove validators
    //                 $this->GarageNetwork->validator()->remove('garage_pays');
    //                 $this->GarageNetwork->validator()->remove('contract_sent_date');
    //                 $this->GarageNetwork->validator()->remove('contract_start_date');
    //                 $this->GarageNetwork->validator()->remove('current_charge');

    //                 $this->GarageNetwork->create();
    //                 $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));
    //                 if (!$garageNetworkBd) {
    //                     CakeLog::write('script-crm', 'T GarageNetworks - Failed insert at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'T GarageNetworks - Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function loadGarageContactsInfoV2($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Contacts')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     $contNewContact = 1;

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // if column B isn't empty and isn't 'NULL
    //             if ($this->notEmpty($row['B'])) {
    //                 $garage = $this->Garage->findByGNumberId($row['A']);
    //                 if ($garage) {

    //                     $contactFirstName = $this->notEmpty($row['C']) ? $row['C'] : '';
    //                     $contactLastName = $this->notEmpty($row['D']) ? $row['D'] : '';
    //                     $phone = $this->notEmpty($row['E']) ? $row['E'] : '';
    //                     $mobilePhone = $this->notEmpty($row['F']) ? $row['F'] : '';
    //                     $email = $this->notEmpty($row['G']) ? $row['G'] : '';

    //                     $garageId = $garage['Garage']['id'];
    //                     $contact = $this->Contact->findByGuid($row['B']);

    //                     if (!empty($contact)) {
    //                         $contact['Contact']['first_name'] = $contactFirstName;
    //                         $contact['Contact']['last_name'] = $contactLastName;
    //                         $contact['Contact']['phone'] = $phone;
    //                         $contact['Contact']['mobile_phone'] = $mobilePhone;
    //                         $contact['Contact']['email'] = $email;
    //                         $contact['Contact']['guid'] = $row['B'];

    //                         $fields = array('Contact' => array('first_name', 'last_name', 'phone', 'mobile_phone', 'email', 'guid'));

    //                         $this->Contact->validator()->remove('phone');
    //                         $this->Contact->validator()->remove('mobile_phone');
    //                         $this->Contact->validator()->remove('email');

    //                         $contact = $this->Contact->save($contact, array('fieldList' => $fields));
    //                         if (!$contact) {
    //                             CakeLog::write('script-crm', 'Contacts - Failed updating at line ' . $key . PHP_EOL);
    //                         }
    //                     } else {
    //                         $contact = array(
    //                             'Contact' => array(
    //                                 'first_name' => empty($contactFirstName) ? 'Default' : $contactFirstName,
    //                                 'last_name' => empty($contactLastName) ? 'Default' : $contactLastName,
    //                                 'garage_id' => $garageId,
    //                                 'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
    //                                 'phone' => $phone,
    //                                 'mobile_phone' => $mobilePhone,
    //                                 'identification_number' => "IMPORT_V2_" . $contNewContact,
    //                                 'email' => $email,
    //                                 'creation_date' => date('Y-m-d'),
    //                                 'guid' => $row['B']
    //                             )
    //                         );

    //                         $fields = array('Contact' => array('first_name', 'last_name', 'garage_id', 'position_id', 'phone', 'mobile_phone', 'identification_number', 'email', 'creation_date', 'guid'));
    //                         $this->Contact->create();
    //                         $this->Contact->validator()->remove('phone');
    //                         $this->Contact->validator()->remove('mobile_phone');
    //                         $this->Contact->validator()->remove('email');
    //                         $contact = $this->Contact->save($contact, array('fieldList' => $fields));

    //                         if ($contact) {
    //                             $contNewContact++;
    //                             $garageContactStaff = $this->GarageContactStaff->findByGarageIdAndContactId($garageId, $contact['Contact']['id']);
    //                             if (!$garageContactStaff) {
    //                                 $garageContactStaff = array(
    //                                     'GarageContactStaff' => array(
    //                                         'garage_id' => $garageId,
    //                                         'contact_id' => $contact['Contact']['id'],
    //                                     )
    //                                 );
    //                                 $fields = array('GarageContactStaff' => array('garage_id', 'contact_id'));
    //                             }
    //                             $garageContactStaffBd = $this->GarageContactStaff->save($garageContactStaff, array('fieldList' => $fields));
    //                             if (!$garageContactStaffBd) {
    //                                 CakeLog::write('script-crm', 'Garage Contacts - Failed insert at line ' . $key . PHP_EOL);
    //                             }
    //                         } else {
    //                             CakeLog::write('script-crm', 'Contacts - Failed insert at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 }
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function updateGarageContactStaffPriority($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);

    //     $result = true;
    //     $this->begin();
    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) { // If exists GUID keep iterating
    //             $contact = $this->Contact->findByGuid($row['A']);
    //             if ($contact) {
    //                 $garage = $this->Garage->findById($contact['Contact']['garage_id']);
    //                 if ($garage) {
    //                     $garageContactStaff = $this->GarageContactStaff->findByGarageIdAndContactId($garage['Garage']['id'], $contact['Contact']['id']);
    //                     $garageContactStaffId = null;
    //                     if (!$garageContactStaff) {
    //                         $garageContactStaffSave = array(
    //                             'GarageContactStaff' => array(
    //                                 'garage_id' => $garage['Garage']['id'],
    //                                 'contact_id' => $contact['Contact']['id'],
    //                                 'priority' => ConstantsBooleans::YES,
    //                             )
    //                         );
    //                         $fields = array('GarageContactStaff' => array('garage_id', 'contact_id', 'priority'));
    //                         $this->GarageContactStaff->create();
    //                         $garageContactStaffBd = $this->GarageContactStaff->save($garageContactStaffSave, array('fieldList' => $fields));
    //                     } else {
    //                         $garageContactStaff['GarageContactStaff']['priority'] = ConstantsBooleans::YES;

    //                         $fields = array('GarageContactStaff' => array('priority'));
    //                         $garageContactStaffBd = $this->GarageContactStaff->save($garageContactStaff, array('fieldList' => $fields));
    //                         if (!$garageContactStaffBd) {
    //                             CakeLog::write('script-crm', 'Update Contact Staff - Failed saving/updating garage contact staff at line: ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 } else {
    //                     // is only saved in the log and the saving process is continued
    //                     CakeLog::write('script-crm', 'Update Contact Staff - Garage not found on contact at line: ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm', 'Update Contact Staff - Contact not found at line: ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function notEmpty($text)
    // {
    //     return !empty($text) && $text != 'NULL';
    // }

    // /**
    //  * Update garages loaded by GV JSON with the Excel info.
    //  */
    // public function updateGVGarages()
    // {
    //     try {
    //         exit;
    //         ini_set('memory_limit', '3G');
    //         set_time_limit(30 * 60);

    //         CakeLog::config('script-crm-gv', array(
    //             'engine' => 'FileLog',
    //             'types' => array('info', 'error', 'warning'),
    //             'scopes' => array('script-crm-gv'),
    //             'file' => 'script-crm-gv.log',
    //         ));

    //         $file_path = "C:\Users\LISA.CANE\Documents\Alliance\Data_migration_file 26-12-2023.xlsx";
    //         App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
    //         $reader = PHPExcel_IOFactory::load($file_path);

    //         // Garage
    //         $this->loadGarageGVInfo($reader);

    //         $this->loadWorksAndServicesGVInfo($reader);

    //         $this->autoRender = false;

    //         echo ('FINALIZADO OK <br>');
    //         return true;
    //     } catch (Exception $e) {
    //         debug($e);
    //         $this->autoRender = false;
    //         echo ('FINALIZADO KO  <br>');
    //         return false;
    //     }
    // }

    // private function loadGarageGVInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Blad1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     $aagRegionId = Configure::read('AAG_REGION_ID_BENELUX');

    //     $cityClass = ClassRegistry::init('City');
    //     $postcodeProvinceClass = ClassRegistry::init('PostcodeProvince');
    //     $provinceClass = ClassRegistry::init('Province');
    //     $countryClass = ClassRegistry::init('Country');

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['B'])) {
    //             // search Garage by RefCode + Erp Id
    //             //$erp_id = Configure::read('ERP_IDS.'.$row['AM']); // Erp name: AX/SAP
    //             $garage = $this->Garage->findByRefCodeAndAagRegionId($row['AN'], $aagRegionId);

    //             if ($garage) {
    //                 $garage['Garage']['email'] = $this->notEmpty($row['C']) ? @array_shift(array_filter(explode(';',  $row['C']))) : null; //In some cases there are multiple emails
    //                 $garage['Garage']['web'] = $this->notEmpty($row['D']) ? $row['F'] : null;
    //                 $garage['Garage']['phone'] = $this->notEmpty($row['E']) ? $row['E'] : null;
    //                 $garage['Garage']['latitude'] = $this->notEmpty($row['AO']) ? $row['AO'] : null;
    //                 $garage['Garage']['longitude'] = $this->notEmpty($row['AP']) ? $row['AP'] : null;
    //                 $garage['Garage']['business_name'] = $this->notEmpty($row['AQ']) ? $row['AQ'] : null;
    //                 $garage['Garage']['town'] = $this->notEmpty($row['AR']) ? $row['AR'] : null;

    //                 if (!empty($garage['Garage']['town'])) {

    //                     $city = $cityClass->findCityByNameInRegion($garage['Garage']['town'], $aagRegionId);
    //                     if (!empty($city)) {
    //                         // city found
    //                         $garage['Garage']['city_id'] = $city['City']['id'];
    //                     } else {
    //                         // city not found, create it

    //                         $postcodeDigits = substr($garage['Garage']['postcode'], 0, 4);
    //                         $postcodeProvince = $postcodeProvinceClass->findPostcodeProvinceByPostcodeInRegion(
    //                             $postcodeDigits,
    //                             $aagRegionId
    //                         );

    //                         if ($postcodeProvince) {
    //                             $province = $provinceClass->findById($postcodeProvince['PostcodeProvince']['province_id']);

    //                             if ($province) {
    //                                 $country = $countryClass->findById($province['Province']['country_id']);
    //                                 //Gets latitude and longitude
    //                                 //$dataLatitudeLongitude = $garageClass->getLatitudeLongitude($country, $province, $cityName, $postcode);
    //                                 //echo "\n"."ciudad gmaps create";

    //                                 $cityArray = array(
    //                                     "City" => array(
    //                                         'name' => $garage['Garage']['town'],
    //                                         'latitude' => null, // $dataLatitudeLongitude['latitude'],
    //                                         'longitude' => null, // $dataLatitudeLongitude['longitude'],
    //                                         'province_id' => $postcodeProvince['PostcodeProvince']['province_id'],
    //                                     )
    //                                 );
    //                                 $city = $cityClass->add_city($cityArray);
    //                                 $garage['Garage']['city_id'] = $city['City']['id'];
    //                             } else {
    //                                 CakeLog::write('script-crm-gv', 'Province to create city not found at line ' . $key . PHP_EOL);
    //                             }
    //                         } else {
    //                             CakeLog::write('script-crm-gv', 'Postcode to create city not found at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 }

    //                 // get opening hours from column J JSON, in key werkplaats
    //                 $jsonOpeningHours = $this->notEmpty($row['J']) ? json_decode($row['J'], true) : null;
    //                 $jsonOpeningHours = !empty($jsonOpeningHours) && isset($jsonOpeningHours['werkplaats']) ? $jsonOpeningHours['werkplaats'] : null;
    //                 $openingHours = !empty($jsonOpeningHours) ? $this->getOpeningHours($jsonOpeningHours) : array();

    //                 if (!empty($openingHours)) {
    //                     $garage['Garage']['monday_open_1'] = $openingHours['monday_open_1'];
    //                     $garage['Garage']['monday_closed_1'] = $openingHours['monday_closed_1'];
    //                     $garage['Garage']['monday_open_2'] = $openingHours['monday_open_2'];
    //                     $garage['Garage']['monday_closed_2'] = $openingHours['monday_closed_2'];

    //                     $garage['Garage']['tuesday_open_1'] = $openingHours['tuesday_open_1'];
    //                     $garage['Garage']['tuesday_closed_1'] = $openingHours['tuesday_closed_1'];
    //                     $garage['Garage']['tuesday_open_2'] = $openingHours['tuesday_open_2'];
    //                     $garage['Garage']['tuesday_closed_2'] = $openingHours['tuesday_closed_2'];

    //                     $garage['Garage']['wednesday_open_1'] = $openingHours['wednesday_open_1'];
    //                     $garage['Garage']['wednesday_closed_1'] = $openingHours['wednesday_closed_1'];
    //                     $garage['Garage']['wednesday_open_2'] = $openingHours['wednesday_open_2'];
    //                     $garage['Garage']['wednesday_closed_2'] = $openingHours['wednesday_closed_2'];

    //                     $garage['Garage']['thursday_open_1'] = $openingHours['thursday_open_1'];
    //                     $garage['Garage']['thursday_closed_1'] = $openingHours['thursday_closed_1'];
    //                     $garage['Garage']['thursday_open_2'] = $openingHours['thursday_open_2'];
    //                     $garage['Garage']['thursday_closed_2'] = $openingHours['thursday_closed_2'];

    //                     $garage['Garage']['friday_open_1'] = $openingHours['friday_open_1'];
    //                     $garage['Garage']['friday_closed_1'] = $openingHours['friday_closed_1'];
    //                     $garage['Garage']['friday_open_2'] = $openingHours['friday_open_2'];
    //                     $garage['Garage']['friday_closed_2'] = $openingHours['friday_closed_2'];

    //                     $garage['Garage']['saturday_open_1'] = $openingHours['saturday_open_1'];
    //                     $garage['Garage']['saturday_closed_1'] = $openingHours['saturday_closed_1'];
    //                     $garage['Garage']['saturday_open_2'] = $openingHours['saturday_open_2'];
    //                     $garage['Garage']['saturday_closed_2'] = $openingHours['saturday_closed_2'];

    //                     $garage['Garage']['sunday_open_1'] = $openingHours['sunday_open_1'];
    //                     $garage['Garage']['sunday_closed_1'] = $openingHours['sunday_closed_1'];
    //                     $garage['Garage']['sunday_open_2'] = $openingHours['sunday_open_2'];
    //                     $garage['Garage']['sunday_closed_2']  = $openingHours['sunday_closed_2'];
    //                 }

    //                 $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

    //                 $fields = array('Garage' => array('email', 'web', 'phone', 'latitude', 'longitude', 'business_name', 'town', 'city_id', 'monday_open_1', 'monday_closed_1', 'monday_open_2', 'monday_closed_2', 'tuesday_open_1', 'tuesday_closed_1', 'tuesday_open_2', 'tuesday_closed_2', 'wednesday_open_1', 'wednesday_closed_1', 'wednesday_open_2', 'wednesday_closed_2', 'thursday_open_1', 'thursday_closed_1', 'thursday_open_2', 'thursday_closed_2', 'friday_open_1', 'friday_closed_1', 'friday_open_2', 'friday_closed_2', 'saturday_open_1', 'saturday_closed_1', 'saturday_open_2', 'saturday_closed_2', 'sunday_open_1', 'sunday_closed_1', 'sunday_open_2', 'sunday_closed_2', 'modification_date'));

    //                 // remove email validation
    //                 $this->Garage->validator()->remove('email');
    //                 $garageBd = $this->Garage->save($garage, array('fieldList' => $fields));

    //                 // only if Garage is well saved and opening hours, Kiyoh location ID, Kiyoh API KEY or about wasn't empty,
    //                 // GV GarageNetwork is searched and updated
    //                 if ($garageBd && (!empty($openingHours) || $this->notEmpty($row['F']) || $this->notEmpty($row['G']) || $this->notEmpty($row['K']))) {

    //                     $this->GarageNetwork->create();

    //                     $garageNetwork = array(
    //                         'GarageNetwork' => array(
    //                             'garage_id' => $garageBd['Garage']['id'],
    //                             'network_id' => NETWORK_ID_GV,
    //                             'status' => 5,
    //                             'last' => 1,
    //                             'booking_days_min_from' => 0,
    //                             'booking_days_max_to' => 30,
    //                             'location_id' => $this->notEmpty($row['F']) ? $row['F'] : null,
    //                             'kiyoh_api_key' => $this->notEmpty($row['G']) ? $row['G'] : null,
    //                             'about' => $this->notEmpty($row['K']) ? $row['K'] : null,
    //                             'quoting_active' => ConstantsBooleans::ACTIVE,
    //                             'enquiries_active' => ConstantsBooleans::ACTIVE
    //                         )
    //                     );

    //                     if (!empty($openingHours)) {
    //                         $garageNetwork['GarageNetwork']['monday_planner_open_1'] = $openingHours['monday_open_1'];
    //                         $garageNetwork['GarageNetwork']['monday_planner_closed_1'] = $openingHours['monday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['monday_planner_open_2'] = $openingHours['monday_open_2'];
    //                         $garageNetwork['GarageNetwork']['monday_planner_closed_2'] = $openingHours['monday_closed_2'];

    //                         $garageNetwork['GarageNetwork']['tuesday_planner_open_1'] = $openingHours['tuesday_open_1'];
    //                         $garageNetwork['GarageNetwork']['tuesday_planner_closed_1'] = $openingHours['tuesday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['tuesday_planner_open_2'] = $openingHours['tuesday_open_2'];
    //                         $garageNetwork['GarageNetwork']['tuesday_planner_closed_2'] = $openingHours['tuesday_closed_2'];

    //                         $garageNetwork['GarageNetwork']['wednesday_planner_open_1'] = $openingHours['wednesday_open_1'];
    //                         $garageNetwork['GarageNetwork']['wednesday_planner_closed_1'] = $openingHours['wednesday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['wednesday_planner_open_2'] = $openingHours['wednesday_open_2'];
    //                         $garageNetwork['GarageNetwork']['wednesday_planner_closed_2'] = $openingHours['wednesday_closed_2'];

    //                         $garageNetwork['GarageNetwork']['thursday_planner_open_1'] = $openingHours['thursday_open_1'];
    //                         $garageNetwork['GarageNetwork']['thursday_planner_closed_1'] = $openingHours['thursday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['thursday_planner_open_2'] = $openingHours['thursday_open_2'];
    //                         $garageNetwork['GarageNetwork']['thursday_planner_closed_2'] = $openingHours['thursday_closed_2'];

    //                         $garageNetwork['GarageNetwork']['friday_planner_open_1'] = $openingHours['friday_open_1'];
    //                         $garageNetwork['GarageNetwork']['friday_planner_closed_1'] = $openingHours['friday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['friday_planner_open_2'] = $openingHours['friday_open_2'];
    //                         $garageNetwork['GarageNetwork']['friday_planner_closed_2'] = $openingHours['friday_closed_2'];

    //                         $garageNetwork['GarageNetwork']['saturday_planner_open_1'] = $openingHours['saturday_open_1'];
    //                         $garageNetwork['GarageNetwork']['saturday_planner_closed_1'] = $openingHours['saturday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['saturday_planner_open_2'] = $openingHours['saturday_open_2'];
    //                         $garageNetwork['GarageNetwork']['saturday_planner_closed_2'] = $openingHours['saturday_closed_2'];

    //                         $garageNetwork['GarageNetwork']['sunday_planner_open_1'] = $openingHours['sunday_open_1'];
    //                         $garageNetwork['GarageNetwork']['sunday_planner_closed_1'] = $openingHours['sunday_closed_1'];
    //                         $garageNetwork['GarageNetwork']['sunday_planner_open_2'] = $openingHours['sunday_open_2'];
    //                         $garageNetwork['GarageNetwork']['sunday_planner_closed_2']  = $openingHours['sunday_closed_2'];
    //                     }

    //                     $garageNetwork['GarageNetwork']['creation_date'] = date('Y-m-d H:i:s');
    //                     $garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

    //                     $fields = array('GarageNetwork' => array('garage_id', 'network_id', 'status', 'last', 'booking_days_min_from', 'booking_days_max_to', 'location_id', 'kiyoh_api_key', 'about', 'quoting_active', 'enquiries_active', 'monday_planner_open_1', 'monday_planner_closed_1', 'monday_planner_open_2', 'monday_planner_closed_2', 'tuesday_planner_open_1', 'tuesday_planner_closed_1', 'tuesday_planner_open_2', 'tuesday_planner_closed_2', 'wednesday_planner_open_1', 'wednesday_planner_closed_1', 'wednesday_planner_open_2', 'wednesday_planner_closed_2', 'thursday_planner_open_1', 'thursday_planner_closed_1', 'thursday_planner_open_2', 'thursday_planner_closed_2', 'friday_planner_open_1', 'friday_planner_closed_1', 'friday_planner_open_2', 'friday_planner_closed_2', 'saturday_planner_open_1', 'saturday_planner_closed_1', 'saturday_planner_open_2', 'saturday_planner_closed_2', 'sunday_planner_open_1', 'sunday_planner_closed_1', 'sunday_planner_open_2', 'sunday_planner_closed_2', 'creation_date', 'modification_date'));
    //                     $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));

    //                     if (!$garageNetworkBd) {
    //                         CakeLog::write('script-crm-gv', 'Failed saving/updating garage network info at line ' . $key . PHP_EOL);
    //                     }
    //                 } else {
    //                     CakeLog::write('script-crm-gv', 'Failed saving/updating garage info at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 // is only saved in the log and the saving process is continued
    //                 CakeLog::write('script-crm-gv', 'Garage not found at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column B is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function getOpeningHours($jsonOpeningHours)
    // {
    //     $openingHours = array();

    //     // monday
    //     $mondayArray = $this->getDayOpeningHours($jsonOpeningHours['mon']);
    //     $openingHours['monday_open_1'] = !empty($mondayArray) && isset($mondayArray['open_1']) ? $mondayArray['open_1'] : null;
    //     $openingHours['monday_closed_1'] = !empty($mondayArray) && isset($mondayArray['closed_1']) ? $mondayArray['closed_1'] : null;
    //     $openingHours['monday_open_2'] = !empty($mondayArray) && isset($mondayArray['open_2']) ? $mondayArray['open_2'] : null;
    //     $openingHours['monday_closed_2']  = !empty($mondayArray) && isset($mondayArray['closed_2']) ? $mondayArray['closed_2'] : null;

    //     // tuesday
    //     $tuesdayArray = $this->getDayOpeningHours($jsonOpeningHours['tue']);
    //     $openingHours['tuesday_open_1'] = !empty($tuesdayArray) && isset($tuesdayArray['open_1']) ? $tuesdayArray['open_1'] : null;
    //     $openingHours['tuesday_closed_1'] = !empty($tuesdayArray) && isset($tuesdayArray['closed_1']) ? $tuesdayArray['closed_1'] : null;
    //     $openingHours['tuesday_open_2'] = !empty($tuesdayArray) && isset($tuesdayArray['open_2']) ? $tuesdayArray['open_2'] : null;
    //     $openingHours['tuesday_closed_2']  = !empty($tuesdayArray) && isset($tuesdayArray['closed_2']) ? $tuesdayArray['closed_2'] : null;

    //     // wednesday
    //     $wednesdayArray = $this->getDayOpeningHours($jsonOpeningHours['wed']);
    //     $openingHours['wednesday_open_1'] = !empty($wednesdayArray) && isset($wednesdayArray['open_1']) ? $wednesdayArray['open_1'] : null;
    //     $openingHours['wednesday_closed_1'] = !empty($wednesdayArray) && isset($wednesdayArray['closed_1']) ? $wednesdayArray['closed_1'] : null;
    //     $openingHours['wednesday_open_2'] = !empty($wednesdayArray) && isset($wednesdayArray['open_2']) ? $wednesdayArray['open_2'] : null;
    //     $openingHours['wednesday_closed_2']  = !empty($wednesdayArray) && isset($wednesdayArray['closed_2']) ? $wednesdayArray['closed_2'] : null;

    //     // thursday
    //     $thursdayArray = $this->getDayOpeningHours($jsonOpeningHours['thu']);
    //     $openingHours['thursday_open_1'] = !empty($thursdayArray) && isset($thursdayArray['open_1']) ? $thursdayArray['open_1'] : null;
    //     $openingHours['thursday_closed_1'] = !empty($thursdayArray) && isset($thursdayArray['closed_1']) ? $thursdayArray['closed_1'] : null;
    //     $openingHours['thursday_open_2'] = !empty($thursdayArray) && isset($thursdayArray['open_2']) ? $thursdayArray['open_2'] : null;
    //     $openingHours['thursday_closed_2']  = !empty($thursdayArray) && isset($thursdayArray['closed_2']) ? $thursdayArray['closed_2'] : null;

    //     // friday
    //     $fridayArray = $this->getDayOpeningHours($jsonOpeningHours['fri']);
    //     $openingHours['friday_open_1'] = !empty($fridayArray) && isset($fridayArray['open_1']) ? $fridayArray['open_1'] : null;
    //     $openingHours['friday_closed_1'] = !empty($fridayArray) && isset($fridayArray['closed_1']) ? $fridayArray['closed_1'] : null;
    //     $openingHours['friday_open_2'] = !empty($fridayArray) && isset($fridayArray['open_2']) ? $fridayArray['open_2'] : null;
    //     $openingHours['friday_closed_2']  = !empty($fridayArray) && isset($fridayArray['closed_2']) ? $fridayArray['closed_2'] : null;

    //     // saturday
    //     $saturdayArray = $this->getDayOpeningHours($jsonOpeningHours['sat']);
    //     $openingHours['saturday_open_1'] = !empty($saturdayArray) && isset($saturdayArray['open_1']) ? $saturdayArray['open_1'] : null;
    //     $openingHours['saturday_closed_1'] = !empty($saturdayArray) && isset($saturdayArray['closed_1']) ? $saturdayArray['closed_1'] : null;
    //     $openingHours['saturday_open_2'] = !empty($saturdayArray) && isset($saturdayArray['open_2']) ? $saturdayArray['open_2'] : null;
    //     $openingHours['saturday_closed_2']  = !empty($saturdayArray) && isset($saturdayArray['closed_2']) ? $saturdayArray['closed_2'] : null;

    //     // sunday
    //     $sundayArray = $this->getDayOpeningHours($jsonOpeningHours['sun']);
    //     $openingHours['sunday_open_1'] = !empty($sundayArray) && isset($sundayArray['open_1']) ? $sundayArray['open_1'] : null;
    //     $openingHours['sunday_closed_1'] = !empty($sundayArray) && isset($sundayArray['closed_1']) ? $sundayArray['closed_1'] : null;
    //     $openingHours['sunday_open_2'] = !empty($sundayArray) && isset($sundayArray['open_2']) ? $sundayArray['open_2'] : null;
    //     $openingHours['sunday_closed_2']  = !empty($sundayArray) && isset($sundayArray['closed_2']) ? $sundayArray['closed_2'] : null;

    //     return $openingHours;
    // }

    // private function getDayOpeningHours($day)
    // {
    //     $dayArray = array();

    //     $dayFrom = isset($day['from']) && !empty($day['from']) ? $day['from'] : null;
    //     if (!empty($dayFrom)) {
    //         // if from contains "-" it means that it was open_1 and closed_1
    //         if (strpos($dayFrom, '-') !== false) {
    //             $range = explode('-', $dayFrom);
    //             $open1 = str_replace('.', ':', $range[0]);
    //             $open1 = str_replace(';', ':', $open1);
    //             $dayArray['open_1'] = Fecha::isHour($open1) ? $open1 : null;

    //             $closed1 = str_replace('.', ':', $range[1]);
    //             $closed1 = str_replace(';', ':', $closed1);
    //             $dayArray['closed_1'] = Fecha::isHour($closed1) ? $closed1 : null;
    //         } else {
    //             $open1 = str_replace('.', ':', $dayFrom);
    //             $open1 = str_replace(';', ':', $open1);
    //             $dayArray['open_1'] = Fecha::isHour($open1) ? $open1 : null;
    //         }
    //     }

    //     $dayTo = isset($day['to']) && !empty($day['to']) ? $day['to'] : null;
    //     if (!empty($dayTo)) {
    //         // if to contains "-" it means that it was open_2 and closed_2
    //         if (strpos($dayTo, '-') !== false) {
    //             $range = explode('-', $dayTo);
    //             $open2 = str_replace('.', ':', $range[0]);
    //             $open2 = str_replace(';', ':', $open2);
    //             $dayArray['open_2'] = Fecha::isHour($open2) ? $open2 : null;

    //             $closed2 = str_replace('.', ':', $range[1]);
    //             $closed2 = str_replace(';', ':', $closed2);
    //             $dayArray['closed_2'] = Fecha::isHour($closed2) ? $closed2 : null;
    //         } else {
    //             $closed1 = str_replace('.', ':', $dayTo);
    //             $closed1 = str_replace(';', ':', $closed1);
    //             $dayArray['closed_1'] = Fecha::isHour($closed1) ? $closed1 : null;
    //         }
    //     }

    //     return $dayArray;
    // }

    // private function loadWorksAndServicesGVInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Blad1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     // get works by name_nl specified in column names of Excel
    //     $workColumnL = $this->Work->findByNameNlAndNetworkIdAndActive('Aankoopkeuring', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnL) {
    //         CakeLog::write('script-crm-gv', 'Work with name Aankoopkeuring not found' . PHP_EOL);
    //     }
    //     $workColumnM = $this->Work->findByNameNlAndNetworkIdAndActive('Accu vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnM) {
    //         CakeLog::write('script-crm-gv', 'Work with name Accu vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnN = $this->Work->findByNameNlAndNetworkIdAndActive('Airco Service', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnN) {
    //         CakeLog::write('script-crm-gv', 'Work with name Airco Service not found' . PHP_EOL);
    //     }
    //     $workColumnO = $this->Work->findByNameNlAndNetworkIdAndActive('APK (Benzine)', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnO) {
    //         CakeLog::write('script-crm-gv', 'Work with name APK (Benzine) not found' . PHP_EOL);
    //     }
    //     $workColumnP = $this->Work->findByNameNlAndNetworkIdAndActive('APK (Diesel)', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnP) {
    //         CakeLog::write('script-crm-gv', 'Work with name APK (Diesel) not found' . PHP_EOL);
    //     }
    //     $workColumnQ = $this->Work->findByNameNlAndNetworkIdAndActive('APK (Elektrisch)', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnQ) {
    //         CakeLog::write('script-crm-gv', 'Work with name APK (Elektrisch) not found' . PHP_EOL);
    //     }
    //     $workColumnR = $this->Work->findByNameNlAndNetworkIdAndActive('Bandenwissel', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnR) {
    //         CakeLog::write('script-crm-gv', 'Work with name Bandenwissel not found' . PHP_EOL);
    //     }
    //     $workColumnS = $this->Work->findByNameNlAndNetworkIdAndActive('Distributieriemset + waterpomp vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnS) {
    //         CakeLog::write('script-crm-gv', 'Work with name Distributieriemset + waterpomp vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnT = $this->Work->findByNameNlAndNetworkIdAndActive('Distributieriemset vernieuwen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnT) {
    //         CakeLog::write('script-crm-gv', 'Work with name Distributieriemset vernieuwen not found' . PHP_EOL);
    //     }
    //     $workColumnU = $this->Work->findByNameNlAndNetworkIdAndActive('Grote onderhoudsbeurt (inclusief gratis APK)', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnU) {
    //         CakeLog::write('script-crm-gv', 'Work with name Grote onderhoudsbeurt (inclusief gratis APK) not found' . PHP_EOL);
    //     }
    //     $workColumnV = $this->Work->findByNameNlAndNetworkIdAndActive('Kleine onderhoudsbeurt', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // dos veces (Kleine onderhoudsbeurt - Kleine Onderhoudsbeurt)
    //     if (!$workColumnV) {
    //         CakeLog::write('script-crm-gv', 'Work with name Kleine onderhoudsbeurt not found' . PHP_EOL);
    //     }
    //     $workColumnW = $this->Work->findByNameNlAndNetworkIdAndActive('Koppeling vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnW) {
    //         CakeLog::write('script-crm-gv', 'Work with name Koppeling vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnX = $this->Work->findByNameNlAndNetworkIdAndActive('Remblokken achter vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnX) {
    //         CakeLog::write('script-crm-gv', 'Work with name Remblokken achter vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnY = $this->Work->findByNameNlAndNetworkIdAndActive('Remblokken voor vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnY) {
    //         CakeLog::write('script-crm-gv', 'Work with name Remblokken voor vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnZ = $this->Work->findByNameNlAndNetworkIdAndActive('Remschijven en remblokken voor venieuwen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnZ) {
    //         CakeLog::write('script-crm-gv', 'Work with name Remschijven en remblokken voor venieuwen not found' . PHP_EOL);
    //     }
    //     $workColumnAA = $this->Work->findByNameNlAndNetworkIdAndActive('Remschijven en remblokken achter venieuwen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnAA) {
    //         CakeLog::write('script-crm-gv', 'Work with name Remschijven en remblokken achter venieuwen not found' . PHP_EOL);
    //     }
    //     $workColumnAB = $this->Work->findByNameNlAndNetworkIdAndActive('Ruitenwisserblad achterzijde vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnAB) {
    //         CakeLog::write('script-crm-gv', 'Work with name Ruitenwisserblad achterzijde vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnAC = $this->Work->findByNameNlAndNetworkIdAndActive('Ruitenwisserbladen voorzijde vervangen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnAC) {
    //         CakeLog::write('script-crm-gv', 'Work with name Ruitenwisserbladen voorzijde vervangen not found' . PHP_EOL);
    //     }
    //     $workColumnAD = $this->Work->findByNameNlAndNetworkIdAndActive('Schokdempers vóór venieuwen', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnAD) {
    //         CakeLog::write('script-crm-gv', 'Work with name Schokdempers vóór venieuwen not found' . PHP_EOL);
    //     }
    //     $workColumnAE = $this->Work->findByNameNlAndNetworkIdAndActive('Schokdempers voorzijde', NETWORK_ID_GV, ConstantsBooleans::ACTIVE); // no bd
    //     if (!$workColumnAE) {
    //         CakeLog::write('script-crm-gv', 'Work with name Schokdempers voorzijde not found' . PHP_EOL);
    //     }
    //     $workColumnAF = $this->Work->findByNameNlAndNetworkIdAndActive('Winter Check', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnAF) {
    //         CakeLog::write('script-crm-gv', 'Work with name Winter Check not found' . PHP_EOL);
    //     }
    //     $workColumnAG = $this->Work->findByNameNlAndNetworkIdAndActive('Zomer Check', NETWORK_ID_GV, ConstantsBooleans::ACTIVE);
    //     if (!$workColumnAG) {
    //         CakeLog::write('script-crm-gv', 'Work with name Zomer Check not found' . PHP_EOL);
    //     }

    //     // get services by name_nl specified in column names of Excel
    //     $serviceColumnAH = $this->ServiceDriver->findByNameNlAndNetworkId('Haal- en brengservice', NETWORK_ID_GV);
    //     if (!$serviceColumnAH) {
    //         CakeLog::write('script-crm-gv', 'Service with name Haal- en brengservice not found' . PHP_EOL);
    //     }

    //     $serviceColumnAI = $this->ServiceDriver->findByNameNlAndNetworkId('Leenauto', NETWORK_ID_GV);
    //     if (!$serviceColumnAI) {
    //         CakeLog::write('script-crm-gv', 'Service with name Leenauto not found' . PHP_EOL);
    //     }

    //     $serviceColumnAJ = $this->ServiceDriver->findByNameNlAndNetworkId('Leenfiets', NETWORK_ID_GV);
    //     if (!$serviceColumnAJ) {
    //         CakeLog::write('script-crm-gv', 'Service with name Leenfiets not found' . PHP_EOL);
    //     }

    //     $serviceColumnAK = $this->ServiceDriver->findByNameNlAndNetworkId('Leenscooter', NETWORK_ID_GV);
    //     if (!$serviceColumnAK) {
    //         CakeLog::write('script-crm-gv', 'Service with name Leenscooter not found' . PHP_EOL);
    //     }

    //     $serviceColumnAL = $this->ServiceDriver->findByNameNlAndNetworkId('WiFi beschikbaar', NETWORK_ID_GV);
    //     if (!$serviceColumnAL) {
    //         CakeLog::write('script-crm-gv', 'Service with name WiFi beschikbaar not found' . PHP_EOL);
    //     }

    //     $aagRegionId = Configure::read('AAG_REGION_ID_BENELUX');

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['B'])) {
    //             // search Garage by RefCode + Erp Id
    //             //$erp_id = Configure::read('ERP_IDS.'.$row['AM']); // Erp name: AX/SAP
    //             $garage = $this->Garage->findByRefCodeAndAagRegionId($row['AN'], $aagRegionId);

    //             if ($garage) {
    //                 // search GV GarageNetwork
    //                 $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garage['Garage']['id'], NETWORK_ID_GV);

    //                 if ($garageNetwork) {
    //                     $garageNetworkId = $garageNetwork['GarageNetwork']['id'];

    //                     // save GarageNetworkWorkLabour if column isn't empty
    //                     if (!empty($row['L']) && $workColumnL) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnL['Work']['id'], $row['L'], 'L', $key);
    //                     }

    //                     if (!empty($row['M']) && $workColumnM) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnM['Work']['id'], $row['M'], 'M', $key);
    //                     }

    //                     if (!empty($row['N']) && $workColumnN) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnN['Work']['id'], $row['N'], 'N', $key);
    //                     }

    //                     if (!empty($row['O']) && $workColumnO) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnO['Work']['id'], $row['O'], 'O', $key);
    //                     }

    //                     if (!empty($row['P']) && $workColumnP) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnP['Work']['id'], $row['P'], 'P', $key);
    //                     }

    //                     if (!empty($row['Q']) && $workColumnQ) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnQ['Work']['id'], $row['Q'], 'Q', $key);
    //                     }

    //                     if (!empty($row['R']) && $workColumnR) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnR['Work']['id'], $row['R'], 'R', $key);
    //                     }

    //                     if (!empty($row['S']) && $workColumnS) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnS['Work']['id'], $row['S'], 'S', $key);
    //                     }

    //                     if (!empty($row['T']) && $workColumnT) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnT['Work']['id'], $row['T'], 'T', $key);
    //                     }

    //                     if (!empty($row['U']) && $workColumnU) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnU['Work']['id'], $row['U'], 'U', $key);
    //                     }

    //                     if (!empty($row['V']) && $workColumnV) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnV['Work']['id'], $row['V'], 'V', $key);
    //                     }

    //                     if (!empty($row['W']) && $workColumnW) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnW['Work']['id'], $row['W'], 'W', $key);
    //                     }

    //                     if (!empty($row['X']) && $workColumnX) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnX['Work']['id'], $row['X'], 'X', $key);
    //                     }

    //                     if (!empty($row['Y']) && $workColumnY) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnY['Work']['id'], $row['Y'], 'Y', $key);
    //                     }

    //                     if (!empty($row['Z']) && $workColumnZ) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnZ['Work']['id'], $row['Z'], 'Z', $key);
    //                     }

    //                     if (!empty($row['AA']) && $workColumnAA) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAA['Work']['id'], $row['AA'], 'AA', $key);
    //                     }

    //                     if (!empty($row['AB']) && $workColumnAB) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAB['Work']['id'], $row['AB'], 'AB', $key);
    //                     }

    //                     if (!empty($row['AC']) && $workColumnAC) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAC['Work']['id'], $row['AC'], 'AC', $key);
    //                     }

    //                     if (!empty($row['AD']) && $workColumnAD) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAD['Work']['id'], $row['AD'], 'AD', $key);
    //                     }

    //                     if (!empty($row['AE']) && $workColumnAE) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAE['Work']['id'], $row['AE'], 'AE', $key);
    //                     }

    //                     if (!empty($row['AF']) && $workColumnAF) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAF['Work']['id'], $row['AF'], 'AF', $key);
    //                     }

    //                     if (!empty($row['AG']) && $workColumnAG) {
    //                         $this->saveGarageNetworkWorkLabour($garageNetworkId, $workColumnAG['Work']['id'], $row['AG'], 'AG', $key);
    //                     }

    //                     // save GarageNetworkLabour if column isn't empty
    //                     if (!empty($row['AH']) && $serviceColumnAH) {
    //                         $this->saveGarageNetworkService($garageNetworkId, $serviceColumnAH['ServiceDriver']['id'], 'AH', $key);
    //                     }

    //                     if (!empty($row['AI']) && $serviceColumnAI) {
    //                         $this->saveGarageNetworkService($garageNetworkId, $serviceColumnAI['ServiceDriver']['id'], 'AI', $key);
    //                     }

    //                     if (!empty($row['AJ']) && $serviceColumnAJ) {
    //                         $this->saveGarageNetworkService($garageNetworkId, $serviceColumnAJ['ServiceDriver']['id'], 'AJ', $key);
    //                     }

    //                     if (!empty($row['AK']) && $serviceColumnAK) {
    //                         $this->saveGarageNetworkService($garageNetworkId, $serviceColumnAK['ServiceDriver']['id'], 'AK', $key);
    //                     }

    //                     if (!empty($row['AL']) && $serviceColumnAL) {
    //                         $this->saveGarageNetworkService($garageNetworkId, $serviceColumnAL['ServiceDriver']['id'], 'AL', $key);
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // private function saveGarageNetworkWorkLabour($garageNetworkId, $workId, $price, $column, $row)
    // {
    //     $this->GarageNetworkWorkLabour->create();

    //     $garageNetworkWorkLabour['GarageNetworkWorkLabour']['garage_network_id'] = $garageNetworkId;
    //     $garageNetworkWorkLabour['GarageNetworkWorkLabour']['work_id'] = $workId;
    //     $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price'] = $price;
    //     $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles'] = $price;

    //     if (!$this->GarageNetworkWorkLabour->save($garageNetworkWorkLabour)) {
    //         CakeLog::write('script-crm-gv', 'Failed saving garage network labour (column ' . $column . ') at line ' . $row . PHP_EOL);
    //     }

    //     $this->saveGarageNetworkWork($garageNetworkId, $workId, $column, $row);
    // }

    // private function saveGarageNetworkWork($garageNetworkId, $workId, $column, $row)
    // {
    //     $this->GarageNetworkWork->create();

    //     $garageNetworkWork['GarageNetworkWork']['garage_network_id'] = $garageNetworkId;
    //     $garageNetworkWork['GarageNetworkWork']['work_id'] = $workId;

    //     if (!$this->GarageNetworkWork->save($garageNetworkWork)) {
    //         CakeLog::write('script-crm-gv', 'Failed saving garage network work (column ' . $column . ') at line ' . $row . PHP_EOL);
    //     }
    // }

    // private function saveGarageNetworkService($garageNetworkId, $serviceId, $column, $row)
    // {
    //     $this->GarageNetworkServiceDriver->create();

    //     $garageNetworkServiceDriver['GarageNetworkServiceDriver']['garage_network_id'] = $garageNetworkId;
    //     $garageNetworkServiceDriver['GarageNetworkServiceDriver']['service_driver_id'] = $serviceId;

    //     if (!$this->GarageNetworkServiceDriver->save($garageNetworkServiceDriver)) {
    //         CakeLog::write('script-crm-gv', 'Failed saving garage network service driver (column ' . $column . ') at line ' . $row . PHP_EOL);
    //     }
    // }

    // /**
    //  * Create garages_networks loaded by GV data with the Excel info.
    //  */
    // public function createUpdateGaragesNetworksGV()
    // {
    //     try {
    //         exit;
    //         ini_set('memory_limit', '3G');
    //         set_time_limit(30 * 60);

    //         CakeLog::config('script-update-crm-gv', array(
    //             'engine' => 'FileLog',
    //             'types' => array('info', 'error', 'warning'),
    //             'scopes' => array('script-update-crm-gv'),
    //             'file' => 'script-update-crm-gv.log',
    //         ));

    //         $file_path = "C:\Users\MARIA.ZORITA\Documents\Alliance\Network_migration_file_DEF.xlsx";
    //         App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
    //         $reader = PHPExcel_IOFactory::load($file_path);

    //         // Garages Networks GV
    //         $this->loadGaragesNetworksGVInfo($reader);
    //         $this->autoRender = false;
    //         echo 'FINALIZADO OK <br>';
    //         return true;
    //     } catch (Exception $e) {
    //         debug($e);
    //         $this->autoRender = false;
    //         echo 'FINALIZADO KO  <br>';
    //         return false;
    //     }
    // }

    // private function loadGaragesNetworksGVInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Blad1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A'])) {
    //             // Check if exist garage in Bd by RefCode + Erp Id + RegionId
    //             //$erp_id = Configure::read('ERP_IDS.'.$row['C']); // Erp name: AX/SAP
    //             $garageBd = $this->Garage->findByRefCodeAndAagRegionId($row['D'], Configure::read('AAG_REGION_ID_BENELUX'));
    //             if ($garageBd && !empty($row['B'])) {
    //                 // Check if exist network in Bd by Name
    //                 $networkBd = $this->Network->findByName($row['B']);
    //                 if ($networkBd) {
    //                     // Check if exist relation in garages_networks table in BD
    //                     $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garageBd['Garage']['id'], $networkBd['Network']['id']);
    //                     if (!$garageNetwork) {
    //                         $this->GarageNetwork->create();

    //                         $garageNetwork = array(
    //                             'GarageNetwork' => array(
    //                                 'garage_id' => $garageBd['Garage']['id'],
    //                                 'network_id' => $networkBd['Network']['id'],
    //                                 'status' => ConstantsNetworksStatus::LIVE,
    //                                 'last' => 1,
    //                                 'imported' => 1,
    //                                 'creation_date' => date('Y-m-d H:i:s'),
    //                                 'modification_date' => date('Y-m-d H:i:s')
    //                             )
    //                         );

    //                         $fields = array('GarageNetwork' => array('garage_id', 'network_id', 'status', 'last', 'imported', 'creation_date', 'modification_date'));
    //                         $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));

    //                         if (!$garageNetworkBd) {
    //                             CakeLog::write('script-update-crm-gv', 'Failed saving garage network info at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 } else {
    //                     CakeLog::write('script-update-crm-gv', 'Network not found at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 CakeLog::write('script-update-crm-gv', 'Garage not found or column B empty at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // /**
    //  * Updates/creates garages_networks loaded by UK+Ireland data with the Excel info.
    //  */
    // public function createUpdateGaragesNetworksUK()
    // {
    //     try {
    //         //exit;
    //         ini_set('memory_limit', '3G');
    //         set_time_limit(30 * 60);

    //         CakeLog::config('script-update-dd_active-uk', array(
    //             'engine' => 'FileLog',
    //             'types' => array('info', 'error', 'warning'),
    //             'scopes' => array('script-update-dd_active-uk'),
    //             'file' => 'script-update-dd_active-uk.log',
    //         ));

    //         //Change this path to your current file location.
    //         $file_path = "C:\Users\GonzaloJuezPrádanosS\Documents\Alliance\DDACTIVE2.xlsx";
    //         App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
    //         $reader = PHPExcel_IOFactory::load($file_path);

    //         // Garages Networks UK+Ireland
    //         $this->loadGaragesNetworksUKInfo($reader);
    //         $this->autoRender = false;
    //         echo 'FINALIZADO OK <br>';
    //         return true;
    //     } catch (Exception $e) {
    //         debug($e);
    //         $this->autoRender = false;
    //         echo 'FINALIZADO KO  <br>';
    //         return false;
    //     }
    // }

    // private function loadGaragesNetworksUKInfo($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Sheet1')->toArray(null, true, true, true);
    //     unset($file[1]);

    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         //Check garage by gnumber + aag_region_id uk+ireland
    //         if (!empty($row['A'])) {
    //             $garageBd = $this->Garage->findByGNumberIdAndAagRegionId($row['C'], Configure::read('AAG_REGION_ID_UK_IRELAND'));

    //             //Check network by  name
    //             if ($garageBd && !empty($row['D'])) {
    //                 $networkBd = $this->Network->findByName($row['D']);

    //                 //Check garage_network by ids
    //                 if ($networkBd) {
    //                     $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkIdAndLast($garageBd['Garage']['id'], $networkBd['Network']['id'], true);

    //                     //Update garage_network dd_active if exists
    //                     if ($garageNetwork) {
    //                         $garageNetwork['GarageNetwork']['dd_active'] = $row['E'];
    //                         $garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

    //                         $fields = array('GarageNetwork' => array('dd_active', 'modification_date'));

    //                         $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));

    //                         if (!$garageNetworkBd) {
    //                             CakeLog::write('script-update-dd_active-uk', 'Failed updating garage network info at line ' . $key . PHP_EOL);
    //                         }
    //                     }

    //                     //Create garage_network if not exists
    //                     if (!$garageNetwork) {
    //                         $this->GarageNetwork->create();

    //                         $garageNetwork = array(
    //                             'GarageNetwork' => array(
    //                                 'garage_id' => $garageBd['Garage']['id'],
    //                                 'network_id' => $networkBd['Network']['id'],
    //                                 'status' => ConstantsNetworksStatus::LIVE,
    //                                 'last' => 1,
    //                                 'dd_active' => $row['E'],
    //                                 'imported' => 1,
    //                                 'creation_date' => date('Y-m-d H:i:s'),
    //                                 'modification_date' => date('Y-m-d H:i:s')
    //                             )
    //                         );

    //                         $fields = array('GarageNetwork' => array('garage_id', 'network_id', 'status', 'last', 'dd_active', 'imported', 'creation_date', 'modification_date'));

    //                         $garageNetworkBd = $this->GarageNetwork->save($garageNetwork, array('fieldList' => $fields));

    //                         if (!$garageNetworkBd) {
    //                             CakeLog::write('script-update-dd_active-uk', 'Failed saving garage network info at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 } else {
    //                     CakeLog::write('script-update-dd_active-uk', 'Network not found at line ' . $key . PHP_EOL);
    //                 }
    //             } else {
    //                 CakeLog::write('script-update-dd_active-uk', 'Garage not found or column D empty at line ' . $key . PHP_EOL);
    //             }
    //         } else {
    //             // if column A is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // /**
    //  * Import images garages, garages_networks loaded by GV data with the Excel info.
    //  */
    // public function importGaragesImagesGV()
    // {
    //     try {
    //         exit;
    //         ini_set('memory_limit', '3G');
    //         set_time_limit(30 * 60);

    //         CakeLog::config('script-load-images-crm-gv', array(
    //             'engine' => 'FileLog',
    //             'types' => array('info', 'error', 'warning'),
    //             'scopes' => array('script-load-images-crm-gv'),
    //             'file' => 'script-load-images-crm-gv.log',
    //         ));

    //         $file_path = "C:/Users/MARIA.ZORITA/Documents/GV/Data_migration_file_26-12-2023.xlsx";
    //         App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
    //         $reader = PHPExcel_IOFactory::load($file_path);

    //         // Garages Networks GV
    //         $this->loadGaragesImagesGV($reader);
    //         $this->autoRender = false;
    //         echo 'FINALIZADO OK <br>';
    //         return true;
    //     } catch (Exception $e) {
    //         debug($e);
    //         $this->autoRender = false;
    //         echo 'FINALIZADO KO  <br>';
    //         return false;
    //     }
    // }

    // private function loadGaragesImagesGV($reader)
    // {
    //     $file = $reader->setActiveSheetIndexByName('Blad1')->toArray(null, true, true, true);
    //     $file_path_images = "C:/Users/MARIA.ZORITA/Documents/GV";
    //     unset($file[1]);
    //     $result = true;
    //     $this->begin();

    //     foreach ($file as $key => $row) {
    //         if (!empty($row['A']) && !empty($row['B']) && !empty($row['C'])) {
    //             // search Garage by ERP y ERP Code
    //             //$erp_id = Configure::read('ERP_IDS.'.$row['B']);
    //             $garage = $this->Garage->findByRefCodeAndAagRegionId($row['C'], Configure::read('AAG_REGION_ID_BENELUX'));
    //             $files = glob($file_path_images . '/' . $row['A'] . '/*.{jpg,JPG,jpeg,JPEG,png,PNG,webp,WEBP,bmp,BMP,gif,GIF}', GLOB_BRACE);

    //             if ($garage && count($files) > 0) {
    //                 $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garage['Garage']['id'], NETWORK_ID_GV);
    //                 $principal = true;
    //                 foreach ($files as $urlFile) {
    //                     if (is_array(getimagesize($urlFile))) {
    //                         $fileDataInitial = pathinfo($urlFile);
    //                         $imageGarage = FileManager::upload_image_webroot(
    //                             $urlFile,
    //                             array('name' => $fileDataInitial['basename'], 'type' => mime_content_type($urlFile), 'error' => false),
    //                             ConstantsFileType::IMAGE,
    //                             ConstantsPath::DIR_GARAGE_IMAGES
    //                         );
    //                         $urlFileWebP = ConstantsPath::DIR_GARAGE_IMAGES . '/' . $imageGarage;
    //                         $fileDataWebP = pathinfo($urlFileWebP);

    //                         if ($imageGarage) {
    //                             $garageImageSave = array(
    //                                 'GarageImage' => array(
    //                                     'garage_id' => $garage['Garage']['id'],
    //                                     'file' => $imageGarage,
    //                                     'source_name' => $imageGarage,
    //                                     'type' => mime_content_type($urlFileWebP),
    //                                     'ext' => $fileDataWebP['extension'],
    //                                     'principal' => $principal,
    //                                     'creation_date' => date('Y-m-d H:i:s')
    //                                 )
    //                             );

    //                             $fields = array('GarageImage' => array('garage_id', 'file', 'source_name', 'type', 'ext', 'principal', 'creation_date'));
    //                             $this->GarageImage->create();
    //                             $garageImageBd = $this->GarageImage->save($garageImageSave, array('fieldList' => $fields));

    //                             if (!$garageImageBd) {
    //                                 $result = false;
    //                                 CakeLog::write('script-load-images-crm-gv', 'Import Garage Images - Failed insert at line ' . $key . PHP_EOL);
    //                             } else {
    //                                 if ($garageNetwork) {
    //                                     //$imageGarageNetwork = copy($urlFileWebP, ConstantsPath::DIR_GARAGE_NETWORK_IMAGES . '/'. $imageGarage);
    //                                     $imageGarageNetwork = FileManager::upload_image_webroot(
    //                                         $urlFile,
    //                                         array('name' => $fileDataInitial['basename'], 'type' => mime_content_type($urlFile), 'error' => false),
    //                                         ConstantsFileType::IMAGE,
    //                                         ConstantsPath::DIR_GARAGE_NETWORK_IMAGES
    //                                     );

    //                                     if ($imageGarageNetwork) {
    //                                         $garageNetworkImageSave = array(
    //                                             'GarageNetworkImage' => array(
    //                                                 'garage_network_id' =>  $garageNetwork['GarageNetwork']['id'],
    //                                                 'file' => $imageGarage,
    //                                                 'source_name' => $imageGarage,
    //                                                 'type' => mime_content_type($urlFileWebP),
    //                                                 'ext' => $fileDataWebP['extension'],
    //                                                 'principal' => $principal,
    //                                                 'creation_date' => date('Y-m-d H:i:s')
    //                                             )
    //                                         );

    //                                         $fields = array('GarageNetworkImage' => array('garage_network_id', 'file', 'source_name', 'type', 'ext', 'principal', 'creation_date'));
    //                                         $this->GarageNetworkImage->create();
    //                                         $garageNetworkImageBd = $this->GarageNetworkImage->save($garageNetworkImageSave, array('fieldList' => $fields));

    //                                         if (!$garageNetworkImageBd) {
    //                                             $result = false;
    //                                             CakeLog::write('script-load-images-crm-gv', 'Import Network Images - Failed insert at line ' . $key . PHP_EOL);
    //                                         }
    //                                         $principal = false;
    //                                     } else {
    //                                         $result = false;
    //                                         CakeLog::write('script-load-images-crm-gv', 'Import Images - Failed upload garage network image at line ' . $key . PHP_EOL);
    //                                     }
    //                                 } else {
    //                                     CakeLog::write('script-load-images-crm-gv', 'Import Images - Garage network not found at line ' . $key . PHP_EOL);
    //                                 }
    //                             }
    //                         } else {
    //                             $result = false;
    //                             CakeLog::write('script-load-images-crm-gv', 'Import Images - Failed upload garage image at line ' . $key . PHP_EOL);
    //                         }
    //                     }
    //                 }
    //             } else {
    //                 if ($garage) {
    //                     CakeLog::write('script-load-images-crm-gv', 'Folder does not exist or there are no images at line ' . $key . PHP_EOL);
    //                 } else {
    //                     CakeLog::write('script-load-images-crm-gv', 'Garage not found at line ' . $key . PHP_EOL);
    //                 }
    //             }
    //         } else {
    //             // if column A, B or C is empty, stop iteration
    //             break;
    //         }
    //     }
    //     if ($result) {
    //         $this->commit();
    //     }
    // }

    // public function createUsersGaragesGV()
    // {
    //     try {
    //         exit;
    //         $garages = $this->Garage->find(
    //             'all',
    //             array(
    //                 'joins' => array(
    //                     array(
    //                         'alias' => 'GarageNetwork',
    //                         'table' => 'garages_networks',
    //                         'type' => 'INNER',
    //                         'conditions' => array(
    //                             'GarageNetwork.garage_id = Garage.id',
    //                         ),
    //                     ),
    //                 ),
    //                 'conditions' => array(
    //                     'Garage.aag_region_id' => Configure::read('AAG_REGION_ID_BENELUX'),
    //                     'GarageNetwork.network_id' => NETWORK_ID_GV
    //                 ),
    //                 'fields' => array(
    //                     'Garage.id, Garage.name, Garage.email'
    //                 )
    //             )
    //         );

    //         $language = $this->Language->find(
    //             'first',
    //             array(
    //                 'fields' => array(
    //                     'id'
    //                 ),
    //                 'conditions' => array(
    //                     'code' => 'nl'
    //                 )
    //             )
    //         );

    //         $result = true;
    //         $this->begin();
    //         foreach ($garages as $garage) {
    //             $contact = array(
    //                 'Contact' => array(
    //                     'first_name' => $garage['Garage']['name'],
    //                     'last_name' => '',
    //                     'garage_id' => $garage['Garage']['id'],
    //                     'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
    //                     'phone' => null,
    //                     'mobile_phone' => null,
    //                     'identification_number' => '',
    //                     'email' => $garage['Garage']['email'],
    //                     'creation_date' => date('Y-m-d'),
    //                     'aag_region_id' => Configure::read('AAG_REGION_ID_BENELUX'),
    //                     'guid' => CakeText::uuid()
    //                 )
    //             );
    //             $fieldsContact = array(
    //                 'Contact' => array(
    //                     'first_name', 'last_name', 'garage_id', 'position_id', 'phone', 'mobile_phone', 'identification_number',
    //                     'email', 'creation_date', 'aag_region_id', 'guid'
    //                 )
    //             );

    //             $this->Contact->create();
    //             $this->Contact->validator()->remove('last_name');
    //             $this->Contact->validator()->remove('identification_number');
    //             $contactBD = $this->Contact->save($contact, array('fieldList' => $fieldsContact));

    //             if ($contactBD) {
    //                 $user = array(
    //                     'User' => array(
    //                         'contact_id' => $contactBD['Contact']['id'],
    //                         'name' => $garage['Garage']['name'],
    //                         'surname' => '',
    //                         'username' => $garage['Garage']['email'],
    //                         'password' => 'importedGV',
    //                         'email' => $garage['Garage']['email'],
    //                         'role_id' => ConstantsRoles::GARAGE,
    //                         'language_id' => $language['Language']['id'],
    //                         'garage_id' => $garage['Garage']['id'],
    //                         'active' => ConstantsBooleans::ACTIVE,
    //                         'creation_date' => date('Y-m-d H:i:s'),
    //                         'aag_region_id' => Configure::read('AAG_REGION_ID_BENELUX'),
    //                         'guid' => CakeText::uuid()
    //                     )
    //                 );

    //                 $fieldsUser = array(
    //                     'User' => array(
    //                         'contact_id', 'name', 'surname', 'username', 'password', 'email', 'role_id', 'language_id',
    //                         'garage_id', 'active', 'creation_date', 'aag_region_id', 'guid'
    //                     )
    //                 );
    //                 $this->User->create();
    //                 $this->User->validator()->remove('surname');
    //                 $this->User->validator()->remove('password');
    //                 $userBd = $this->User->save($user, array('fieldList' => $fieldsUser));

    //                 if (!$userBd) {
    //                     $result = false;
    //                     CakeLog::write('script-create-userGV', 'Failed saving user info from garage ' . $garage['Garage']['id'] . PHP_EOL);
    //                 } else {
    //                     $garageContactStaff = array(
    //                         'GarageContactStaff' => array(
    //                             'garage_id' => $garage['Garage']['id'],
    //                             'contact_id' => $contactBD['Contact']['id'],
    //                             'main_contact' => 1
    //                         )
    //                     );
    //                     $this->GarageContactStaff->create();
    //                     $fieldsGontactStaff = array('GarageContactStaff' => array('garage_id', 'contact_id', 'main_contact'));
    //                     $garageContactStaffBd = $this->GarageContactStaff->save($garageContactStaff, array('fieldList' => $fieldsGontactStaff));

    //                     if (!$garageContactStaffBd) {
    //                         $result = false;
    //                         CakeLog::write('script-create-userGV', 'Failed saving garage contact staff from garage ' . $garage['Garage']['id'] . PHP_EOL);
    //                     } else {
    //                         // if the user has been created successfully: Send an email with data and link.
    //                         $this->UserRecoverPassword->add_user_password($userBd['User']['id']);

    //                     }
    //                 }
    //             } else {
    //                 $result = false;
    //                 CakeLog::write('script-create-userGV', 'Failed saving contact info from garage ' . $garage['Garage']['id'] . PHP_EOL);
    //             }
    //         }

    //         $this->autoRender = false;
    //         if ($result) {
    //             $this->commit();
    //             echo 'FINALIZADO OK <br>';
    //             return true;
    //         }
    //         echo 'FINALIZADO KO  <br>';
    //         return false;
    //     } catch (Exception $e) {
    //         debug($e->getMessage());
    //         $this->autoRender = false;
    //         echo 'FINALIZADO KO  <br>';
    //         return false;
    //     }
    // }
}
