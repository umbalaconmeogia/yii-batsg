<?php
/**
 * Alternative class of CHtml.
 *
 * @author thanh <umbalaconmeogia@gmail.com>
 */
class HHtml
{
  /**
   * Used in modelIndex()
   * @var int
   */
  private static $modelIndexCounter = 0;

  /**
   * Renders a radio button list for a model attribute.
   * This method is a wrapper of {@link CHtml::activeRadioButtonList}.
   * Please check {@link CHtml::activeRadioButtonList} for detailed information
   * about the parameters for this method.
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $data value-label pairs used to generate the radio button list.
   * @param array $htmlOptions addtional HTML options.
   * @param boolean $translateData tranlsate $data value if set to TRUE.
   * @return string the generated radio button list
   */
  public static function activeRadioButtonList($model, $attribute, $data,
      $htmlOptions = array('separator' => ' ', 'labelOptions' => array('style' => 'display: inline; font-weight: normal;')),
      $translateData = TRUE)
  {
    if ($translateData) {
      $data = Y::translateArrayValue($data);
    }
    return CHtml::activeRadioButtonList($model, $attribute, $data, $htmlOptions);
  }

  /**
   * Renders a checkbox list for a model attribute.
   * This method is a wrapper of {@link CHtml::activeCheckBoxList}.
   * Please check {@link CHtml::activeCheckBoxList} for detailed information
   * about the parameters for this method.
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $data value-label pairs used to generate the check box list.
   * @param array $htmlOptions addtional HTML options.
   * @param boolean $translateData tranlsate $data value if set to TRUE.
   * @return string the generated check box list
   */
  public static function activeCheckBoxList($model, $attribute, $data,
      $htmlOptions = array('separator' => ' ', 'labelOptions' => array('style' => 'display: inline; font-weight: normal;')),
      $translateData = TRUE)
  {
    if ($translateData) {
      $data = Y::translateArrayValue($data);
    }
    return CHtml::activeCheckBoxList($model, $attribute, $data, $htmlOptions);
  }

  public static function activeLabelEx($model, $attribute,
      $htmlOptions = array('style' => 'display: inline; font-weight: normal;'))
  {
    return CHtml::activeLabelEx($model, $attribute, $htmlOptions);
  }

  /**
   * Display a checkbox that is "readonly" (user cannot click on it).
   * This is used mainly for displaying data as checkbox.
   * @param string $name
   * @param boolean $checked
   * @param array $htmlOptions addtional HTML options.
   */
  public static function readOnlyCheckBox($name, $checked, $htmlOptions = array())
  {
    $htmlOptions['onclick'] = 'return false;';
    $htmlOptions['onkeydown'] = 'return false;';
    return CHtml::checkBox($name, $checked, $htmlOptions);
  }

  /**
   * Display a checkbox that is "readonly" (user cannot click on it).
   * This is used mainly for displaying data as checkbox.
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $htmlOptions addtional HTML options.
   */
  public static function activeReadOnlyCheckBox($model, $attribute, $htmlOptions = array())
  {
    $htmlOptions['onclick'] = 'return false;';
    $htmlOptions['onkeydown'] = 'return false;';
    return CHtml::activeCheckBox($model, $attribute, $htmlOptions);
  }

  /**
   * Generates a check box list.
   * This method is a wrapper of {@link CHtml::activeCheckBoxList}.
   * Please check {@link CHtml::activeCheckBoxList} for detailed information
   * about the parameters for this method.
   * @param string $name name of the check box list
   * @param mixed $select selection of the check boxes.
   * @param array $data value-label pairs used to generate the check box list.
   * @param array $htmlOptions addtional HTML options.
   * @param boolean $translateData tranlsate $data value if set to TRUE.
   * @return string the generated check box list
   */
  public static function checkBoxList($name, $select, $data,
      $htmlOptions = array('separator' => ' ', 'labelOptions' => array('style' => 'display: inline; font-weight: normal;')),
      $translateData = TRUE)
  {
    if ($translateData) {
      $data = Y::translateArrayValue($data);
    }
    return CHtml::checkBoxList($name, $select, $data, $htmlOptions);
  }

