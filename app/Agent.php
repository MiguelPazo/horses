<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function animals()
    {
        return $this->belongsToMany('Horses\Animal', 'animal_agent');
    }

    public function scopeIdsIn($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeNames($query, $names, $or = false)
    {
        if ($or) {
            return $query->orWhere('names', $names);
        } else {
            return $query->where('names', $names);
        }

    }

    public function scopeLastnames($query, $lastnames, $or = false)
    {
        if ($or) {
            return $query->orWhere('lastnames', $lastnames);
        } else {
            return $query->where('lastnames', $lastnames);
        }
    }

}
