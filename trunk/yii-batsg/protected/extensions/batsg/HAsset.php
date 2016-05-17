<?php
/**
 * Manipulate assets path in <code>protected/assets</code>.
 * @author Thanh
 * @deprecated Consider using HAssets instead.
 */
class HAsset
{
  private static $_assetsBase;


  /**
   * Get base path of asset files.
   * <p>
   * This help to release resource files (css, image...) with updating version.
   * <p>
   * Usage example:
   * <p>
   *   <code>echo HAsset::assetsBase() . '/css/app.css';</code>
   */
  public static function assetsBase()
  {
    if (self::$_assetsBase === NULL) {
      self::$_assetsBase = Yii::app()->assetManager->publish(
        Yii::getPathOfAlias('application.assets'),
        false,
        -1,
        YII_DEBUG
      );
    }
    return self::$_assetsBase;
  }

  /**
   * Get full path to assets file.
   * <p>
   * Usage example:
   * <p>
   *   <code>echo HAsset::assetsPath('/css/app.css');</code>
   * @param string $path Path to an css, js or image files...
   * @return string
   */
  public static function assetsPath($path) {
    return self::assetsBase() . $path;
  }

  /**
   * Register javascript file in assets folder.
   * @param string $path Path to js file.
   */
  public static function registerScript($path)
  {
    Yii::app()->clientScript->registerScriptFile(self::assetsPath($path), CClientScript::POS_BEGIN);
  }

  /**
   * Register css file in assets folder.
   * @param string $path Path to js file.
   */
  public static function registerCss($path)
  {
    Yii::app()->clientScript->registerCssFile(self::assetsPath($path));
  }
}
?>