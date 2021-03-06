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

    public function scopeProfile($query, $profile)
    {
        return $query->where('profile', $profile);
    }

    public function scopeUsers($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function categories()
    {
        return $this->belongsToMany('Horses\Category', 'category_users')->withPivot(['dirimente', 'actual_stage']);
    }

    public function stages()
    {
        return $this->hasMany('Horses\Stage');
    }

    public function checkRol($rol)
    {
        if (is_array($rol)) {
            if (in_array($this->profile, $rol)) {
                return true;
            }
        } else {
            if ($this->profile == $rol) {
                return true;
            }
        }

        return false;
    }
}
