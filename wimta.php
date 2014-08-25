<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();

define('PATH', $argv[1]);
define(
    'PUBLIC_PATH',
    PATH . '/public/'
);
define(
    'APPLICATION_PATH',
    PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
);
define(
    'LIBRARY_PATH',
    PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR
);


global $templates;
$templates = array(
    'controller'        => '<?php

namespace Application\\Controllers;

use \\Application\\Models;

class %sController extends \\Library\\Controller
{

    public function init()
    {
        // Initialization code here
    }

    public function indexAction()
    {
        // Default action code
    }

}
',
    'controller_action' => '

    public function %sAction()
    {
        // Put your code here
    }

',
    'action_view'       => '
<h3>%s#%s</h3>
<p>Place your code here</p>
',
    'migration'         => '<?php

namespace Application\\Migrations;

class Migration%s extends \\Library\\Db\\Migration
{

    public $version = \'%s\';

    public function apply()
    {

    }

    public function rollback()
    {

    }

}
',
    'model'             => '<?php

namespace Application\\Models;

class %s extends \\Library\\Db\\Table
{

    public function __construct()
    {
        $this->_table = \'%s\';
    }

}
',
    'layout' => '<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>%s</title>
    </head>
    <body>
        <?=$content ?>
    </body>
</html>'
);


function get_real_name($controller)
{
    $controller = explode('-', $controller);
    foreach ($controller as $k => $v) {
        $controller[$k][0] = strtoupper($controller[$k][0]);
    }
    return implode('', $controller);
}

function create_custom_view($view)
{
    $path = PATH . '/application/views/custom/' . $view . '.phtml';
    echo 'Creating views/custom/', $view, '.phtml file...' . PHP_EOL;
    if(
        file_put_contents(
            $path,
            '<h4>' . $view . '.phtml</h4>'
        )
    ){
        echo 'Custom view "',
            $view,
            '" successfully created!',
            PHP_EOL;
    }
}

function create_layout($layout)
{
    global $templates;
    $path = PATH . '/application/views/layouts/' . $layout . '.phtml';
    echo 'Creating views/layouts/', $layout, '.phtml file...' . PHP_EOL;
    if(
        file_put_contents(
            $path,
            sprintf($templates['layout'], $layout)
        )
    ){
        echo 'Layout "',
            $layout,
            '" successfully created!',
            PHP_EOL;
    }
}

function create_action_view($controller, $action)
{
    global $templates;
    $path = PATH . '/application/views/scripts/' . $controller;
    if(!file_exists($path)){
        mkdir($path);
    }
    $path .= '/' . $action . '.phtml';
    echo 'Creating ', $controller, '/', $action, '.phtml file...', PHP_EOL;
    if(
        file_put_contents(
            $path,
            sprintf($templates['action_view'], get_real_name($controller), $action)
        )
    ){
        echo 'View for ',
            get_real_name($controller),
            '#',
            $action,
            ' successfully created!',
            PHP_EOL;
    }
}

function create_controller($controller_l)
{
    global $templates;
    $controller = get_real_name($controller_l);
    echo 'Creating ', $controller, 'Controller.php file...', PHP_EOL;
    if(
        file_put_contents(
            PATH . '/application/controllers/' . $controller . 'Controller.php', 
            sprintf($templates['controller'], $controller)
        )
    ){
        echo 'Controller ', $controller, ' successfully created!', PHP_EOL;
        create_action_view($controller_l, 'index');
    }
}

function create_action($controller_l, $action_l)
{
    global $templates;
    $controller      = get_real_name($controller_l);
    $action          = get_real_name($action_l);
    $action[0]       = strtolower($action[0]);
    echo 'Creating ', $controller, '#', $action_l, ' action...', PHP_EOL;
    $controller_code = trim(
        file_get_contents(PATH . '/application/controllers/' . $controller . 'Controller.php'),
        ' ' . PHP_EOL
    );
    $controller_code = substr($controller_code, 0, strlen($controller_code) - 1);
    $controller_code = trim($controller_code, ' ' . PHP_EOL);
    $controller_code .= sprintf($templates['controller_action'], $action) . '}';
    if (
        file_put_contents(
            PATH . '/application/controllers/' . $controller . 'Controller.php',
            $controller_code
        )
    ) {
        echo 'Action ', $controller, '#', $action_l, ' successfully created!', PHP_EOL;
        create_action_view($controller_l, $action_l);
    }
}


