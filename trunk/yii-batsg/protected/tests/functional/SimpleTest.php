<?php

class SimpleTest extends WebTestCase
{
	public function testIndex()
	{
		$this->open('');
		$this->assertTextPresent('Index');
	}
}
?>