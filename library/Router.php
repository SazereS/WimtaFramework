<?php

class Library_Router{

    private $_routes = array(
        '{controller}/{id; type: integer}' => array(
            'controller' => '{controller}s',
            'action' => 'view',
            'id' => '{id}'
            ),
        '{controller}/{id; type: integer}/{action}' => array(
            'controller' => '{controller}s',
            'action' => '{action}',
            'id' => '{id}'
            ),
        '{controller}/{action}/*' => array(
            'controller' => '{controller}',
            'action' => '{action}'
            ),
        '{controller}/{action}' => array(
            'controller' => '{controller}',
            'action' => '{action}'
            ),
        '{controller}' => array(
            'controller' => '{controller}',
            'action' => 'index'
            ),
        '' => array(
            'controller' => 'index',
            'action' => 'index'
            ),
    );
    private $_request;

    public function __construct(Library_Request $request){
        $this->_request = $request;
    }

    public function findRoute(){
        if($this->_request->url === ''){
            $this->_request->params = $this->_routes[''];
            return $this;
        }
        $exploded_url = explode('/', $this->_request->url);
        $url_length = count($exploded_url);
        foreach($this->_routes as $route_string => $result){
            $route = explode('/', trim($route_string, '/'));
            $route_length = count($route);
            if(
                    (
                            (end($route) != '*')
                            AND ($route_length == $url_length)
                            )
                    OR (
                            (end($route) == '*')
                            AND ($route_length <= $url_length + 1)
                            )
                    ){
                $flag = true;
                foreach($route as $key => $lonely_param){ // Forever alone :'(
                    if($lonely_param[0] == '{' AND $lonely_param[strlen($lonely_param) - 1] == '}'){
                        $lonely_param = explode(';', trim($lonely_param, '{};'));
                        $first = true;
                        $anchors = array();
                        foreach($lonely_param as $p){
                            if($first){
                                $anchors['{' . $p . '}'] = $exploded_url[$key];
                                $first = false;
                                continue;
                            }
                            $p = explode(':', $p);
                            $k = trim($p[0]);
                            $v = trim($p[1]);
                            switch($k){
                                case 'type':
                                    switch($v){
                                        case 'int':
                                        case 'integer':
                                        case 'number':
                                            if(strval(intval($exploded_url[$key])) !== $exploded_url[$key]){
                                                $flag = false;
                                                break;
                                            }
                                        break;
                                    }
                                break;
                                case 'min-length':
                                    if((int) $v > strlen($exploded_url[$key])){
                                        $flag = false;
                                        break;
                                    }
                                break;
                                case 'max-length':
                                    if((int) $v < strlen($exploded_url[$key])){
                                        $flag = false;
                                        break;
                                    }
                                break;
                                case 'regex':
                                    # NEED TO ADD
                                break;
                            }
                        }
                    } elseif($lonely_param == '*'){
                        if($exploded_url[$key] != NULL){
                            $is_key = true;
                            $params = array();
                            for($i = $key; $i < $url_length; $i++){
                                if($is_key){
                                    $params[$exploded_url[$i]] = false;
                                } else {
                                    $params[$exploded_url[$i - 1]] = $exploded_url[$i];
                                }
                                $is_key = ! $is_key;
                            }
                            $this->_request->params = array_merge($params, $this->_request->params);
                        }
                    } else {
                        if($exploded_url[$key] != $lonely_param){
                            $flag = false;
                        }
                    }
                    if(!$flag){
                        break;
                    }
                }
                if($flag){
                    $route_params = $this->_routes[$route_string];
                    foreach($route_params as $k => $v){
                        $route_params[$k] = strtr($v, $anchors);
                    }
                    $this->_request->params = array_merge($this->_request->params, $route_params);
                    return $this;
                } else {
                    //break;
                }
            }
        }
        return $this;
    }

}