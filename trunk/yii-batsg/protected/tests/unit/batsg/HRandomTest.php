<?php
class HRandomTest extends CTestCase
{
  public function testGeneratePassword()
  {
    // Test default call.
    $password = HRandom::generatePassword();
    $this->assertEquals(strlen($password), 8);
    $this->assertTrue($this->isSpecifiedCharacterSet($password, HRandom::DEFAULT_CHARACTER_SET));
    // Call with specified length and character set.
    $length = 5;
    $characterSet = 'abc34';
    $password = HRandom::generatePassword($length, $characterSet);
    $this->assertEquals(strlen($password), $length);
    $this->assertTrue($this->isSpecifiedCharacterSet($password, $characterSet));
  }
  
  /**
   * Check if a string contains only characters in specified character set.
   * @param string $str the string to be checked.
   * @param string $characterSet
   * @return boolean
   */
  private function isSpecifiedCharacterSet($str, $characterSet)
  {
    foreach (str_split($str) as $c) {
      if (strpos($characterSet, $c) === FALSE) {
        return FALSE;
      }
    }
    return TRUE;
  }
}
?>