function create_migration()
{
    global $templates;
    $time = time();
    echo 'Creating new migration file...', PHP_EOL;
    if (
        file_put_contents(
            PATH . '/application/migrations/Migration' . $time . '.php',
            sprintf($templates['migration'], $time, $time)
        )
    ) {
        echo 'Migration for ', $time, ' successfully created!', PHP_EOL;
    }
}

function create_model($table_name)
{
    global $templates;
    $class_name = explode('_', strtolower($table_name));
    foreach ($class_name as $key => $val) {
        $class_name[$key][0] = strtoupper($val[0]);
    }
    $class_name = implode('', $class_name);
    echo 'Creating model for `' . $table_name . '` table...', PHP_EOL;
    if (
        file_put_contents(
            PATH . '/application/models/' . $class_name . '.php',
            sprintf($templates['model'], $class_name, strtolower($table_name))
        )
    ) {
        echo 'Model for `' . $table_name . '` table successfully created!', PHP_EOL;
    }
}

function migrate()
{
    echo 'Migration process started!', PHP_EOL;
    require_once(PATH . '/library/Application.php');
    $application = new \Library\Application();
    $application->setConfig('default', 'development');
    if (file_exists(PATH . '/application/migrations/version')) {
        $version = file_get_contents(PATH . '/application/migrations/version');
        unlink(PATH . '/application/migrations/version');
    } else {
        $version = 0;
    }
    echo 'Current schema version is ', $version, PHP_EOL;
    $files = scandir(PATH . '/application/migrations/');
    foreach ($files as $migration) {
        if (in_array($migration, array('.', '..'))) {
            continue;
        }
        $temp         = substr($migration, 9);
        $migrations[] = substr($temp, 0, 10);
    }
    sort($migrations);
    foreach ($migrations as $migration) {
        if ($version < $migration) {
            echo 'Migrating to version ', $migration, '...', PHP_EOL;
            $class_name = '\\Application\\Migrations\\Migration' . $migration;
            $class      = new $class_name();
            try {
                $class->apply();
            } catch (\Exception $e) {
                throw new \Library\Db\Exception($e->getMessage());
            }
            $version = $class->version;
            echo 'Complete!', PHP_EOL;
        }
    }
    file_put_contents(
        PATH . '/application/migrations/version', $version
    );
    echo 'Successfully migrated to version ', $version, '!', PHP_EOL;
}

function rollback($target = NULL)
{
    echo 'Migration rollback started!', PHP_EOL;
    require_once(PATH . '/library/Application.php');
    $application = new \Library\Application();
    $application->setConfig('default', 'development');
    if (file_exists(PATH . '/application/migrations/version')) {
        $version = file_get_contents(PATH . '/application/migrations/version');
        unlink(PATH . '/application/migrations/version');
    } else {
        $version = 0;
    }
    echo 'Current schema version is ', $version, PHP_EOL;
    $files = scandir(PATH . '/application/migrations/');
    foreach ($files as $migration) {
        if (in_array($migration, array('.', '..'))) {
            continue;
        }
        $temp         = substr($migration, 9);
        $migrations[] = substr($temp, 0, 10);
    }
    rsort($migrations);
    foreach ($migrations as $key => $migration) {
        $class_name = '\\Application\\Migrations\\Migration' . $migration;
        $class      = new $class_name();
        if (isset($migrations[$key + 1])) {
            $prev_class_name = '\\Application\\Migrations\\Migration' . $migrations[$key + 1];
            $prev_class      = new $prev_class_name();
            $prev_version    = $prev_class->version;
        } else {
            $prev_version = 0;
        }
        echo 'Returning to version ', $prev_version, '...', PHP_EOL;
        $class->rollback();
        echo 'Complete!', PHP_EOL;
        if (is_null($target)) {
            break;
        } elseif ($target > $migration) {
            break;
        }
    }
    file_put_contents(
        PATH . '/application/migrations/version', $prev_version
    );
    echo 'Successfully returned to version ', $prev_version, '!', PHP_EOL;
}


