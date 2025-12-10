<?php
App::uses('PaginatorComponent', 'Controller/Component');

class PaginatorOrderCustomComponent extends PaginatorComponent
{
	public function validateSort(Model $object, array $options, array $whitelist = array())
	{
		if (empty($options['order']) && is_array($object->order)) {
			$options['order'] = $object->order;
		}

		if (isset($options['sort'])) {
			$direction = null;
			if (isset($options['direction'])) {
				$direction = strtolower($options['direction']);
			}
			if (!in_array($direction, array('asc', 'desc'))) {
				$direction = 'asc';
			}
			$options['order'] = array($options['sort'] => $direction);
		}

		if (!empty($whitelist) && isset($options['order']) && is_array($options['order'])) {
			$field = key($options['order']);
			$inWhitelist = in_array($field, $whitelist, true);
			if (!$inWhitelist) {
				$options['order'] = null;
			}
			return $options;
		}
		// There are rows with null or empty fields. The "if else" of each case is used to keep these values always at the bottom, regardless of the column order.
		if (!empty($options['order']) && is_array($options['order'])) {
			$order = array();
			foreach ($options['order'] as $key => $value) {
				if (is_int($key)) {
					$key = $value;
					$value = 'asc';
				}
				$field = $key;
				$alias = $object->alias;
				if (strpos($key, '.') !== false) {
					list($alias, $field) = explode('.', $key);
				}
				$correctAlias = ($object->alias === $alias);
				if ($correctAlias && $object->hasField($field)) {
					if ($value == 'asc') {
						$order['IF((' . $object->alias . '.' . $field . ') IS NULL OR LENGTH(' . $object->alias . '.' . $field . ') = 0, 1, 0)'] = 'asc';
					} else {
						$order['IF((' . $object->alias . '.' . $field . ') IS NULL OR LENGTH(' . $object->alias . '.' . $field . ') = 0, 0, 1)'] = 'desc';
					}
					$order[$object->alias . '.' . $field] = $value;
				} elseif ($correctAlias && $object->hasField($key, true)) {
					if ($value == 'asc') {
						$order['IF((' . $field . ') IS NULL OR LENGTH(' . $field . ') = 0, 1, 0)'] = 'asc';
					} else {
						$order['IF((' . $field . ') IS NULL OR LENGTH(' . $field . ') = 0, 0, 1)'] = 'desc';
					}
					$order[$field] = $value;
				} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field, true)) {
					if ($value == 'asc') {
						$order['IF((' . $alias . '.' . $field . ') IS NULL OR LENGTH(' . $alias . '.' . $field . ') = 0, 1, 0)'] = 'asc';
					} else {
						$order['IF((' . $alias . '.' . $field . ') IS NULL OR LENGTH(' . $alias . '.' . $field . ') = 0, 0, 1)'] = 'desc';
					}
					$order[$alias . '.' . $field] = $value;
				} else {
					$aliasObject = $this->_getObject($alias);
					if (is_object($aliasObject)) {
						if ($aliasObject->hasField($field, true)) {
							if ($value == 'asc') {
								$order['IF((' . $alias . '.' . $field . ') IS NULL OR LENGTH(' . $alias . '.' . $field . ') = 0, 1, 0)'] = 'asc';
							} else {
								$order['IF((' . $alias . '.' . $field . ') IS NULL OR LENGTH(' . $alias . '.' . $field . ') = 0, 0, 1)'] = 'desc';
							}
							$order[$alias . '.' . $field] = $value;
						}
					}
				}
			}
			$options['order'] = $order;
		}

		return $options;
	}
}
