<?php

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
    case 'create2':

        break;
    default:
        echo 'WIMTA Generating tools' . PHP_EOL . PHP_EOL;
        echo '    create' . PHP_EOL;
        echo '        controller  <controller-name>' . PHP_EOL;
        echo '        *action     <controller-name> <action-name>' . PHP_EOL;
        echo '        *view       <custom-view-name>' . PHP_EOL;
        echo '        *layout     <layout-name>' . PHP_EOL;
        echo PHP_EOL;
        echo '    *db' . PHP_EOL;
        echo '        migrate' . PHP_EOL;
        echo '        create' . PHP_EOL;
        echo '            migration' . PHP_EOL;
        echo '            model     <table-name>' . PHP_EOL;
        break;
}
