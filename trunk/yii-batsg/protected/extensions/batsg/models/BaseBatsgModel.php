<?php
/**
 * Model that has field $id, $data_status, $create_time, $create_user_id, $update_time, $update_user_id.
 */
class BaseBatsgModel extends BaseModel
{
  const DATA_STATUS_NEW = 1;
  const DATA_STATUS_UPDATE = 2;
  const DATA_STATUS_DELETE = 9;

  /**
   * Prepare data_status, update_time, create_time
   * attributes before performing validation.
   * @see CModel::beforeValidate()
   */
  protected function beforeValidate()
  {
    $currentUserId = isset(Yii::app()->user) ? Yii::app()->user->id : NULL;
    if ($this->isNewRecord) {
      // data_status will be set when import data from backup csv etc.
      if (!$this->data_status) {
        $this->data_status = self::DATA_STATUS_NEW;
      }
      if (!$this->create_user_id) {
        $this->create_user_id = $currentUserId;
      }
      // create_time will be set when import data from backup csv etc.
      if (!$this->create_time) {
        $this->create_time = HDateTime::now()->toString();
      }
    } else {
      // Only set data_status to "update" if this is not deleted.
      if ($this->data_status == self::DATA_STATUS_NEW) {
        $this->data_status = self::DATA_STATUS_UPDATE;
      }
      if (!$this->update_user_id) {
        $this->update_user_id = $currentUserId;
      }
      $this->update_time = HDateTime::now()->toString();
    }
    return parent::beforeValidate();
  }

  /**
   * Perform massiveAssignment to a model.
   * @param CActiveRecord $model
   * @param array $parameters key=>value to assign to $model->attributes.
   * @param array $exclusiveFields Fields that are not assigned.
   */
  public static function massiveAssign($model, $parameters,
      $exclusiveFields = array('id', 'create_time', 'create_user_id', 'update_time', 'update_user_id'))
  {
    parent::massiveAssign($model, $parameters, $exclusiveFields);
  }

  /**
   * Get only valid models (data_status <> deleted) from model list.
   * @param BaseBatsgModel[] $modelList
   */
  public static function getValidModels($modelList)
  {
    $result = array();
    foreach ($modelList as $model) {
      if ($model->data_status <> self::DATA_STATUS_DELETE) {
        $result[] = $model;
      }
    }
    return $result;
  }

  /**
   * @return BaseBatsgModel[]
   */
  public function findAllNotDeleted($order = NULL)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('data_status', '<>' . self::DATA_STATUS_DELETE);
    $criteria->order = $order;
    return $this->findAll($criteria);
  }

  /**
   * Search for same models by condition specified by this model.
   * @param string[] $searchFields
   * @param string $order
   * @return BaseBatsgModel[]
   */
  public function searchNotDeleted($searchFields, $order = NULL)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('data_status', '<>' . self::DATA_STATUS_DELETE);
    foreach ($searchFields as $fieldName) {
      $criteria->compare($fieldName, $this->$fieldName);
    }
    $criteria->order = $order;
    return $this->findAll($criteria);
  }

  /**
   * Reset fields below to NULL.
   *   id
   *   data_status
   *   create_time
   *   create_user_id
   *   update_time
   *   update_user_id
   */
  public function resetCommonFields()
  {
    $this->setFieldToNull(array('id', 'data_status', 'create_time', 'create_user_id', 'update_time', 'update_user_id'));
  }

  public function saveLogError()
  {
    $result = parent::save();
    if (!$result) {
      $this->logError();
    }
    return $result;
  }

  public function saveThrowError()
  {
    if (!$this->saveLogError()) {
      throw new Exception("Error while saving " . $this->toString());
    }
  }

  public function deleteLogically()
  {
    Yii::log("Delete $this logically.");

    $this->data_status = self::DATA_STATUS_DELETE;
    if (!$this->save()) {
      $this->logError();
      throw new Exception("Error while deleting " . $this);
    }
  }

  /**
   * Fields that are not checked in hasData()
   * @return string[]
   */
  protected function hasDataNotCheckFields()
  {
    return array(
      'id',
      'data_status',
      'create_user_id',
      'create_time',
      'update_user_id',
      'update_time',
    );
  }

  /**
   * Fields that are used when checking hasData().
   * @return string[]
   */
  protected function hasDataCheckFields()
  {
    return array_diff(array_keys($this->attributes), $this->hasDataNotCheckFields());
  }

  /**
   * Check if operation has any data (inputable by user) specified.
   * @return boolean
   */
  public function hasData()
  {
    $result = FALSE;
    foreach ($this->hasDataCheckFields() as $field) {
      if ($this->$field) {
        $result = TRUE;
        break;
      }
    }
    return $result;
  }

  /**
   * Create a hash of model list by a model field value (usally "id" field).
   * @param mixed $models An array of models or a model class name.
   *                      In case of model class name, then get all alive model from the db table.
   * @param string $hashField
   * @return CActiveRecord[] field value => model.
   */
  public static function hashModels($models, $hashField = 'id')
  {
    if (is_string($models)) {
      $models = $models::model()->findAllNotDeleted();
    }
    return parent::hashModels($models, $hashField);
  }
}
?>