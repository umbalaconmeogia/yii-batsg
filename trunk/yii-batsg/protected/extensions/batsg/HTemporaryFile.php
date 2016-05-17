<?php
/**
 * Manipulate temporary file.
 */
class HTemporaryFile
{
  const DEFAULT_FILE_PREFIX = 'batsg';

  /**
   * Generate a path to temporary file.
   *
   * @param string $prefix
   * @return string Return the file path.
   */
  public static function generateFilePath($prefix = self::DEFAULT_FILE_PREFIX)
  {
    $filePath = tempnam(sys_get_temp_dir(), $prefix);
    self::delete($filePath);
    return $filePath;
  }

  /**
   * Write a content to a temporary file.
   * @param string $content
   * @param string $filePath If not specified, then a new file is created.
   * @return string Return the file path.
   */
  public static function writeContentToFile($content, $filePath = NULL, $prefix = self::DEFAULT_FILE_PREFIX)
  {
    if (!$filePath) {
      $filePath = self::generateFilePath($prefix);
    }
    if (file_put_contents($filePath, $content) === FALSE) {
      throw new Exception("Error while write content to file $filePath");
    }
    return $filePath;
  }

  /**
   * Delete a temporary file.
   * @param string $filePath
   */
  public static function delete($filePath)
  {
    if (!unlink($filePath)) {
      throw new Exception("Error deleting file $filePath");
    }
  }
}
?>