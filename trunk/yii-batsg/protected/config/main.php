<?php
mb_internal_encoding("UTF-8");
if (YII_DEBUG) {
  ini_set("error_reporting", E_ALL);
  ini_set("display_error", 'On');
}

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name'=>'My Web Application',
  'language' => 'en',
  'defaultController' => 'admin/company',

  // preloading 'log' component
  'preload' => array('log'),

  // autoloading model and component classes
  'import' => array(
    'application.models.*',
    'application.components.*',
    'ext.batsg.*',
    'ext.batsg.models.*',
    'ext.batsg.controllers.*',
  ),

  'modules'=>array(
    // uncomment the following to enable the Gii tool
    'gii'=>array(
      'class'=>'system.gii.GiiModule',
      'password'=>'password',
       // If removed, Gii defaults to localhost only. Edit carefully to taste.
      'ipFilters'=>array('127.0.0.1','::1'),
    ),
  ),

  // application components
  'components'=>array(
    'user'=>array(
      // enable cookie-based authentication
      'allowAutoLogin'=>true,
    ),
    // uncomment the following to enable URLs in path-format
    /*
    'urlManager'=>array(
      'urlFormat'=>'path',
      'rules'=>array(
        '<controller:\w+>/<id:\d+>'=>'<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
        '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
      ),
    ),
    */
    'db'=>array(
      'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/demo.sqlite',
      'enableParamLogging' => TRUE,
    ),
    // uncomment the following to use a MySQL database
    /*
    'db'=>array(
      'connectionString' => 'mysql:host=localhost;dbname=testdrive',
      'emulatePrepare' => true,
      'username' => 'root',
      'password' => '',
      'charset' => 'utf8',
    ),
    */
    'errorHandler'=>array(
      // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
    'log'=>array(
      'class'=>'CLogRouter',
      'routes'=>array(
        array(
          'class'=>'CFileLogRoute',
          'levels'=>'error, warning',
        ),
        array(
          'class'=>'CFileLogRoute',
          'levels'=>'trace',
          'categories'=>'system.db.*',
          'logFile'=>'sql.log',
        ),
        // uncomment the following to show log messages on web pages
        /*
        array(
          'class'=>'CWebLogRoute',
        ),
        */
      ),
    ),
  ),

  // application-level parameters that can be accessed
  // using Yii::app()->params['paramName']
  'params'=>array(
    // this is used in contact page
    'adminEmail'=>'webmaster@example.com',
    'dataList' => array(
      'pageSize' => 10,
    ),
    'language' => array(
      'en' => 'English',
      'ja' => '日本語',
      'vi' => 'Tiếng Việt',
    ),
    'paramLevel1' => array(
      'paramLevel2_1' => 'level 2_1',
      'paramLevel2_2' => 'level 2_2',
    ),
  ),
);