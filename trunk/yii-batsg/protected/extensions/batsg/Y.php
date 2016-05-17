<?php
class Y
{
  /**
   * @var string default category to be used in Yii::t().
   */
  public static $i18nDefaultCategory = 'app';

  /**
   * @var string URL parameter to specify language.
   */
  public static $urlParamLanguage = 'language';
  
  /**
   * Call Yii::t().
   * @param string $mesage
   * @param string $category use self::$i18nDefaultCategory if not specified.
   * @param array $params
   * @return string
   */
  public static function t($message, $params = array(), $category = NULL)
  {
    if ($category === NULL) {
      $category = self::$i18nDefaultCategory;
    }
    return Yii::t($category, $message, $params);
  }
  
  /**
   * Display a translate text by echo.
   * @param string $mesage
   * @param string $category use self::$i18nDefaultCategory if not specified.
   * @param array $params
   */
  public static function et($message, $params = array(), $category = NULL)
  {
    if ($category === NULL) {
      $category = self::$i18nDefaultCategory;
    }
    echo Yii::t($category, $message, $params);
  }

  /**
   * Set a flash message with the key "error".
   * @param string $message
   */
  public static function setFlashError($message)
  {
    Yii::app()->user->setFlash('error', $message);
  }

  /**
   * Set a flash message with the key "error".
   * The message is translated by Y::t().
   * @param string $message
   */
  public static function setFlashErrorT($message)
  {
    Yii::app()->user->setFlash('error', Y::t($message));
  }
  
  /**
   * Set a flash message with the key "notice".
   * @param string $message
   */
  public static function setFlashNotice($message)
  {
    Yii::app()->user->setFlash('notice', $message);
  }
  
  /**
   * Set a flash message with the key "notice".
   * The message is translated by Y::t().
   * @param string $message
   */
  public static function setFlashNoticeT($message)
  {
    Yii::app()->user->setFlash('notice', Y::t($message));
  }
  
  /**
   * Set a flash message with the key "success".
   * @param string $message
   */
  public static function setFlashSuccess($message)
  {
    Yii::app()->user->setFlash('success', $message);
  }
  
  /**
   * Set a flash message with the key "success".
   * The message is translated by Y::t().
   * @param string $message
   */
  public static function setFlashSuccessT($message)
  {
    Yii::app()->user->setFlash('success', Y::t($message));
  }
  
  /**
   * Wrapper to access to Yii::app()->params
   * 
   * If you intent to use Yii::app()->params['arrayName'] which returns an array in foreach,
   * it will raise error. Use Y::params('arrayName') instead.
   * 
   * @param param1 [, param2...]
   * @return mixed
   */
  public static function params()
  {
    $params = Yii::app()->params;
    foreach (func_get_args() as $arg) {
      $params = $params[$arg];
    }
    return $params;
  }
//
//  /**
//   * Translate a value in Yii::app()->params.
//   * 
//   * @param param1 [, param2...]
//   * @return string
//   */
//  public static function paramsT()
//  {
//    $params = Yii::app()->params;
//    foreach (func_get_args() as $arg) {
//      $params = $params[$arg];
//    }
//    return Y::t($params);
//  }
//
//  /**
//   * Display the translation of a value in Yii::app()->params.
//   * 
//   * @param param1 [, param2...]
//   * @return string
//   */
//  public static function paramsEt()
//  {
//    $params = Yii::app()->params;
//    foreach (func_get_args() as $arg) {
//      $params = $params[$arg];
//    }
//    Y::et($params);
//  }
  
  /**
   * Translate array's values by Y::t().
   * 
   * This is often used to translate the i18n value defined in Yii::app()->params, for example:
   * $translatedArray = Y::translateArrayValue(Y::params('arrayName'));
   * 
   * @param array $array
   * @return array
   */
  public static function translateArrayValue($array)
  {
    $arr = array();
    foreach ($array as $key => $value) {
      $arr[$key] = Y::t($value);
    }
    return $arr;
  }

  /**
   * Set the language.
   * @param string $language if this parameter is set, then it is used
   *                         for this request only ignoring the URL parameter.
   */
  public static function setLanguage($language = NULL)
  {
    // Use language if specified.
    if ($language) {
      Yii::app()->language = $language;
      return;
    }

    // Check if user specify language via URL parameter.
    if (isset($_REQUEST[self::$urlParamLanguage])) {
      Yii::app()->session[self::$urlParamLanguage] = $_REQUEST[self::$urlParamLanguage];
    }
    // Set language.
    if (isset(Yii::app()->session[self::$urlParamLanguage])) {
      Yii::app()->language = Yii::app()->session[self::$urlParamLanguage];
    }
  }
  
  /**
   * Generate url for language setting.
   * @param array $languages $language[$languageCode] = Language name.
   * @return array <languageCode> => url
   */
  public static function languageSettingUrls($languages)
  {
    $urls = array();
    $route = Yii::app()->urlManager->parseUrl(Yii::app()->request); // Route for createUrl().
    $app = Yii::app(); // CApplication
    $getParam = $_GET; // Copy $_GET
    foreach ($languages as $code => $name) {
      $getParam[self::$urlParamLanguage] = $code;
      $urls[$code] = $app->createUrl($route, $getParam);
    }
    return $urls;
  }
  
  /**
   * Generate url for language setting.
   * @param array $languages $language[$languageCode] = Language name.
   * @param string $separator Separator between HTML links.
   * @return string
   */
  public static function languageSettingLinks($languages, $separator = ' ')
  {
    $htmls = array();
    $urls = Y::languageSettingUrls($languages);
    foreach ($urls as $code => $url) {
      $languageName = $languages[$code];
      if ($code == Yii::app()->language) {
        $htmls[] = $languageName;
      } else {
        $htmls[] = CHtml::link($languageName, $url);
      }
    }
    return join($separator, $htmls);
  }
}
?>