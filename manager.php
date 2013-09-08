<?php
define('APPLICATION_PATH', './application/');

global $templates;
$templates= array(
    'controller' => '<?php

class Application_Controllers_%sController extends Library_Controller{

    public function init(){

    }

    public function indexAction(){

    }

}
',
    'action_view' => '
<h3>%s#%s</h3>
<p>Place your code here</p>
',
    'migration' => '<?php

class Application_Migrations_Migration%s extends Library_Db_Migration{

    public $version = \'%s\';

    public function apply() {

    }

    public function rollback() {

    }

}
',
    'model' => '<?php

class Application_Models_%s extends Library_Db_Table{

    public function __construct() {
        $this->_table = \'%s\';
    }

}
'
);

function get_real_name($controller){
    $controller = explode('-', $controller);
    foreach ($controller as $k => $v) {
        $controller[$k][0] = strtoupper($controller[$k][0]);
    }
    return implode('', $controller);
}

function create_action_view($controller, $action){
    global $templates;
    $path = './application/views/scripts/' . $controller;
    if(!file_exists($path)){
        mkdir($path);
    }
    $path .= '/' . $action . '.phtml';
    echo 'Creating ', $controller, '/', $action, '.phtml file...', PHP_EOL;
    if(file_put_contents($path, sprintf($templates['action_view'], $controller, $action))){
        echo 'View for ', $controller, '#', $action, ' successfulle created!', PHP_EOL;
    }
}

function create_controller($controller_b){
    global $templates;
    $controller = get_real_name($controller_b);
    echo 'Creating ', $controller, 'Controller.php file...', PHP_EOL;
    if(
            file_put_contents(
                    './application/controllers/' . $controller . 'Controller.php',
                    sprintf($templates['controller'], $controller)
                    )
            ){
        echo 'Controller ', $controller, ' successfully created!', PHP_EOL;
        create_action_view($controller_b, 'index');
    }
}

function create_migration(){
    global $templates;
    $time = time();
    echo 'Creating new migration file...', PHP_EOL;
    if(
            file_put_contents(
                    './application/migrations/Migration' . $time . '.php',
                    sprintf($templates['migration'], $time, $time)
                    )
            ){
        echo 'Migration for ', $time, ' successfully created!', PHP_EOL;
    }
}

function create_model($table_name){
    global $templates;
    $class_name = explode('_', strtolower($table_name));
    foreach ($class_name as $key => $val){
        $class_name[$key][0] = strtoupper($val[0]);
    }
    echo 'Creating model for `' . $table_name . '` table...', PHP_EOL;
    if(
            file_put_contents(
                    './application/models/' . $class_name . '.php',
                    sprintf($templates['model'], $class_name, strtolower($table_name))
                    )
            ){
        echo 'Model for `' . $table_name . '` table successfully created!', PHP_EOL;
    }
}

function migrate(){
    echo 'Migration process started!', PHP_EOL;
    require_once('library/Application.php');
    $application = new Library_Application();
    $application->initDbAdapter();
    if(file_exists('./application/migrations/version')){
        $version = file_get_contents('./application/migrations/version');
        unlink('./application/migrations/version');
    } else {
        $version = 0;
    }
    echo 'Current schema version is ', $version, PHP_EOL;
    $files = scandir('./application/migrations/');
    foreach($files as $migration){
        if(in_array($migration, array('.', '..'))){
            continue;
        }
        $temp = substr($migration, 9);
        $migrations[] = substr($temp, 0, 10);
    }
    sort($migrations);
    foreach($migrations as $migration){
        if($version < $migration){
            echo 'Migrating to version ', $migration, '...', PHP_EOL;
            $class_name = 'Application_Migrations_Migration' . $migration;
            $class = new $class_name();
            $class->apply();
            $version = $class->version;
            echo 'Complete!', PHP_EOL;
        }
    }
    file_put_contents(
            './application/migrations/version',
            $version
            );
    echo 'Successfully migrated to version ', $version, '!', PHP_EOL;
}

function rollback($target = NULL){
    echo 'Migration rollback started!', PHP_EOL;
    require_once('library/Application.php');
    $application = new Library_Application();
    $application->initDbAdapter();
    if(file_exists('./application/migrations/version')){
        $version = file_get_contents('./application/migrations/version');
        unlink('./application/migrations/version');
    } else {
        $version = 0;
    }
    echo 'Current schema version is ', $version, PHP_EOL;
    $files = scandir('./application/migrations/');
    foreach($files as $migration){
        if(in_array($migration, array('.', '..'))){
            continue;
        }
        $temp = substr($migration, 9);
        $migrations[] = substr($temp, 0, 10);
    }
    rsort($migrations);
    foreach($migrations as $key => $migration){
        $class_name = 'Application_Migrations_Migration' . $migration;
        $class = new $class_name();
        if(isset($migrations[$key + 1])){
            $prev_class_name = 'Application_Migrations_Migration' . $migrations[$key + 1];
            $prev_class = new $prev_class_name();
            $prev_version = $prev_class->version;
        } else {
            $prev_version = 0;
        }
        echo 'Returning to version ', $prev_version, '...', PHP_EOL;
        $class->rollback();
        echo 'Complete!', PHP_EOL;
        if(is_null($target)){
            break;
        } elseif($target > $migration){
            break;
        }
    }
    file_put_contents(
            './application/migrations/version',
            $prev_version
            );
    echo 'Successfully returned to version ', $prev_version, '!', PHP_EOL;
}

########################
# LET THE MAGIC BEGINS #
########################

echo PHP_EOL;

switch (strtolower($argv[1])){
    case 'create':

        switch($argv[2]){
            case 'controller':
                if($argv[3]){
                    create_controller($argv[3]);
                }
                break;
            default:
                echo 'WIMTA - create' . PHP_EOL;
                break;
        }

        break; # create end
    case 'db':

        switch($argv[2]){
            case 'migrate':
                migrate();
                break;
            case 'rollback':
                if(isset($argv[3])){
                    rollback($argv[3]);
                } else {
                    rollback();
                }
                break;
            case 'create':

                switch ($argv[3]) {
                    case 'model':
                        if ($argv[4]) {
                            create_model($argv[4]);
                        }
                        break;
                    case 'migration':
                        create_migration();
                        break;
                    default:
                        echo 'WIMTA - db - create' . PHP_EOL;
                        break;
                }

                break;
            default:
                echo 'WIMTA - db' . PHP_EOL;
                break;
        }

        break;
    default:
        echo 'WIMTA Generating tools' . PHP_EOL . PHP_EOL;
        echo '    create' . PHP_EOL;
        echo '        controller  <controller-name>' . PHP_EOL;
        echo '        *action     <controller-name> <action-name>' . PHP_EOL;
        echo '        *view       <custom-view-name>' . PHP_EOL;
        echo '        *layout     <layout-name>' . PHP_EOL;
        echo PHP_EOL;
        echo '    db' . PHP_EOL;
        echo '        migrate' . PHP_EOL;
        echo '        rollback [target-version]' . PHP_EOL;
        echo '        create' . PHP_EOL;
        echo '            migration' . PHP_EOL;
        echo '            model     <table-name>' . PHP_EOL;
        break;
}
