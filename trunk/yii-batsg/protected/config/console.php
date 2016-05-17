<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.

// Get config from main.php
$mainConfig = require(dirname(__FILE__) . '/main.php');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',


  // preloading 'log' component
  'preload'=>array('log'),

  // autoloading model and component classes
  'import' => $mainConfig['import'],

  'commandMap' => array(
    'i18n' => array(
      'class' => 'ext.batsg.commands.I18nCommand',
    ),
  ),

  // application components
  'components'=>array(
    'db' => $mainConfig['components']['db'],
    'log'=>array(
      'class'=>'CLogRouter',
      'routes'=>array(
        array(
          'class'=>'CFileLogRoute',
          'levels'=>'error, warning, info',
        ),
      ),
    ),
  ),
);