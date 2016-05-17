<?php
/**
 * Class HRandom
 */
class HRandom
{
  /**
   * Default characters that is used to generate password.
   * @var string
   */
  const DEFAULT_CHARACTER_SET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

  /**
   * Generate a HRandom password with specified length.
   * E.g:
   * <code>
   *   HRandom::generatePassowrd(4);
   * </code>
   * will generage a HRandom string like 'z7a4'
   *
   * @param int $length the length of the password to be generated.
   * @param string $characterSet the string that contains characters to be used.
   * @return string The password
   */
  static public function generatePassword($length = 8, $characterSet = self::DEFAULT_CHARACTER_SET) {
    $randMax = strlen($characterSet) - 1;
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
      $pass .= substr($characterSet, rand(0, $randMax), 1);
    }
    return $pass;
  }
}
?>