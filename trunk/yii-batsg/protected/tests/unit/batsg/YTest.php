<?php
class YTest extends CTestCase
{
  private $message1 = 'message_1_same';
  private $message2 = 'message_2';
  private $message3 = 'message_3_with_param';
  
  public function testT()
  {
    foreach (array('en', 'ja') as $lang) {
      Yii::app()->language = $lang;
      // Sure that it is translated.
      $translatedMessage = Y::t($this->message1);
      $this->assertEquals('Message 1', $translatedMessage);
      // No param.
      $message = $this->message2;
      $translatedMessage = Y::t($message);
      $this->assertEquals(Yii::t(Y::$i18nDefaultCategory, $message),
          $translatedMessage);
      // Param
      $message = $this->message3;
      $paramName = 'param';
      $paramValue = 'abcdefg123';
      $translatedMessage = Y::t($message, array($paramName => $paramValue));
      $this->assertEquals(Yii::t(Y::$i18nDefaultCategory, $message, array($paramName => $paramValue)),
          $translatedMessage);
    }
  }
  
  public function testEt()
  {
    foreach (array('en', 'ja') as $lang) {
      Yii::app()->language = $lang;
      // Sure that it is translated.
      // Get output of Y::et
      ob_start();
      Y::et($this->message1);
      $translatedMessage = ob_get_contents();
      ob_end_clean();
      // Compare
      $this->assertEquals('Message 1', $translatedMessage);
      
      // No param.
      $message = $this->message2;
      // Get output of Y::et
      ob_start();
      Y::et($message);
      $translatedMessage = ob_get_contents();
      ob_end_clean();
      // Compare
      $this->assertEquals(Yii::t(Y::$i18nDefaultCategory, $message),
          $translatedMessage);

      // Param
      $message = $this->message3;
      $paramName = 'param';
      $paramValue = 'abcdefg123';
      // Get output of Y::et
      ob_start();
      Y::et($message, array($paramName => $paramValue));
      $translatedMessage = ob_get_contents();
      ob_end_clean();
      // Compare
      $this->assertEquals(Yii::t(Y::$i18nDefaultCategory, $message, array($paramName => $paramValue)),
          $translatedMessage);
    }
  }
  
  public function testSetFlashError()
  {
    $message = 'error 123';
    Y::setFlashError($message);
    $flashes = Yii::app()->user->getFlashes();
    $this->assertEquals($message, $flashes['error']);
  }
  
  public function testSetFlashErrorT()
  {
    $message = $this->message2;
    Y::setFlashErrorT($message);
    $flashes = Yii::app()->user->getFlashes();
    $this->assertEquals(Y::t($message), $flashes['error']);
  }
  
  public function testSetFlashNotice()
  {
    $message = 'notice 123';
    Y::setFlashNotice($message);
    $flashes = Yii::app()->user->getFlashes();
    $this->assertEquals($message, $flashes['notice']);
  }
  
  public function testSetFlashNoticeT()
  {
    $message = $this->message2;
    Y::setFlashNoticeT($message);
    $flashes = Yii::app()->user->getFlashes();
    $this->assertEquals(Y::t($message), $flashes['notice']);
  }
  
  public function testSetFlashSuccess()
  {
    $message = 'success 123';
    Y::setFlashSuccess($message);
    $flashes = Yii::app()->user->getFlashes();
    $this->assertEquals($message, $flashes['success']);
  }
  
  public function testSetFlashSuccessT()
  {
    $message = $this->message2;
    Y::setFlashSuccessT($message);
    $flashes = Yii::app()->user->getFlashes();
    $this->assertEquals(Y::t($message), $flashes['success']);
  }
  
  public function testParams()
  {
    $this->assertEquals(Yii::app()->params['paramLevel1']['paramLevel2_1'], Y::params('paramLevel1', 'paramLevel2_1'));
  }
  
  public function testTranslateArrayValue()
  {
    Yii::app()->language = 'ja';
    $arr = array(
        'm1' => $this->message1,
        'm2' => $this->message2,
        'm3' => $this->message3,
    );
    $translatedValue = Y::translateArrayValue($arr);
    $this->assertEquals(count($arr), count($translatedValue));
    foreach ($arr as $key => $value) {
      $this->assertEquals(Y::t($value), $translatedValue[$key]);
    }
  }
  
  public function testSetLanguage()
  {
    $lang = 'non_exist';
    Y::setLanguage($lang);
    $this->assertEquals($lang, Yii::app()->language);
    // TODO: Test another situation.
  }
}
?>