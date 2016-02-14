<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function animals()
    {
        return $this->belongsTo('Horses\Animal');
    }

    public function scopeTournament($query, $id)
    {
        return $query->where('tournament_id', $id);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    public function scopeNumber($query, $number)
    {
        return $query->where('number', $number);
    }

    public function scopeAnimal($query, $idAnimal)
    {
        return $query->where('animal_id', $idAnimal);
    }

}
