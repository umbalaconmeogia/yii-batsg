<?php
/**
 * Manipulate assets path.
 * @author Thanh
 */
class HAssets
{
  private $_assetsBase;

  /**
   * Create HAssets to manipulate assets of extension yii-batsg (this extension).
   * @return HAssets
   */
  public static function batsgHAssets()
  {
    return new HAssets(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'assets');
  }

  /**
   * @param string $assetsFolderPath A path (if not contains directory separator)
   *                                 or alias (do not contains directory separator.
   *                                 Set to <code>protected/assets</code> by default.
   */
  public function __construct($assetsFolderPath = 'application.assets')
  {
    if (strpos($assetsFolderPath, '/') === FALSE && strpos($assetsFolderPath, '\\') === FALSE) {
      $assetsFolderPath = Yii::getFrameworkPath($assetsFolderPath);
    }
    $this->_assetsBase = Yii::app()->assetManager->publish($assetsFolderPath, FALSE, -1, YII_DEBUG);
  }

  /**
   * Get base path of asset files.
   * <p>
   * This help to release resource files (css, image...) with updating version.
   * <p>
   * Usage example:
   * <p>
   *   <code>echo $hAssets::assetsBase() . '/css/app.css';</code>
   */
  public function assetsBase()
  {
    return $this->_assetsBase;
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
  public function assetsPath($path) {
    return $this->assetsBase() . $path;
  }

  /**
   * Register javascript file in assets folder.
   * @param string $path Path to js file.
   */
  public function registerScript($path)
  {
    Yii::app()->clientScript->registerScriptFile($this->assetsPath($path), CClientScript::POS_BEGIN);
  }

  /**
   * Register css file in assets folder.
   * @param string $path Path to js file.
   */
  public static function registerCss($path)
  {
    Yii::app()->clientScript->registerCssFile($this->assetsPath($path));
  }
}
?>