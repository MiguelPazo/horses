<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function agents()
    {
        return $this->belongsToMany('Horses\Agent', 'animal_agent')->withPivot('type');
    }

    public function catalogs()
    {
        return $this->hasMany('Horses\Catalog');
    }

    public function scopeIdsIn($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeName($query, $name, $or = false)
    {
        if ($or) {
            return $query->orWhere('name', $name);
        } else {
            return $query->where('name', $name);
        }
    }

    public function scopeGender($query, $gender, $or = false)
    {
        if ($or) {
            return $query->orWhere('gender', $gender);
        } else {
            return $query->where('gender', $gender);
        }

    }

    public function scopeCode($query, $code, $or = false)
    {
        if ($or) {
            return $query->orWhere('code', $code);
        } else {
            return $query->where('code', $code);
        }
    }
}
