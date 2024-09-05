<?php

namespace Emneslab\ORM\WP;


use Emneslab\ORM\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'comment_ID';

    /**
     * Post relation for a comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function post()
    {
        return $this->hasOne('Emneslab\ORM\WP\Post');
    }
}