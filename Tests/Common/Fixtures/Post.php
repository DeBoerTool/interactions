<?php

namespace Dbt\Interactions\Tests\Common\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @var array */
    protected $fillable = ['title'];

    /** @var string */
    protected $table = 'posts';

    /** @var bool */
    public $timestamps = false;
}
