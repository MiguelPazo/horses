<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{

    protected $table = 'etapa';
    public $timestamps = false;
    protected $guarded = ['id'];

}
