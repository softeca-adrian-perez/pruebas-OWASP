<?php

class Order extends AppModel
{
    public $useTable = 'orders';

    public $hasMany = array(
        'GarageProduct',
    );

    public $validate = array(
        'order_number' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number',
            ),
            'unique' => array(
                'rule' => 'isUniqueWithinGarage',
                'message' => 'Validation.Order_number_must_be_unique',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'invoce_number' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'invoice_amount' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', 0, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'notes' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Config.Empty_field',
            ),
        ),
        'order_type_id' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_order',
            ),
        ),
        'order_date' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_order_date',
            ),
        ),
    );

    public function isUniqueWithinGarage($check)
    {
        $garageId = $this->data['Order']['garage_id'];
        $orderId = isset($this->data['Order']['id']) ? $this->data['Order']['id'] : null;
        $conditions = array(
            'AND' => array(
                array('Order.garage_id' => $garageId),
                array('Order.order_number' => $check['order_number']),
            ),
        );
        if ($orderId !== null) {
            $conditions['NOT']['Order.id'] = $orderId;
        }
        $count =  $this->find('count', array(
            'conditions' => $conditions,
        ));
        return $count === 0;
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'order_number',
            ),
            'order' => array(
                'order_number',
            )
        ));
    }

    public function add_orders($orders, $garage_id)
    {
        $fields = array(
            'Order' => array(
                'garage_id',
                'order_number',
                'invoice_date',
                'order_date',
                'completed_date',
                'invoce_number',
                'invoice_amount',
                'notes',
                'order_type_id',
            )
        );
        unset($orders['Order']['array_product_id']);
        unset($orders['Order']['array_quantity']);

        $orders['Order']['garage_id'] = $garage_id;

        $orders['Order']['invoice_date'] = Fecha::toFormatoBd($orders['Order']['invoice_date']);
        $orders['Order']['order_date'] = Fecha::toFormatoBd($orders['Order']['order_date']);
        $orders['Order']['completed_date'] = Fecha::toFormatoBd($orders['Order']['completed_date']);
        $this->create();

        $orders_bd = $this->guardar($orders, $fields);

        if (!$orders_bd) {
            return false;
        }

        $this->commit();
        return $orders_bd;
    }

    public function edit_orders($orders)
    {
        $fields = array(
            'Order' => array(
                'garage_id',
                'order_number',
                'invoice_date',
                'order_date',
                'completed_date',
                'invoce_number',
                'invoice_amount',
                'notes',
                'order_type_id',
            )
        );

        unset($orders['Order']['array_product_id']);
        unset($orders['Order']['array_quantity']);

        $orders['Order']['invoice_date'] = Fecha::toFormatoBd($orders['Order']['invoice_date']);
        $orders['Order']['order_date'] = Fecha::toFormatoBd($orders['Order']['order_date']);
        $orders['Order']['completed_date'] = Fecha::toFormatoBd($orders['Order']['completed_date']);

        $orders_bd = $this->guardar($orders, $fields);
        if (!$orders_bd) {
            return false;
        }

        return $orders_bd;
    }

    public function getAllOrders($garage_id)
    {
        return
            $this->find(
                'all',
                array(
                    'conditions' => array(
                        'Order.garage_id' => $garage_id,
                    ),
                    'fields' => array(
                        'Order.*',
                    ),
                    'group' => array(
                        'Order.id'
                    ),
                )
            );
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['garage_id'])) {
            $conditions[] = $this->_conditionTable($fields['garage_id']);
        }
        if (!empty($fields['order_number'])) {
            $conditions[] = $this->_conditionField($fields['order_number']);
        }
        if (!empty($fields['invoice_date'])) {
            $conditions[] = $this->_conditionUser($fields['invoice_date']);
        }
        if (!empty($fields['order_date'])) {
            $conditions[] = $this->_conditionOldValue($fields['order_date']);
        }
        if (!empty($fields['completed_date'])) {
            $conditions[] = $this->_conditionNewValue($fields['completed_date']);
        }
        if (!empty($fields['invoce_number'])) {
            $conditions[] = $this->_conditionDateFrom($fields['invoce_number']);
        }
        if (!empty($fields['invoice_amount'])) {
            $conditions[] = $this->_conditionDateTo($fields['invoice_amount']);
        }
        if (!empty($fields['notes'])) {
            $conditions[] = $this->_conditionContact($fields['notes']);
        }
        return $conditions;
    }
}
