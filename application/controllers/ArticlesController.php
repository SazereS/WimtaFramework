<?php

namespace Application\Controllers;

use \Application\Models\Articles;

class ArticlesController extends \Library\Controller
{

    public function init()
    {

    }

    public function indexAction()
    {
        $articles             = new Articles();
        $this->view->articles = $articles->fetchAll();
        if($format = $this->getParam('format')){
            foreach ($this->view->articles as $article){
                $content['articles'][] = $article->toArray();
            }
            $this->view->content = $content;
            if(strtolower($format) == 'xml'){
                $this->getResponse()->setFormat(\Library\Response::FORMAT_XML);
            } else {
                $this->getResponse()->setFormat(\Library\Response::FORMAT_JSON);
            }
        }
    }

    public function newAction()
    {
        $this->view->post = array();
        if ($this->isPost()) {
            $post = $this->getPost();
            if ($post['title'] AND $post['text']) {
                $articles   = new Articles();
                $row        = $articles->newRow();
                $row->text  = $post['text'];
                $row->title = $post['title'];
                $row->save();
                if ($row->id) {
                    $this->redirect('article/' . $row->id);
                }
            }
            $this->view->post = $post;
        }
    }

    public function viewAction()
    {
        $articles = new Articles();
        if ($id = $this->getParam('id')) {
            if ($articles->find($id)) {
                $this->view->article = $articles->getCurrent();
            } else {
                $this->page404();
            }
        } else {
            $this->redirect('articles');
        }
    }

    public function editAction()
    {
        $articles = new Articles();
        if ($id = $this->getParam('id')) {
            if ($articles->find($id)) {
                $article = $articles->getCurrent();

                $this->view->post = $article->toArray();
                if ($this->isPost()) {
                    $post = $this->getPost();
                    if ($post['title'] AND $post['text']) {
                        $article->text  = $post['text'];
                        $article->title = $post['title'];
                        $article->save();
                        if ($article->id) {
                            $this->redirect('article/' . $article->id);
                        }
                    }
                    $this->view->post = $post;
                }
                $this->view->render('new');
            } else {
                die('Error 404');
            }
        } else {
            $this->redirect('articles');
        }
    }

    public function deleteAction()
    {
        $articles = new Articles();
        if ($id = $this->getParam('id')) {
            if ($articles->find($id)) {
                $articles->getCurrent()->delete();
            }
        }
        $this->redirect('articles');
    }

}