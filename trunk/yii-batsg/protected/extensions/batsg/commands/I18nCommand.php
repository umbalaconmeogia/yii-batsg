<?php
/**
 * Example usage:
 * <pre>
 *   yiic i18n generateMessageFiles --category=app --inputDir=. --outputDir=..
 * </pre>
 * @author umbalaconmeogia
 */
class I18nCommand extends CConsoleCommand
{
  /**
   * Generate message languages from 'app.csv'.
   * @param string $category CSV file, without .csv extension.
   * @param string $inputDir
   * @param string $outputDir Path to protected/messages
   */
  public function actionGenerateMessageFiles($category = 'app',
      $inputDir = '.',
      $outputDir = '.')
  {
    // Read data from csv.
    $i18nData = $this->readCsvData("$inputDir/$category.csv");
    // Write language files.
    foreach ($i18nData as $lang => $translation) {
      $this->writeLanguageFile($outputDir, $category, $lang, $translation);
    }
  }

  /**
   * Read data from app.csv.
   * @param string $messageCsvFile
   * @return array Translated text for all languages, in the type of $i18nData[$lang][$message] = translated text.
   */
  private function readCsvData($messageCsvFile)
  {
    // Open the file.
    $handle = fopen($messageCsvFile, "r");
    if ($handle === FALSE) {
      die("Error opening $file");
    }
    // Process header.
    $data = fgetcsv($handle);
    $columns = HBackup::parseColumnFromCsv($data);
    // Initiate the language data.
    $i18nData = array();
    foreach ($columns as $lang => $index) {
      $i18nData[$lang] = array();
    }
    // Read each line.
    while (($data = fgetcsv($handle)) !== FALSE) {
      $message = $data[0];
      foreach ($columns as $lang => $index) {
        $i18nData[$lang][$message] = $data[$index];
      }
    }
    // Sort by message.
    foreach ($i18nData as $lang => $translation) {
      ksort($i18nData[$lang]);
    }
    // Close the file.
    fclose($handle);
    return $i18nData;
  }

  /**
   * Generate the language file for a specified language.
   * This will create the file /protected/message/$lang/app.php.
   *
   * @param string $outputDir Path to protected/messages
   * @param string $category CSV file, without .csv extension.
   * @param string $lang 'vi' or 'ja' etc.
   * @param array $translation $translation['message'] = translated text.
   */
  private function writeLanguageFile($outputDir, $category, $lang, $translation)
  {
    // Generate language file content.
    $arrayData = var_export($translation, TRUE);
    $content = '';
    $content .= <<<EOT
<?php
/**
 * This is the language file for "$category" category.
 *
 * NOTICE: the message should be sorted by the alphabet.
 * When add new message to a language, synchronize it in all other languages.
 */
return $arrayData;
?>
EOT;
    // Write to language file.
    mb_language('ja');
    $dir = "$outputDir/$lang";
    // Create directory if not exist.
    if (!file_exists($dir) || !is_dir($dir)) {
      mkdir($dir);
    }
//    $content = mb_convert_encoding($content, 'UTF-8', 'SJIS');
    file_put_contents("$dir/$category.php", $content);
  }
}
?>