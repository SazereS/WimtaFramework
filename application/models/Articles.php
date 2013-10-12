<?php

namespace Application\Models;

class Articles extends \Library\Db\Table
{

    public function __construct()
    {
        $this->_table = 'articles';
        $this->_has_many = array(
            'comments' => array(
                'public_key' => 'article_id',
                'as'         => 'comments'
            )
        );
    }

}