  /**
   * Get cycling value.
   * @param int $counter
   * @param array $values
   * @return mixed
   */
  public static function cycle(&$counter, $values = array('even', 'odd'))
  {
    $value = isset($values[$counter]) ? $values[$counter] : NULL;
    $counter = ($counter + 1) % count($values);
    return $value;
  }

  /**
   * Echo class="odd" or class="even"
   * @param int $counter
   * @param boolean $echo
   * @param array $values
   */
  public static function cycleClass(&$counter, $echo = TRUE, $values = array('even', 'odd'))
  {
    $cssClass = 'class="' . self::cycle($counter, $values) . '"';
    if ($echo) {
      echo $cssClass;
    }
    return $cssClass;
  }

  /**
   * Generate a link display as button.
   * See CHtml::link() for the parameters' detail.
   * @param string $text link body. This is changed to button tag.
   * @param mixed $url a URL or an action route that can be used to create a URL.
   * @param array $htmlOptions additional HTML attributes.
   * @return string the generated hyperlink
   */
  public static function buttonLink($text, $url='#', $htmlOptions = array())
  {
    if (!isset($htmlOptions['onclick'])) {
      $url = CHtml::normalizeUrl($url);
      $htmlOptions['onclick'] = "window.location='$url'";
    }
    return CHtml::button($text, $htmlOptions);
  }

  /**
   * Generate a button with GET.
   * See CHtml::button() for the parameters' detail.
   * @param string $label The button label.
   * @param array $htmlOptions additional HTML attributes.
   * @return string the generated hyperlink
   */
//   public static function button($label = 'button', array $htmlOptions = array())
//   {
//     if (!isset($htmlOptions['onclick']) && isset($htmlOptions['submit'])) {
//       $url = CHtml::normalizeUrl($htmlOptions['submit']);
//       $htmlOptions['onclick'] = "window.location='$url'";
//       unset($htmlOptions['submit']);
//     }
//     return CHtml::button($label, $htmlOptions);
//   }

  /**
   * Generate a link display as button.
   * See CHtml::link() for the parameters' detail.
   * @param string $text link body. This is changed to button tag.
   * @param mixed $url a URL or an action route that can be used to create a URL.
   * @param array $htmlOptions additional HTML attributes.
   * @return string the generated hyperlink
   */
  public static function htmlButtonLink($text, $url='#', $htmlOptions = array())
  {
    if (!isset($htmlOptions['onclick'])) {
      $url = CHtml::normalizeUrl($url);
      $htmlOptions['onclick'] = "window.location='$url'";
    }
    return CHtml::htmlButton($text, $htmlOptions);
  }

  /**
   * Display hidden fields.
   * @param CActiveRecord $model
   * @param string $index
   * @param mixed $fields NULL, or string (fields name), or array of fieldNames.
   * @param array $htmlOptions
   */
  public static function activeHiddenFields($model, $index = NULL, $fields = NULL, $htmlOptions = array())
  {
    // Output all attributes if $fields is not specified.
    if (!$fields) {
      $fields = array_keys($model->attributes);
    }
    // Wrap $fields by array if only string specified.
    if (!is_array($fields)) {
      $fields = array($fields);
    }
		$html = '';
    foreach ($fields as $field) {
      $attribute = $index !== NULL ? "[$index]$field" : $field;
      // Add class to html options.
      $options = $htmlOptions;
      if (isset($options['class'])) {
        $options['class'] .= " $field";
      } else {
        $options['class'] = "$field";
      }
      $html .= CHtml::activeHiddenField($model, $attribute, $options) . "\n";
    }
		return $html;
  }

  public static function showHiddenFields($model, $index = NULL, $fields = NULL, $htmlOptions = array())
  {
		echo self::activeHiddenFields($model, $index, $fields, $htmlOptions);
	}

  /**
   * Generate an index for model list element.
   * If primary key is set, then primary key is used, else a random string is generated.
   * @param BaseModel $model
   * @param string $pk
   * @return string
   */
  public static function modelIndex($model, $pk = 'id')
  {
    return $model->$pk ? $model->$pk : (++self::$modelIndexCounter) . '_' . HRandom::generatePassword(2);
  }

  /**
   * Format long text to display.
   * This will use CHtml::encode and convert nl2br.
   * @param string $remarks
   */
  public static function longText($text)
  {
    return nl2br(CHtml::encode($text));
  }
}
?>