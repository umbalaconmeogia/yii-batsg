<?php
class HFileTest extends CTestCase
{

  public function testRmdir() {
    // test checkDirExistance (remove unexist directory).
    HFile::rmdir('rootDir', FALSE); // Exception does not occur.
    $this->setExpectedException('InvalidArgumentException');
    HFile::rmdir('rootDir');
    
    // Remove real directory.
    $this->createDirForTest();
    HFile::rmdir('rootDir');
    $this->assertFalse(file_exists('rootDir'));
  }
  
  public function testListFile() {
    $this->createDirForTest();

    $expected = array(
      'file1' => 'rootDir/file1',
      'file2' => 'rootDir/file2',
    );
    $this->assertEquals($expected, HFile::listFile('rootDir'));

    $this->removeDirForTest();
  }
  
  public function testListDir()
  {
    $this->createDirForTest();

    $expected = array(
      'dir1' => 'rootDir/dir1',
      'dir2' => 'rootDir/dir2',
    );
    $this->assertEquals($expected, HFile::listDir('rootDir'));

    $this->removeDirForTest();
  }
  
  public function testFileExtension()
  {
    $this->assertNull(HFile::fileExtension('/etc'));
    $this->assertTrue('' === HFile::fileExtension('/etc.'));
    $this->assertEquals('ini', HFile::fileExtension('/etc.ini'));
    $this->assertEquals('ini', HFile::fileExtension('/etc.abc.ini'));
  }
  
  private function createDirForTest()
  {
    $this->removeDirForTest();
    mkdir('rootDir');
    mkdir('rootDir/dir1');
    mkdir('rootDir/dir2');
    touch('rootDir/file1');
    touch('rootDir/file2');
  }
  
  private function removeDirForTest()
  {
    HFile::rmdir('rootDir', FALSE);
  }
}
?>
