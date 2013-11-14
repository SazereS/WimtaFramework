<?php

namespace Application\Models;

class Comments extends \Library\Db\Table
{

    public function __construct()
    {
        $this->_table = 'comments';
        $this->_belongs_to = array(
            'articles' => array(
                'public_key' => 'article_id',
                'as'         => 'article'
            )
        );
    }

}
