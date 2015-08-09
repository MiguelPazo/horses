<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeStatusDif($query, $status)
    {
        return $query->where('status', '<>', $status);
    }

    public function setDateBeginAttribute($value)
    {
        $dateBegin = date('Y-m-d', strtotime($value));

        $this->attributes['date_begin'] = $dateBegin;
    }

    public function setDateEndAttribute($value)
    {
        $dateBegin = date('Y-m-d', strtotime($value));

        $this->attributes['date_end'] = $dateBegin;
    }

    public function getDateBeginAttribute()
    {
        $dateBegin = date('d-m-Y', strtotime($this->attributes['date_begin']));

        return $dateBegin;
    }

    public function getDateEndAttribute()
    {
        $dateBegin = date('d-m-Y', strtotime($this->attributes['date_end']));

        return $dateBegin;
    }

}
