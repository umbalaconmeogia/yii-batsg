<?php
class BaseController extends CController
{
  /**
   * Controller's specified language.
   * If specified, then this language will be used ignoring the user's selection.
   * <p>
   * By default ($fixedLanguage is set to NULL), controller that extends
   * the <code>BaseController</code> will use language specified by the user.
   * Setting $fixedLanguage to specified value makes current controller
   * only use specified language. For example, when you want a controller
   * only use English in a multilingua web application.
   */
  public $fixedLanguage = NULL;

  /**
   * Override the init() method to call the setLanguage().
   * <p>
   * <code>init()</code> is always called by yii controller constructor.
   */
  public function init()
  {
    parent::init();
    Y::setLanguage($this->fixedLanguage);
  }

  /**
   * Get the specified URL parameter.
   * <p>
   * This is the wrapper for getting <code>$_REQUEST[&lt;parameter&gt;]</code>,
   * it will return the default value if the parameter is not set.
   * <p>
   * If it is not set, $defValue will be returned.
   * @param string $paramName The parameter to be get.
   * @param mixed $defValue Value to return in case the parameter is not specified in the URL.
   * @param boolean $trim Trim the input value if set to TRUE
   * @return mixed
   */
  protected function getParam($paramName, $defValue = NULL, $trim = TRUE)
  {
    $value = isset($_REQUEST[$paramName]) ? $_REQUEST[$paramName] : $defValue;
    // Trim
    if ($trim) {
      if (is_array($value)) {
        foreach ($value as $key => $v) {
          $value[$key] = trim($v);
        }
      } else {
        $value = trim($value);
      }
    }
    return $value;
  }

  protected function loadModelFromParam($modelClassName, $id = NULL, $pk = 'id')
  {
    if ($id == NULL && isset($_REQUEST[$modelClassName]) && isset($_REQUEST[$modelClassName][$pk])) {
      $id = $_REQUEST[$modelClassName][$pk];
    }
    $model = BaseModel::loadModel($modelClassName, $id);
    if (isset($_REQUEST[$modelClassName])) {
      $model->attributes = $_REQUEST[$modelClassName];
    }
    return $model;
  }

  /**
   * Load model list from URL params.
   * Each model information is passed as $_REQUEST[$modelClassName][index]
   * @param string $modelClassName Class name of model.
   * @return CActiveRecord[]
   */
  protected function loadModelListFromParam($modelClassName) {
    $modelList = array();
    if (isset($_REQUEST[$modelClassName])) {
      foreach ($_REQUEST[$modelClassName] as $modelArr) {
        $model = new $modelClassName;
        $model->massiveAssign($model, $modelArr);
        $model->setEmptyStringToNull();
        $modelList[] = $model;
      }
    }
    return $modelList;
  }

  /**
   * Load existed/create new model.
   * <p>
   * Override this when neccessary, for example, to set some attributes
   * of the model.
   * @param string $modelClassName
   * @param mixed $id The object id. If id is NULL, then new model object is created.
   * @return BaseModel
   */
//  protected function loadModel($modelClassName, $id = NULL)
//  {
//    return BaseModel::loadModel($modelClassName, $id);
//  }
}
?>