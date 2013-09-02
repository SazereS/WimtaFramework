<?php

// Const init
define('PUBLIC_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('APPLICATION_PATH', PUBLIC_PATH . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
define('LIBRARY_PATH', PUBLIC_PATH . '..' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);

require_once(LIBRARY_PATH . 'Application.php');

$application = new Library_Application();
$application->setConfig('default')->run();
# echo $application->getElapsedTime();