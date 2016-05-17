<?php
/**
 * HalfWidthValidator class file.
 *
 * @author Tran Trung Thanh <umbalaconmeogia@gmail.com>
 * @link http://chauhai.com/
 * @copyright Copyright &copy; 2011 chauhai.com
 * @license http://www.yiiframework.com/license/
 */

/**
 * HalfWidthValidator validates that the attribute value contains only single byte characters.
 *
 * @author Tran Trung Thanh <umbalaconmeogia@gmail.com>
 * @version $Id$
 * @package application.extensions
 */
class HalfWidthValidator extends CValidator
{
  /**
   * @var string the regular expression to be matched with
   */
  const PATTERN = '/^[\x{00}-\x{FF}]*$/u';
  const PATTERN_ALFANUMERIC = '/^[A-Za-z0-9]*$/u';

  /**
   * @var boolean whether the attribute value can be null or empty. Defaults to true,
   * meaning that if the attribute is empty, it is considered valid.
   */
  public $allowEmpty=true;

  public $pattern = self::PATTERN;

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
    if (!preg_match(self::PATTERN, $value)) {
      $message = $this->message !== null ? $this->message : $this->getErrorMessage();
      $this->addError($object, $attribute, $message);
    }
  }

  private function getErrorMessage() {
    switch ($this->pattern) {
      case self::PATTERN_ALFANUMERIC:
        $message = '{attribute} must be half-width alphanumeric characters.';
        break;
      default:
        $message = '{attribute} must be half-width characters.';
        break;
    }
    return Yii::t('batsg', $message);
  }
}
?>