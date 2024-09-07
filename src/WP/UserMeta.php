<?php

namespace Emneslab\ORM\WP;


use Emneslab\ORM\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $primaryKey = 'umeta_id';

    public $timestamps    = false;

    public function getTable()
    {
        return $this->getConnection()->getTablePrefix() . 'usermeta';
    }
}