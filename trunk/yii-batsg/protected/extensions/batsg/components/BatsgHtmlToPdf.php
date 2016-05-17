<?php
/**
 * Using wkhtmltopdf tool to create pdf file from html.
 *
 * The following options are set by default:
 *   - marginTop, marginBottom, marginLeft, marginRight are set to 15mm.
 *   - pageSize set to A4.
 *   - smartShrinking is disabled.
 *
 * @property string orientation Landscape or Portrait.
 * @property string source
 * @property string dest
 * @property string executablePath
 * @property string executableAlias
 *
 * @author umbalaconmeogia
 */
class BatsgHtmlToPdf extends CApplicationComponent
{
  const OPTION_NAME_MARGIN_TOP = '--margin-top';
  const OPTION_NAME_MARGIN_BOTTOM = '--margin-bottom';
  const OPTION_NAME_MARGIN_LEFT = '--margin-left';
  const OPTION_NAME_MARGIN_RIGHT = '--margin-right';
  const OPTION_NAME_PAGE_SIZE = '--page-size';
  const OPTION_NAME_ORIENTATION = '--orientation';

  const OPTION_NAME_DISABLE_SMART_SHRINKING = '--disable-smart-shrinking';
  const OPTION_NAME_ENABLE_SMART_SHRINKING = '--enable-smart-shrinking';

  const DEFAULT_MARGIN = '15mm';
  const DEFAULT_PAGE_SIZE = 'A4';

  /**
   * @var string HTML source file or URL to be converted to PDF.
   */
  private $_source;

  /**
   * @var string Output pdf file to be created.
   */
  private $_dest;

  /**
   * @var string wkhtmltopdf executable.
   */
  private $_executablePath;

  /**
   * @var string wkhtmltopdf executable alias.
  */
  private $_executableAlias;

  /**
   * @var string[] Parameters set to wkhtmltopdf.
   */
  private $_params = array();

  public function init()
  {
    parent::init();

    // Set default option.
    $defaultOptions = array(
      self::OPTION_NAME_MARGIN_TOP => self::DEFAULT_MARGIN,
      self::OPTION_NAME_MARGIN_BOTTOM => self::DEFAULT_MARGIN,
      self::OPTION_NAME_MARGIN_LEFT => self::DEFAULT_MARGIN,
      self::OPTION_NAME_MARGIN_RIGHT => self::DEFAULT_MARGIN,
      self::OPTION_NAME_PAGE_SIZE => self::DEFAULT_PAGE_SIZE,
      self::OPTION_NAME_ENABLE_SMART_SHRINKING,
    );
    foreach ($defaultOptions as $option => $value) {
      if (is_numeric($option)) {
        $this->setOption($value);
      } else {
        $this->setOption($option, $value);
      }
    }
  }

  /**
   * @param string $optionName wkhtmltopdf option name. For example: --margin-top or -T (the two options set the page top margin)
   * @param string $value If NULL, then value is not set in to option.
   */
  public function setOption($optionName, $value = NULL)
  {
    if ($value !== NULL) {
      $this->_params[$optionName] = $value;
    } else {
      if (!isset($this->_params[$optionName])) {
        $this->_params[] = $optionName;
      }
    }
  }

  /**
   * Remove wkhtmltopdf option.
   * @param string $optionName
   * @return BatsgHtmlToPdf Return the object itself.
   */
  public function removeOption($optionName)
  {
    if (isset($this->_params[$optionName])) {
      unset($this->_params[$optionName]);
    } else {
      if (($key = array_search($optionName, $this->_params)) !== FALSE) {
        unset($this->_params[$key]);
      }
    }
  }

  /**
   * @param boolean $value
   * @return BatsgHtmlToPdf Return the object itself.
   */
  public function setSmartShrink($value)
  {
    if ($value) {
      $this->removeOption(self::OPTION_NAME_DISABLE_SMART_SHRINKING);
      $this->setOption(self::OPTION_NAME_ENABLE_SMART_SHRINKING);
    } else {
      $this->setOption(self::OPTION_NAME_DISABLE_SMART_SHRINKING);
      $this->removeOption(self::OPTION_NAME_ENABLE_SMART_SHRINKING);
    }
  }

  public function setOrientation($value)
  {
    $this->setOption(self::OPTION_NAME_ORIENTATION, $value);
  }

  /**
   * @return string
   */
  public function getSource()
  {
    return $this->_source;
  }

  /**
   * @param string $source
   */
  public function setSource($source)
  {
    $this->_source = $source;
  }

  /**
   * @return string
   */
  public function getDest()
  {
    return $this->_dest;
  }

  /**
   * @param string $source
   */
  public function setDest($dest)
  {
    $this->_dest = $dest;
  }

  /**
   * @return string
   */
  public function getExecutableAlias()
  {
    return $this->_executableAlias;
  }

  /**
   * @param string $executableAlias
   */
  public function setExecutableAlias($executableAlias)
  {
    $this->_executableAlias = $executableAlias;
  }

  /**
   * @return string
   */
  public function getExecutablePath()
  {
    if (!$this->_executablePath && $this->_executableAlias) {
      $this->_executablePath = Yii::getPathOfAlias($this->_executableAlias);
    }
    return $this->_executablePath;
  }

  /**
   * @param string $executablePath
   */
  public function setExecutablePath($executablePath)
  {
    $this->_executablePath = $executablePath;
  }

  /**
   * @return string
   */
  private function generateCommand()
  {
    $this->checkMandatory('executablePath');
    $this->checkMandatory('source');
    $this->checkMandatory('dest');

    $params = array($this->_executablePath);
    foreach ($this->_params as $option => $value) {
      if (is_numeric($option)) {
        $params[] = $value;
      } else {
        $params[] = "$option $value";
      }
    }
    $params[] = $this->_source;
    $params[] = $this->_dest;
    return implode(' ', $params);
  }

  private function checkMandatory($property)
  {
    if (!$this->$property) {
      throw new Exception("Property \"$property\" is not set.");
    }
  }

  /**
   * Generate the pdf file from set parameters.
   * @return string the created file path.
   */
  private function exec()
  {
    $command = $this->generateCommand();
    Yii::log("Run $command");
    exec($command);
  }

  /**
   * Convert html content to pdf file.
   * @param string $htmlContent HTML code.
   * @return string Created pdf file content.
   */
  public function htmlContentToPdf($htmlContent)
  {
    $tempFilePrefix = Yii::app()->params['tempFilePrefix'];
    // Write html content to temporary file.
    $htmlFile = HTemporaryFile::generateFilePath($tempFilePrefix) . '.html';
    $htmlFile = HTemporaryFile::writeContentToFile($htmlContent, $htmlFile);
    // Create pdf output temporary file.
    $pdfFile = HTemporaryFile::generateFilePath($tempFilePrefix) . '.pdf';
    // Convert html to pdf.
    $this->setSource($htmlFile);
    $this->setDest($pdfFile);
    $this->exec();
    // Get pdf content.
    $pdfContent = file_get_contents($pdfFile);
    // Delete html and pdf temporary.
    HTemporaryFile::delete($htmlFile);
    HTemporaryFile::delete($pdfFile);

    return $pdfContent;
  }
}
?>