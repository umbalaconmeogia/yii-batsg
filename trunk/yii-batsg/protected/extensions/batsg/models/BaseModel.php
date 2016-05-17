<?php
class BaseModel extends CActiveRecord
{
  private $_backupAttributes = NULL;

  /**
   * Returns the data model based on the primary key given.
   * If the data model is not found, an HTTP exception will be raised.
   * @param string $modelClassName
   * @param mixed $id the primary key of the model to be loaded
   * @return CActiveRecord
   */
  public static function loadModel($modelClassName, $id = NULL)
  {
    if ($id != NULL) {
      $model = call_user_func(array($modelClassName, 'model'))->findByPk($id);
      if($model === null) {
        throw new CHttpException(404, 'The requested page does not exist.');
      }
    } else {
      $model = new $modelClassName;
    }
    return $model;
  }

  /**
   * Get all errors on this model.
   * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
   * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
   */
  public function getErrorMessages($attribute = NULL)
  {
    if ($attribute === NULL) {
      $attribute = $this->attributeNames();
    }
    if (!is_array($attribute)) {
      $attribute = array($attribute);
    }
    $errors = array();
    foreach ($attribute as $attr) {
      if ($this->hasErrors($attr)) {
        $errors = array_merge($errors, array_values($this->getErrors($attr)));
      }
    }
    return $errors;
  }

  /**
   * Log error of this model.
   * @param string $message
   */
  public function logError($message = NULL, $category='application')
  {
    if ($message) {
      Yii::log($message, 'error', $category);
    }
    Yii::log($this->tableName() . " " . print_r($this->attributes, TRUE), 'error', $category);
    Yii::log(print_r($this->getErrorMessages(), TRUE), 'error', $category);
  }

  /**
   * Create a hash of model list by a model field value (usally "id" field).
   * @param mixed $models An array of models or a model class name.
   *                      In case of model class name, then get all alive model from the db table.
   * @param string $hashField Default by id.
   * @return CActiveRecord[] field value => model.
   */
  public static function hashModels($models, $hashField = 'id')
  {
    $hash = array();

    if (is_string($models)) {
      $models = $models::model()->findAll();
    }
    foreach ($models as $model) {
      $hash[$model->$hashField] = $model;
    }
    return $hash;
  }

  /**
   * Create a criteria for searching fields with OR operator (for example, searching name and name kana fields).
   * This is used to merge with the main criteria.
   * Usage example:
   *  $mainCriteria->mergeWith($this->dbCriteriaOr('Adam', array('first_name', 'last_name')));
   * @param string $searchValue
   * @param string $table
   * @param string[] $fields
   * @return CDbCriteria
   */
  protected function dbCriteriaOr($searchValue, $fields, $table = 't', $partialMatch = TRUE)
  {
    $prefix = $table ? "{$table}." : NULL;
    $criteria = new CDbCriteria();
    foreach ($fields as $field) {
      $criteria->compare("$prefix$field", $searchValue, $partialMatch, 'OR');
    }
    return $criteria;
  }

	public function __toString()
	{
	  return $this->toString($this->tableSchema->primaryKey);
	}

  /**
   * @param mixed $fields String or string array. If NULL, all attributes are used.
   * @return string.
   */
  public function toString($fields = NULL)
  {
    if ($fields === NULL) {
      $fields = array_keys($this->attributes);
    }
    if (!is_array($fields)) {
      $fields = array($fields);
    }
    foreach ($fields as $field) {
      $info[] = "$field: {$this->$field}";
    }
    return get_class($this) . '(' . join(', ', $info) . ')';
  }

  /**
   * Add compare year/month to a db criterial.
   * @param CDbCriteria $criteria
   * @param string $column Column to be compared.
   * @param mixed $dateTime String or HDateTime Input date (or date time).
   */
  public static function addCompareYearMonth($criteria, $column, $dateTime)
  {
    if ($dateTime) {
      if (!$dateTime instanceof HDateTime) {
        $dateTime = preg_split("/[\/\-]+/", $dateTime);
        if (count($dateTime) > 1) {
          $dateTime = HDateTime::createFromString($dateTime[0] . '/' . $dateTime[1] . '/1');
        }
      }
      $dateTime = $dateTime->toString('Y-m');
      $criteria->compare("DATE_FORMAT($column, '%Y-%m')", $dateTime);
    }
  }

  /**
   * Perform massiveAssignment to a model.
   * @param CActiveRecord $model
   * @param array $parameters key=>value to assign to $model->attributes.
   * @param array $exclusiveFields Fields that are not assigned.
   */
  public static function massiveAssign($model, $parameters, $exclusiveFields = array())
  {
    foreach ($exclusiveFields as $field) {
      if (isset($parameters[$field])) {
        unset($parameters[$field]);
      }
    }
    $model->attributes = $parameters;
  }

