<?php

namespace Application\Controllers;
use \Application\Models;

class ArticlesController extends \Library\Controller{

    public function init(){
    }

    public function indexAction(){
        $articles = new Articles();
        $this->view->articles = $articles->fetchAll();
    }

    public function newAction(){
        $this->view->post = array();
        if($this->isPost()){
            $post = $this->getPost();
            if($post['title'] AND $post['text']){
                $articles = new Articles();
                $row = $articles->newRow();
                $row->text  = $post['text'];
                $row->title = $post['title'];
                $row->save();
                if($row->id){
                    $this->redirect('article/' . $row->id);
                }
            }
            $this->view->post = $post;
        }
    }

    public function viewAction(){
        $articles = new \Application\Models\Articles();
        if($id = $this->getParam('id')){
            if($articles->find($id)){
                $this->view->article = $articles->getCurrent();
            } else {
                die('Error 404');
            }
        }
    }

    public function editAction(){
        // Put your code here
    }

    public function deleteAction(){
        // Put your code here
    }

}