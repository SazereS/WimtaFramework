<?php

namespace Application\Models;

class Articles extends \Library\Db\Table
{

    public function __construct()
    {
        $this->_table = 'articles';
        $this->hasMany('comments', 'article_id');
    }

}
