<?php namespace Horses;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Jury extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'jurado';
    public $timestamps = false;

}
