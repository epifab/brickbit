<?php
return array(
  // Database connection
  'dbUser' => 'root',
  'dbPass' => '',
  'dbHost' => 'localhost',
  'dbName' => 'cider',
  'dbType' => 'mysql',
  
  // Language
  'defaultLang' => 'it',
  'languages' => array(
    'en' => 'http://brickbit.local/en/',
    'it' => 'http://brickbit.local/it/'
  ),

  // Theme
  'theme' => 'brickbit2',
  'themes' => array('brickbit2'),
  
  // Base directory
  'baseDir' => '/',
  'virtualDir' => 'it/',
    
  'siteTitle' => 'BrickBit',
  'siteSubtitle' => 'OO MVC Framework',
  
  'defaultPateTitle' => 'BrickBit',
    
  'coreCache' => false,
  'debug' => false,
    
  'cacheDir' => 'appdata/brickbit.local_en/cache/',
  'filesDir' => 'appdata/brickbit.local_en/files/',
);