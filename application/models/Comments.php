<?php

namespace Application\Models;

class Comments extends \Library\Db\Table
{

    public function __construct()
    {
        $this->_table = 'comments';
        $this->belongsTo('articles', 'article_id', 'article');
    }

}
