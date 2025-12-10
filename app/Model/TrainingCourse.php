<?php
class TrainingCourse extends AppModel
{
    public $useTable = 'trainings_courses';
    public $displayField = 'name';

    public $validate = array(
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
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Validation.Name_must_be_unique',
            ),
        ),
        'course_type_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_course'
            ),
        ),
        'training_provider_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_provider'
            ),
        ),
        'price_credit' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_price'
            ),
        ),
        'cost_training' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_price'
            ),
        ),
        'description' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'part_number' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['TrainingCourse_name'])) {
            $conditions[] = $this->conditionCourseName($fields['TrainingCourse_name']);
        }
        if (!empty($fields['CourseType_name'])) {
            $conditions[] = $this->conditionCourseType($fields['CourseType_name']);
        }
        if (!empty($fields['TrainingProvider_name'])) {
            $conditions[] = $this->conditionTrainingProvider($fields['TrainingProvider_name']);
        }
        if (!empty($fields['TrainingPlannedCourse_date_from'])) {
            $conditions[] = $this->conditionDateFrom($fields['TrainingPlannedCourse_date_from']);
        }
        if (!empty($fields['TrainingPlannedCourse_date_to'])) {
            $conditions[] = $this->conditionDateTo($fields['TrainingPlannedCourse_date_to']);
        }
        if (!empty($fields['TrainingPlannedCourse_duration'])) {
            $conditions[] = $this->conditionDuration($fields['TrainingPlannedCourse_duration']);
        }
        if (!empty($fields['Venue_name'])) {
            $conditions[] = $this->conditionVenueName($fields['Venue_name']);
        }
        if (isset($fields['TrainingPlannedCourse_status']) && $fields['TrainingPlannedCourse_status'] !== '') {
            $conditions[] = $this->conditionStatus($fields['TrainingPlannedCourse_status']);
        }
        if (!empty($fields['TrainingCourse_cost_training'])) {
            $conditions[] = $this->conditionCostTraining($fields['TrainingCourse_cost_training']);
        }
        if (!empty($fields['TrainingCourse_price_credit'])) {
            $conditions[] = $this->conditionPriceCredit($fields['TrainingCourse_price_credit']);
        }
        if (!empty($fields['TrainingPlannedCourse_availability'])) {
            $conditions[] = $this->conditionAvailability($fields['TrainingPlannedCourse_availability']);
        }
        if (!empty($fields['active'])) {
            $conditions[] = $this->conditionActive($fields['active']);
        }
        if (!empty($fields['TrainingCourse_part_number'])) {
            $conditions[] = $this->conditionPartNumber($fields['TrainingCourse_part_number']);
        }
        if (!empty($fields['is_online'])) {
            $conditions[] = $this->conditionIsOnline($fields['is_online']);
        }

        return $conditions;
    }

    private function conditionCourseName($course_name)
    {
        return array('TrainingCourse.id =' => $course_name);
    }

    private function conditionCourseType($course_type)
    {
        return array('TrainingCourse.course_type_id =' => $course_type);
    }

    private function conditionTrainingProvider($training_provider)
    {
        return array('TrainingCourse.training_provider_id =' => $training_provider);
    }

    private function conditionDateFrom($date_from)
    {
        return array('TrainingPlannedCourse.date_from >=' => Fecha::toFormatoBd($date_from));
    }

    private function conditionDateTo($date_to)
    {
        return array('TrainingPlannedCourse.date_to <=' => Fecha::toFormatoBd($date_to));
    }

    private function conditionDuration($duration)
    {
        return array('TrainingPlannedCourse.duration =' => $duration);
    }

    private function conditionVenueName($venue_name)
    {
        return array('Venue.id =' => $venue_name);
    }

    private function conditionStatus($status)
    {
        if (in_array($status, array('1', '0', '2'))) {
            return array('TrainingPlannedCourse.status' => $status);
        }
    }

    private function conditionCostTraining($cost_training)
    {
        return array('TrainingCourse.cost_training <=' => $cost_training);
    }

    private function conditionPriceCredit($price_credit)
    {
        return array('TrainingCourse.price_credit <=' => $price_credit);
    }

    private function conditionAvailability($availability)
    {
        if ($availability == 'Unavailable') {
            return array('TrainingPlannedCourse.status !=' => '1');
        } else {
            return array('TrainingPlannedCourse.status =' => '1');
        }
    }

    private function conditionActive($active)
    {
        return array('TrainingCourse.active =' => $active);
    }

    private function conditionPartNumber($part_number)
    {
        return array('TrainingCourse.part_number LIKE' => '%' . $part_number . '%');
    }

    private function conditionIsOnline($is_online)
    {
        return array('TrainingCourse.is_online' => $is_online);
    }

    private $queries = array(
        'home' => array(
            'joins' => array(
                array(
                    'alias' => 'TrainingPlannedCourse',
                    'table' => 'trainings_planned_courses',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingPlannedCourse.training_course_id = TrainingCourse.id',
                    ),
                ),
                array(
                    'alias' => 'Venue',
                    'table' => 'venues',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Venue.id = TrainingPlannedCourse.venue_id',
                    ),
                ),
                array(
                    'alias' => 'TrainingTrainer',
                    'table' => 'trainings_trainers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingTrainer.id = TrainingPlannedCourse.training_trainer_id',
                    ),
                ),
                array(
                    'alias' => 'CourseType',
                    'table' => 'courses_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CourseType.id = TrainingCourse.course_type_id',
                    ),
                ),
                array(
                    'alias' => 'TrainingProvider',
                    'table' => 'trainings_providers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingProvider.id = TrainingCourse.training_provider_id',
                    ),
                ),
            ),
            'fields' => array(
                'TrainingPlannedCourse.*',
                'Venue.name',
                'TrainingCourse.*',
                'TrainingTrainer.name',
                'CourseType.*',
                'TrainingProvider.id',
                'TrainingProvider.name'
            ),
            'group' => 'TrainingCourse.name',
            'order' => 'TrainingCourse.name asc',
        ),
    );

    public function _query($index)
    {
        return $this->queries[$index];
    }

    public function add($trainingCourse)
    {
        $fields = array(
            'TrainingCourse' => array(
                'name',
                'course_type_id',
                'training_provider_id',
                'duration',
                'price',
                'price_credit',
                'cost_training',
                'description',
                'part_number',
                'invoice_number',
                'active',
                'is_online',
                'creation_date'
            )
        );

        $trainingCourse['TrainingCourse']['active'] = ConstantsBooleans::ACTIVE;
        $trainingCourse['TrainingCourse']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();
        if ($this->guardar($trainingCourse, $fields)) {
            return $trainingCourse;
        }
    }

    public function edit($trainingCourse)
    {
        $fields = array(
            'TrainingCourse' => array(
                'name',
                'course_type_id',
                'training_provider_id',
                'duration',
                'price',
                'price_credit',
                'cost_training',
                'description',
                'part_number',
                'invoice_number',
                'is_online',
                'modification_date'
            )
        );

        $trainingCourse['TrainingCourse']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($trainingCourse, $fields);
    }

    public function searchList()
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

    public function getTrainingCourseTypeById($course_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TrainingProvider',
                        'table' => 'trainings_providers',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TrainingProvider.id = TrainingCourse.training_provider_id',
                        ),
                    ),
                    array(
                        'alias' => 'CourseType',
                        'table' => 'courses_types',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'CourseType.id = TrainingCourse.course_type_id',
                        ),
                    )
                ),
                'conditions' => array(
                    'TrainingCourse.id' => $course_id,
                ),
                'fields' => array(
                    'CourseType.name_' . __l(),
                    'TrainingProvider.name'
                ),
            )
        );
    }

    public function checkMaxValuePrice()
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(),
                'fields' => array(
                    'MAX(TrainingCourse.price_credit) as max_price_credit',
                ),
            )
        );
    }

    public function checkMaxValueCost()
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(),
                'fields' => array(
                    'MAX(TrainingCourse.cost_training) as max_cost_training',
                ),
            )
        );
    }

    public function getByCourseId($course_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TrainingCourse.id' => $course_id,
                ),
            )
        );
    }

    public function toggleStatusActive($course_id)
    {
        $fields = array(
            'TrainingCourse' => array(
                'active',
                'modification_date'
            )
        );

        $trainingCourse = $this->findById($course_id);

        if ($trainingCourse['TrainingCourse']['active'] != ConstantsBooleans::ACTIVE) {
            $trainingCourse['TrainingCourse']['active'] = ConstantsBooleans::ACTIVE;
        }

        $trainingCourse['TrainingCourse']['modification_date'] = date('Y-m-d H:i:s');

        $trainingCourseDb = $this->guardar($trainingCourse, $fields);
        if (!$trainingCourseDb) {
            return false;
        }

        $this->commit();
        return $trainingCourseDb;
    }

    public function toggleStatusInactive($course_id)
    {
        $fields = array(
            'TrainingCourse' => array(
                'active',
                'modification_date'
            )
        );

        $trainingCourse = $this->findById($course_id);

        if ($trainingCourse['TrainingCourse']['active'] == ConstantsBooleans::ACTIVE) {
            $trainingCourse['TrainingCourse']['active'] = ConstantsBooleans::NO_ACTIVE;
        }

        $trainingCourse['TrainingCourse']['modification_date'] = date('Y-m-d H:i:s');

        $trainingCourseDb = $this->guardar($trainingCourse, $fields);
        if (!$trainingCourseDb) {
            return false;
        }

        $this->commit();
        return $trainingCourseDb;
    }

    public function searchListActive()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            ),
            'conditions' => array(
                'TrainingCourse.active' => ConstantsBooleans::ACTIVE,
            ),
        ));
    }

    public function getListofCoursesWithoutIds($coursesIds)
    {
        return $this->find('list', array(
            'conditions' => array(
                'TrainingCourse.id NOT IN' => $coursesIds
            ),
            'fields' => array(
                'TrainingCourse.id'
            )
        ));
    }
}
