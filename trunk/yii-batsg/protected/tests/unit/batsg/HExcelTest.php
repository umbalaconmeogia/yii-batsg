<?php
class HExcelTest extends CTestCase
{
  public function testcolumnNumberToAlphabet()
  {
    $this->assertEquals('A', HExcel::columnNumberToAlphabet(0));
    $this->assertEquals('Z', HExcel::columnNumberToAlphabet(25));
    $this->assertEquals('AA', HExcel::columnNumberToAlphabet(26));
    $this->assertEquals('BA', HExcel::columnNumberToAlphabet(52));
    $this->assertEquals('A', HExcel::columnNumberToAlphabet(1, 1));
    $this->assertEquals('Z', HExcel::columnNumberToAlphabet(26, 1));
    $this->assertEquals('AA', HExcel::columnNumberToAlphabet(27, 1));
    $this->assertEquals('BA', HExcel::columnNumberToAlphabet(53, 1));
  }

  public function testcolumnAlphabetToNumber()
  {
    $this->assertEquals(0, HExcel::columnAlphabetToNumber('A'));
    $this->assertEquals(25, HExcel::columnAlphabetToNumber('Z'));
    $this->assertEquals(26, HExcel::columnAlphabetToNumber('AA'));
    $this->assertEquals(52, HExcel::columnAlphabetToNumber('BA'));
    $this->assertEquals(1, HExcel::columnAlphabetToNumber('A', 1));
    $this->assertEquals(26, HExcel::columnAlphabetToNumber('Z', 1));
    $this->assertEquals(27, HExcel::columnAlphabetToNumber('AA', 1));
    $this->assertEquals(53, HExcel::columnAlphabetToNumber('BA', 1));
  }
}?>