<?php
class HDownload
{
  const MIME_EXCEL_5 = 'application/vnd.ms-excel';
  const MIME_EXCEL_2007 = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

  /**
   * Export a file, make browser to download it.
   * @param string $fileName
   * @param string $content
   * @param string $contentType
   */
  public static function downloadFile($fileName, $content, $contentType)
  {
    header("Content-Type: $contentType");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    echo $content;
  }

  public static function downloadPdf($fileName, $content)
  {
    self::downloadFile($fileName, $content, 'application/pdf');
  }

  public static function downloadXls($fileName, $content)
  {
    $ext = HFile::fileExtension($fileName);
    $mime = $ext == 'xlsx' ? self::MIME_EXCEL_2007 : self::MIME_EXCEL_5;
    self::downloadFile($fileName, $content, $mime);
  }

  /**
   * @param string $fileName
   * @param string $content
   */
  public static function downloadCsv($fileName, $content)
  {
    self::downloadFile($fileName, $content, 'application/octet-stream');
  }

  /**
   * Generate a CSV file from $data, then make downloading it as specified CSV file.
   * @param string $fileName
   * @param string[] $data
   * @param boolean $writeUtf8Bom
   */
  public static function downloadCsvArray($fileName, $contentArray, $writeUtf8Bom = TRUE)
  {
    self::downloadCsv($fileName, self::createCsvFileContent($contentArray, $writeUtf8Bom));
  }

  /**
   * Ref: http://xirasaya.com/?m=detail&hid=407
   * @param array $csv = null
   * @return string
   */
  public static function createCsvFileContent($csv = null, $writeUtf8Bom = TRUE) {
    $buf = null;
    if (is_array($csv)) {
      $fp = fopen('php://memory', 'rw+');
      if ($writeUtf8Bom) { //Write BOM
        fwrite($fp, pack('C*', 0xEF, 0xBB, 0xBF));
      }
      foreach($csv as $fields) {
        fputcsv($fp, $fields);
      }
      rewind($fp);
      $buf = stream_get_contents($fp);
      fclose($fp);
    }
    return $buf;
  }
}
?>