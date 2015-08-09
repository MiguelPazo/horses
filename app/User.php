<?php namespace Horses;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    public $timestamps = false;
    protected $guarded = ['id'];


    public function scopeUser($query, $user)
    {
        return $query->where('user', $user);
    }
}
