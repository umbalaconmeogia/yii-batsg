<?php
/**
 * FullWidthValidator class file.
 *
 * @author Tran Trung Thanh <umbalaconmeogia@gmail.com>
 * @link http://chauhai.com/
 * @copyright Copyright &copy; 2011 chauhai.com
 * @license http://www.yiiframework.com/license/
 */

/**
 * FullWidthValidator validates that the attribute value contains only multi byte characters.
 *
 * @author Tran Trung Thanh <umbalaconmeogia@gmail.com>
 * @version $Id$
 * @package application.extensions
 */
class FullWidthValidator extends CValidator
{
  /**
   * @var boolean whether the attribute value can be null or empty. Defaults to true,
   * meaning that if the attribute is empty, it is considered valid.
   */
  public $allowEmpty = true;
  
  /**
   * Validates the attribute of the object.
   * If there is any error, the error message is added to the object.
   * @param CModel $object the object being validated
   * @param string $attribute the attribute being validated
   */
  protected function validateAttribute($object, $attribute)
  {
    $value = $object->$attribute;
    if($this->allowEmpty && $this->isEmpty($value)) {
      return;
    }
    
    $len = strlen($value);
    // UTF-8の場合は全角を3文字カウントするので「* 3」にする
    $mblen = mb_strlen($value, "UTF-8") * 3;
    
    if ($len != $mblen) {
      $message = $this->message !== null ? $this->message : Yii::t('batsg', '{attribute} must be full-width characters.');
      $this->addError($object, $attribute, $message);
    }
  }
}
?>