<?php

namespace Emneslab\ORM\WP;


use Emneslab\ORM\Database\Eloquent\Model;

class PostMeta extends Model
{
    protected $primaryKey = 'meta_id';

    public $timestamps    = false;

    public function getTable()
    {
        return $this->getConnection()->getTablePrefix() . 'postmeta';
    }
}