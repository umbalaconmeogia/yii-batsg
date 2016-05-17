<?php
class HJapaneseTest extends CTestCase
{
  public function testReplaceFullWidthDigits()
  {
    $fullWidthStr = 'a１b２３４５６７８９０あ1い2う３え4お5';
    $halfWidthStr = 'a1b234567890あ1い2う3え4お5';
    $this->assertEquals($halfWidthStr, HJapanese::replaceFullWidthDigits($fullWidthStr));
  }

  public function testReplaceHalfWidthDigits()
  {
    $halfWidthStr = 'a1b234567890あ１い２う3え４お５';
    $fullWidthStr = 'a１b２３４５６７８９０あ１い２う３え４お５';
    $this->assertEquals($fullWidthStr, HJapanese::replaceHalfWidthDigits($halfWidthStr));
  }
  
  public function testParseDateTime()
  {
    $dateTime = '2011年';
    $this->assertEquals(HDateTime::now()->toString('2011-m-d 00:00:00'),
        HJapanese::parseDateTime($dateTime)->toString('Y-m-d H:i:s'));
    $dateTime = '2011年5月';
    $this->assertEquals(HDateTime::now()->toString('2011-05-d 00:00:00'),
        HJapanese::parseDateTime($dateTime)->toString('Y-m-d H:i:s'));
    $dateTime = '2011年5月6日';
    $this->assertEquals(HDateTime::now()->toString('2011-05-06 00:00:00'),
        HJapanese::parseDateTime($dateTime)->toString('Y-m-d H:i:s'));
    $dateTime = '2011年5月6日 23時';
    $this->assertEquals(HDateTime::now()->toString('2011-05-06 23:00:00'),
        HJapanese::parseDateTime($dateTime)->toString('Y-m-d H:i:s'));
    $dateTime = '2011年5月6日 23時9分';
    $this->assertEquals(HDateTime::now()->toString('2011-05-06 23:09:00'),
        HJapanese::parseDateTime($dateTime)->toString('Y-m-d H:i:s'));
    $dateTime = '2011年5月6日 23時9分1秒';
    $this->assertEquals(HDateTime::now()->toString('2011-05-06 23:09:01'),
        HJapanese::parseDateTime($dateTime)->toString('Y-m-d H:i:s'));
  }
  
  public function testSjisToUtf8()
  {
    $utf8 = '今日の天気がよいですね。';
    $sjis = mb_convert_encoding($utf8, 'SJIS', 'UTF-8');
    $this->assertEquals($utf8, HJapanese::sjisToUtf8($sjis));
  }
  
  public function testUtf8ToSjis()
  {
    $utf8 = '今日の天気がよいですね。';
    $sjis = mb_convert_encoding($utf8, 'SJIS', 'UTF-8');
    $this->assertEquals($sjis, HJapanese::utf8ToSjis($utf8));
  }
  
  public function testGetJapaneseYear()
  {
    // TODO: test with the year before 1970
    $testData = array(
//      array('1868/9/8', '明治', 1),
//      array('1912/7/29', '明治', 45), // Last Meiji
      array('1912/7/30', '大正', 1), // First Taisho
      array('1926/12/24', '大正', 15), // Last Taisho
      array('1926/12/25', '昭和', 1), // First Showa
      array('1989/1/7', '昭和', 64), // Last Showa
      array('1989/1/8', '平成', 1), // First Heisei
      array('2011/5/6', '平成', 23),
    );
    foreach ($testData as $data) {
      $dateTime = HDateTime::createFromString($data[0]);
      $jpYear = HJapanese::getJapaneseYear($dateTime, $eraName, $yearNumber);
      $this->assertEquals("{$data[1]}{$data[2]}年", $jpYear, "Wrong converting {$data[0]}");
      $this->assertEquals($data[1], $eraName);
      $this->assertEquals($data[2], $yearNumber);
    }
  }
  
  public function testToJapaneseCalendar()
  {
    // Month and day with leading zeros.
    $jpDate = HJapanese::toJapaneseCalendar(HDateTime::createFromString('2011/05/07'));
    $this->assertEquals('平成23年05月07日', $jpDate);
    // Month and day without leading zeros.
    $jpDate = HJapanese::toJapaneseCalendar(HDateTime::createFromString('2011/05/07'), 'n月j日');
    $this->assertEquals('平成23年5月7日', $jpDate);
    // TODO: test with the year before 1970
    $testData = array(
//      array('1868/9/8', '明治1年9月8日'),
//      array('1912/7/29', '明治45年7月29日'), // Last Meiji
      array('1912/7/30', '大正1年7月30日'), // First Taisho
      array('1926/12/24', '大正15年12月24日'), // Last Taisho
      array('1926/12/25', '昭和1年12月25日'), // First Showa
      array('1989/1/7', '昭和64年1月7日'), // Last Showa
      array('1989/1/8', '平成1年1月8日'), // First Heisei
      array('2011/5/6', '平成23年5月6日'),
    );
    foreach ($testData as $data) {
      $jpDate = HJapanese::toJapaneseCalendar(HDateTime::createFromString($data[0]), 'n月j日');
      $this->assertEquals($data[1], $jpDate, "Wrong converting {$data[0]}");
    }
  }
  
  public function testMb_str_split()
  {
    $arr = array('あ', 'a', 'い', 'i', 'う', 'u');
    $str = join('', $arr);
    $this->assertEquals($arr, HJapanese::mb_str_split($str));
    $arr = array('あa', 'iい', 'う');
    $str = join('', $arr);
    $this->assertEquals($arr, HJapanese::mb_str_split($str, 2));
  }
  
  public function testContainHiragana()
  {
    $this->assertTrue(1 == HJapanese::containHiragana('ab漢字ひらがなc'));
    $this->assertFalse(1 == HJapanese::containHiragana('ab漢字カタカナc'));
  }
  
  public function testContainKatakana()
  {
    $this->assertTrue(1 == HJapanese::containKatakana('ab漢字カタカナc'));
    $this->assertFalse(1 == HJapanese::containKatakana('ab漢字ひらがなc'));
  }
  
  public function testHiraganaToKatakana()
  {
    $this->assertEquals('ab漢字ヒラガナcカタカナ', HJapanese::hiraganaToKatakana('ab漢字ひらがなcカタカナ'));
  }

  public function testKatakanaToHiragana()
  {
    $this->assertEquals('ab漢字かたかなcひらがな', HJapanese::katakanaToHiragana('ab漢字カタカナcひらがな'));
  }
}
?>