<?php

namespace Dbt\Interactions\Tests\Common\Fixtures;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Employee extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable, Authenticatable;

    /** @var array */
    protected $fillable = ['email'];

    /** @var bool */
    public $timestamps = false;

    /** @var string */
    protected $table = 'users';
}
