<?php
Yii::import('application.vendor.PHPExcel.Classes.PHPExcel');
$temp = new PHPExcel(); // To load auto load.

/**
 * Helper to access PHPExcel (http://phpexcel.codeplex.com/).
 * To use this, should modify PHPExcel_Autoloader::Register() as below
 * <code>
 * public static function Register() {
 *   return spl_autoload_register(array('PHPExcel_Autoloader', 'Load'), false, true);
 * }
 * </code>
 */
class HPhpExcel
{
  /**
   * @param string $file
   * @return PHPExcel
   */
  public static function load($file)
  {
    return PHPExcel_IOFactory::load($file);
  }

  /**
   * @param string $sourceFile
   * @param string $writerType
   * @return PHPExcel_Writer_IWriter
   */
  public static function createWriter($excel, $writerType)
  {
    if (is_string($excel)) {
      $excel = self::load($excel);
    }
    return PHPExcel_IOFactory::createWriter($excel, $writerType);
  }

  public static function removeSheetByName(PHPExcel $excel, $sheetName)
  {
    foreach ($excel->getSheetNames() as $index => $name) {
      if ($sheetName == $name) {
        $excel->removeSheetByIndex($index);
        break;
      }
    }
  }

  /**
   * @param string $file
   * @return PHPExcel_Worksheet
   */
  public static function openExcel($file) {
    $objPHPExcel = new PHPExcel(); // Load PHPExcel classes.

    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getActiveSheet();

    return $sheet;
  }

  /**
   * Get excel cell data.
   * @param PHPExcel_Worksheet $sheet
   * @param int $rowIndex Row number (base 1).
   * @param mixed $colIndex Column name (string, 'A' or 'AB' for example) or column number (base 0).
   * @return string
   */
  public static function getCell($sheet, $rowIndex, $colIndex) {
    $value = is_numeric($colIndex) ?
        $sheet->getCellByColumnAndRow($colIndex, $rowIndex) :
        $sheet->getCell("$colIndex$rowIndex");
    return trim($value);
  }

  /**
   * Get value of a work sheet as two dimension array.
   * @param string[][] $sheet
   */
  public static function toArray($sheet)
  {
    return $sheet->toArray(null,true,true,true);
  }

  /**
   * Set sheet title. This will convert invalid character to _.
   * @param PHPExcel_Worksheet $sheet
   * @param string $sheetTitle
   */
  public static function setSheetTitle(PHPExcel_Worksheet $sheet, $sheetTitle)
  {
    $invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']', '＊', '：', '／', '￥', '？', '［', '］');
    $sheetTitle = str_replace($invalidCharacters, '_', $sheetTitle);
    $sheet->setTitle($sheetTitle);
  }
}
?>