  /**
   * Set all empty data fields to NULL.
   * @return CModel This object.
   */
  public function setEmptyStringToNull()
  {
    foreach ($this->attributes as $field => $value) {
      if ($value === '') {
        $this->$field = NULL;
      }
    }
    return $this;
  }

  /**
   * Set specified fields to NULL.
   * @param string[] $fields
   */
  public function setFieldToNull($fields = array())
  {
    foreach ($fields as $field) {
      $this->$field = NULL;
    }
  }

  /**
   * Lock table relates to this model.
   */
  public function lockThisTable()
  {
    self::lockTable($this->tableName());
  }

  /**
   * Lock a DB table. This method should use with InnoDB only.
   * @param mixed $tables A string (table name) or array of tables with or without alias ("people", "people AS p")
   */
  public static function lockTable($tables)
  {
    if (!is_array($tables)) {
      $tables = array($tables);
    }
    $lockTables = array();
    foreach ($tables as $table) {
      $lockTables[] = "$table WRITE";
    }
    $lockTables = implode(', ', $lockTables);

    Yii::app()->db->createCommand("SET AUTOCOMMIT = 0;")->execute(); // This is needed for transaction use inside the lock.
    Yii::app()->db->createCommand("LOCK TABLES {$lockTables};")->execute();
  }

  /**
   * Unlock DB tables.
   */
  public static function unlockTables()
  {
    Yii::app()->db->createCommand('UNLOCK TABLES;')->execute();
  }

  /**
   * Copy fields from other model to this.
   * Usage example:
   * <pre>
   *   // This is equivalent to
   *   // $destModel->name = $sourceModel->name;
   *   $destModel->copyFieldFromModel($sourceModel, 'name');
   *
   *   // This is equivalent to
   *   // $destModel->name = $sourceModel->name;
   *   // $destModel->source_id = $sourceModel->id;
   *   $destModel->copyFieldFromModel($sourceModel, array('name', 'id' => 'source_id');
   * </pre>
   * @param CActiveRecord $source
   * @param mixed $fields A string (field name) or an array of field names.
   *              Array element may be a field name or in type of source field => dest field.
   */
  public function copyFieldFromModel($source, $fields)
  {
    if (!is_array($fields)) {
      $fields = array($fields);
    }
    foreach ($fields as $index => $fieldName) {
      $sourceField = is_numeric($index) ? $fieldName : $index;
      $this->$fieldName = $source->$sourceField;
    }
  }

  /**
   * @param array $arr
   * @param CActiveRecord $model
   * @param string[] $fields (array of field names or source field -> dest field.
   */
  public function copyFieldToArray(&$arr, $fields)
  {
    foreach ($fields as $index => $field) {
      $sourceField = is_numeric($index) ? $field : $index;
      $arr[$field] = $this->$sourceField;
    }
    return $arr;
  }

  /**
   * @param array $source
   * @param string[] $fields (array of field names or source field -> dest field.
   */
  public function copyFieldFromArray($source, $fields)
  {
    foreach ($fields as $index => $field) {
      $sourceField = is_numeric($index) ? $field : $index;
      $this->$field = $source[$sourceField];
    }
  }

  public function backupAttributes()
  {
    $this->_backupAttributes = $this->attributes;
  }

  public function restoreAttributes()
  {
    if ($this->_backupAttributes) {
      foreach ($this->_backupAttributes as $key => $value) {
        $this->$key = $value;
      }
    }
  }

  /**
   * @param BaseModel[] $models
   */
  public static function restoreAttributeOfModels(array $models)
  {
    foreach ($models as $model) {
      $model->restoreAttributes();
    }
  }

  /**
   * Compare this model and other model by specified $field.
   * @param BaseModel $other
   * @param string $field
   * @return int -1 if this model is "smaller", 0 if two are equal or 1 if this model is "larger".
   */
  public function compare(BaseModel $other, $fields)
  {
	  if (!is_array($fields)) {
		  $fields = array($fields);
		}
		$result = 0;
		foreach ($fields as $field) {
		  if ($this->$field != $other->$field) {
				$result = $this->$field < $other->$field ? -1 : 1;
				break;
			}
		}
    return $result;
  }

  /**
   * Sort array of models by specified attribute.
   * @param BaseModel[] $models
   * @param mixed $sortField String or array of string. "display_order" for example.
   * @return BaseModel[] The sorted model list.
   */
  public static function sortModels(array &$models, $sortFields)
  {
    usort($models, function($a, $b) use ($sortFields) {
      $result = $a->compare($b, $sortFields);
      if ($result == 0) { // Sort by id if two are equal.
        $result = $a->compare($b, 'id');
      }
      return $result;
    });
    return $models;
  }

  /**
   * Get specified field value of a model array into array.
   * @param CActiveRecord[] $models
   * @param string $field
   * @return array
   */
  public static function getFieldValueArray($models, $field = 'id')
  {
    $result = array();
    foreach ($models as $model) {
      $result[] = $model->$field;
    }
    return $result;
  }
}
?>