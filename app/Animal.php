<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{

    protected $guarded = ['id'];
    public $timestamps = false;

    public function agents()
    {
        return $this->belongsToMany('Horses\Agent');
    }
}
