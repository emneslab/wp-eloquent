<?php

namespace Emneslab\ORM\WP;


use Emneslab\ORM\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'ID';
    protected $timestamp = false;

    public function meta()
    {
        return $this->hasMany('Emneslab\ORM\WP\UserMeta', 'user_id');
    }
}