<?php

namespace Dbt\Interactions\Tests\Support\Fixtures;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class Employee extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;

    /** @var array */
    protected $fillable = ['email'];

    /** @var bool */
    public $timestamps = false;

    /** @var string */
    protected $table = 'users';
}
