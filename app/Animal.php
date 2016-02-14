<?php namespace Horses;

use Horses\Constants\ConstDb;
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

    public function tournaments()
    {
        return $this->belongsToMany('Horses\Tournament', 'catalogs');
    }

    public function breeder()
    {
        return $this->agents()->wherePivot('type', ConstDb::AGENT_BREEDER);
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

    public function setBirthdateAttribute($value)
    {
        $birthDate = ($value != null && $value != '') ? date('Y-m-d', strtotime($value)) : null;

        $this->attributes['birthdate'] = $birthDate;
    }

    public function getBirthdateAttribute()
    {
        $attr = $this->attributes['birthdate'];
        $birthDate = ($attr != '') ? date('d-m-Y', strtotime($this->attributes['birthdate'])) : null;

        return $birthDate;
    }
}