function full_copy($source, $target) {
  if (is_dir($source))  {
    @mkdir($target);
    $d = dir($source);
    while (FALSE !== ($entry = $d->read())) {
      if ($entry == '.' || $entry == '..') continue;
      full_copy("$source/$entry", "$target/$entry");
    }
    $d->close();
  }
  else copy($source, $target);
}


if(
    !(@$argv[2] == 'create' AND (@$argv[3] == 'project' OR @$argv[3] == 'application'))
    AND (!file_exists(PATH . '/application') OR !file_exists(PATH . '/library'))
){
	if(isset($argv[2])) {
    	die('You must run this command from exists project directory!');
    }
}

switch (@$argv[2]){
    case 'create':

        switch (@$argv[3]){
            case 'project':
            case 'application':
                if(@$argv[4])
                    $project_path = PATH . DIRECTORY_SEPARATOR . $argv[4];
                    if((file_exists($project_path) AND is_dir($project_path)) OR @mkdir($project_path)){
                        $project_path .= DIRECTORY_SEPARATOR;
                        $sources_path  = dirname($argv[0]) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR; 
                        full_copy($sources_path, $project_path);
                        echo 'Success! Now type \'cd "' . $argv[4] . '"\' to open your new project\'s directoty' . PHP_EOL;
                    } else {
                        echo 'Oops! Something is wrong!';
                    }
            break;
            case 'controller':
                if(@$argv[4]){
                    create_controller($argv[4]);
                }
            break;
            case 'action':
                if(@$argv[4] AND @$argv[5]){
                    create_action($argv[4], $argv[5]);
                }
            break;
            case 'view':
                if(@$argv[4]){
                    create_custom_view($argv[4]);
                }
            break;
            case 'layout':
                if(@$argv[4]){
                    create_layout($argv[4]);
                }
            break;
            case 'model':
                if(@$argv[4]){
                    create_model($argv[4]);
                }
            break;
            case 'migration':
                create_migration();
            break;
        }

    break;

    case 'db':

    	switch(@$argv[3]){

    		case 'migrate':
    			migrate();
    		break;

    		case 'rollback':
    			rollback(@$argv[4]);
    		break;

    		case 'repair':
    			echo 'All migrations will be rollbacked then applied again in 10 seconds!' . PHP_EOL;
    			echo 'Press Ctrl+C to cancel!' . PHP_EOL;
    			echo '10...' . PHP_EOL;
    			sleep(5);
    			echo '5...' . PHP_EOL;
    			sleep(1);
    			echo '4...' . PHP_EOL;
    			sleep(1);
    			echo '3...' . PHP_EOL;
    			sleep(1);
    			echo '2...' . PHP_EOL;
    			sleep(1);
    			echo '1...' . PHP_EOL;
    			sleep(1);
    			rollback(0);
    			migrate();
    			echo 'Database repair complete!' . PHP_EOL;
    		break;

    	}

    break;

    default:
        echo '
Wimta Framework Manager

create
    project <path>
    controller <controller-name>
    action <controller-name> <action-name>
    view <custom-view-name>
    layout <layout-name>
    model <table-name>
    migration

db
    migrate
    rollback [version]
    repair (ALL DATA WILL BE LOST!)
        ';
    break;
}


/*

create
    project <path>
    controller <controller-name>
    action <controller-name> <action-name>
    view <custom-view-name>
    layout <layout-name>
    model <table-name>
    migration

db
    migrate
    rollback [version]
    repair (ALL DATA WILL BE LOST!)

*/