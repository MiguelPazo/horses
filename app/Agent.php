<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{

    protected $guarded = ['id'];
    public $timestamps = false;

    public function animals()
    {
        return $this->belongsToMany('Horses\Animal');
    }

}
