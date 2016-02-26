<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function animals()
    {
        return $this->belongsTo('Horses\Animal', 'animal_id');
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

    public function scopeNumberIn($query, $numbers)
    {
        return $query->whereIn('number', $numbers);
    }

    public function scopeAnimal($query, $idAnimal)
    {
        return $query->where('animal_id', $idAnimal);
    }

    public function scopeAnimalIn($query, $idsAnimal)
    {
        return $query->whereIn('animal_id', $idsAnimal);
    }

}
