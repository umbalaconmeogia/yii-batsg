<?php
class HDateTimeTest extends CTestCase
{
  public function testCreateFromString()
  {
    // Date only
    $str = '2011/5/7';
    $dateTime = HDateTime::createFromString($str);
    $this->assertDateTime(array(2011, 5, 7, 0, 0, 0), $dateTime);
    // Date and hour, minute
    $str = '2011/5/7 11:22';
    $dateTime = HDateTime::createFromString($str);
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 0), $dateTime);
    // Date and hour, minute, second
    $str = '2011/5/7 11:22:33';
    $dateTime = HDateTime::createFromString($str);
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 33), $dateTime);
    // Date (hyphen) and hour, minute, second
    $str = '2011-05-07 11:22:33';
    $dateTime = HDateTime::createFromString($str);
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 33), $dateTime);
  }

  public function testCreateFromYmdHms()
  {
    $dateTime = HDateTime::createFromYmdHms(2011, 5, 7);
    $this->assertDateTime(array(2011, 5, 7, 0, 0, 0), $dateTime);
    $dateTime = HDateTime::createFromYmdHms(2011, 5, 7, 11, 22, 33);
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 33), $dateTime);
  }

  public function testCreateFromTimestamp()
  {
    $dateTime = HDateTime::createFromTimestamp(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 33), $dateTime);
  }

  public function testNow()
  {
    $now = getdate();
    $dateTime = HDateTime::now();
    $arr = array($now['year'], $now['mon'], $now['mday'], $now['hours'], $now['minutes'], $now['seconds']);
    $this->assertDateTime($arr, $dateTime);
  }

  public function testConstruct()
  {
    $nowTimestamp = time();
    $now = getdate($nowTimestamp);
    $dateTime = new HDateTime($nowTimestamp);
    $arr = array($now['year'], $now['mon'], $now['mday'], $now['hours'], $now['minutes'], $now['seconds']);
    $this->assertDateTime($arr, $dateTime);
    $this->assertEquals($now['wday'], $dateTime->getWDay());
    $this->assertEquals($nowTimestamp, $dateTime->getTimestamp());
  }

  public function testReset()
  {
    $dateTime = HDateTime::now();
    $dateTime->reset(2011, 5, 6, 11, 22, 33);
    $this->assertDateTime(array(2011, 5, 6, 11, 22, 33), $dateTime);
  }

  public function testResetByTimestamp()
  {
    $dateTime = HDateTime::now();
    $timestamp = mktime(11, 22, 33, 5, 7, 2011);
    $dateTime->resetByTimestamp($timestamp);
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 33), $dateTime);
  }

  public function testGetElements()
  {
    $timestamp = mktime(11, 22, 33, 5, 7, 2011);
    $arr = getdate($timestamp);
    $dateTime = new HDateTime($timestamp);
    $this->assertEquals($arr['year'], $dateTime->getYear());
    $this->assertEquals($arr['mon'], $dateTime->getMonth());
    $this->assertEquals($arr['mday'], $dateTime->getDay());
    $this->assertEquals($arr['hours'], $dateTime->getHour());
    $this->assertEquals($arr['minutes'], $dateTime->getMinute());
    $this->assertEquals($arr['seconds'], $dateTime->getSecond());
    $this->assertEquals($arr['wday'], $dateTime->getWDay());
    $this->assertEquals($timestamp, $dateTime->getTimestamp());
  }

  public function testToString()
  {
    $timestamp = mktime(1, 2, 3, 5, 7, 2011);
    $dateTime = new HDateTime($timestamp);
    // __toString()
    $this->assertEquals('2011-05-07 01:02:03', $dateTime . "");
    // toString()
    $this->assertEquals('2011-05-07 01:02:03', $dateTime->toString());
    $this->assertEquals('2011-05-07', $dateTime->toString(HDateTime::FORMAT_DATE));
    $this->assertEquals('01:02:03', $dateTime->toString(HDateTime::FORMAT_TIME));
    $this->assertEquals('2011/05/07 01:02:03', $dateTime->toString('Y/m/d H:i:s'));
  }

  public function testFirstDayOfMonth()
  {
    $timestamp = mktime(1, 2, 3, 5, 7, 2011);
    $dateTime = new HDateTime($timestamp);
    $this->assertDateTime(array(2011, 5, 1, 0, 0, 0), $dateTime->firstDayOfMonth());
  }

  public function testLastDayOfMonth()
  {
    // Month that has 31 days.
    $dateTime = new HDateTime(mktime(1, 2, 3, 5, 7, 2011));
    $this->assertDateTime(array(2011, 5, 31, 0, 0, 0), $dateTime->lastDayOfMonth());
    // Month that has 30 days.
    $dateTime = new HDateTime(mktime(1, 2, 3, 4, 7, 2011));
    $this->assertDateTime(array(2011, 4, 30, 0, 0, 0), $dateTime->lastDayOfMonth());
    // Month that has 28 days.
    $dateTime = new HDateTime(mktime(1, 2, 3, 2, 7, 2011));
    $this->assertDateTime(array(2011, 2, 28, 0, 0, 0), $dateTime->lastDayOfMonth());
    // Month that has 29 days.
    $dateTime = new HDateTime(mktime(1, 2, 3, 2, 7, 2012));
    $this->assertDateTime(array(2012, 2, 29, 0, 0, 0), $dateTime->lastDayOfMonth());
  }

  public function testDate()
  {
    // Month that has 31 days.
    $dateTime = new HDateTime(mktime(1, 2, 3, 5, 7, 2011));
    $this->assertDateTime(array(2011, 5, 7, 0, 0, 0), $dateTime->date());

  }

  public function testAdd()
  {
    // add
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    // add with no modifying
    $this->assertDateTime(array(2012, 7, 10, 12, 24, 36), $dateTime->add(1, 2, 3, 1, 2, 3));
    $this->assertDateTime(array(2011, 5, 7, 11, 22, 33), $dateTime);
    // add with modifying
    $this->assertDateTime(array(2012, 7, 10, 12, 24, 36), $dateTime->add(1, 2, 3, 1, 2, 3, TRUE));
    $this->assertDateTime(array(2012, 7, 10, 12, 24, 36), $dateTime);
    // nextNYear
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertEquals(2012, $dateTime->nextNYear(1)->getYear());
    $this->assertEquals(2010, $dateTime->nextNYear(-1)->getYear());
    // nextNMonth
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertEquals(6, $dateTime->nextNMonth(1)->getMonth());
    $this->assertEquals(4, $dateTime->nextNMonth(-1)->getMonth());
    // nextNDay
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertEquals(8, $dateTime->nextNDay(1)->getDay());
    $this->assertEquals(6, $dateTime->nextNDay(-1)->getDay());
    // nextNHour
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertEquals(12, $dateTime->nextNHour(1)->getHour());
    $this->assertEquals(10, $dateTime->nextNHour(-1)->getHour());
    // nextNMinute
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertEquals(23, $dateTime->nextNMinute(1)->getMinute());
    $this->assertEquals(21, $dateTime->nextNMinute(-1)->getMinute());
    // nextNSecond
    $dateTime = new HDateTime(mktime(11, 22, 33, 5, 7, 2011));
    $this->assertEquals(34, $dateTime->nextNSecond(1)->getSecond());
    $this->assertEquals(32, $dateTime->nextNSecond(-1)->getSecond());
  }

  /**
   * Assert that a HDateTime object has elements equals to specified values.
   * @param int[] $arr array of year, month, day, hour, minute, second
   * @param HDateTime $dateTime
   */
  private function assertDateTime($arr, $dateTime)
  {
    $this->assertEquals($arr,
        array($dateTime->getYear(), $dateTime->getMonth(), $dateTime->getDay(), $dateTime->getHour(), $dateTime->getMinute(), $dateTime->getSecond()));
  }
}
?>