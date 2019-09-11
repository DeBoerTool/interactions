<?php

namespace Dbt\Interactions\Tests;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title'];
    protected $table = 'posts';
    public $timestamps = false;
}
