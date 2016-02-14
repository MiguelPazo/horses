<?php namespace Horses\Http\Controllers\Admin;

use Horses\Animal;
use Horses\Catalog;
use Horses\Category;
use Horses\Http\Controllers\Controller;
use Horses\Services\Facades\AnimalFac;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class CatalogController extends Controller
{

    public function infoCatalog($idTournament, $numCatalog)
    {
        $jResponse = [
            'success' => false
        ];

        $oCatalog = Catalog::tournament($idTournament)->number($numCatalog)->first(['animal_id']);

        if ($oCatalog) {
            $jResponse = AnimalFac::getInfo($oCatalog->animal_id);
        }

        return response()->json($jResponse);
    }

    public function addAnimal($idAnimal, $idCategory)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'number' => 0
        ];

        $oCategory = Category::findorFail($idCategory);
        $lstCatalog = Catalog::tournament($oCategory->tournament_id)->animal($idAnimal)->get();

        if ($lstCatalog->count() > 0) {
            $catalogExist = $lstCatalog->filter(function ($item) use ($oCategory) {
                return $item->category_id == $oCategory->id;
            })->first();

            if (!$catalogExist) {
                $number = $this->saveAnimalCatalog($oCategory, $idAnimal, $lstCatalog->get(0)->number);

                if ($number != 0) {
                    $jResponse['success'] = true;
                    $jResponse['number'] = $number;
                }
            } else {
                $jResponse['message'] = 'El animal ya se encuentra registrado en esta categoría';
            }
        } else {
            $oAnimal = Animal::findorFail($idAnimal);
            $number = $this->saveAnimalCatalog($oCategory, $oAnimal->id);

            if ($number != 0) {
                $jResponse['success'] = true;
                $jResponse['number'] = $number;
            }
        }

        return response()->json($jResponse);
    }

    public function saveAnimalCatalog($oCategory, $idAnimal, $currNumber = null)
    {
        $number = 0;

        DB::beginTransaction();

        try {
            $newNumber = ($currNumber) ? $currNumber : Catalog::tournament($oCategory->tournament_id)->max('number') + 1;

            $newCatalog = Catalog::create([
                'number' => $newNumber,
                'category_id' => $oCategory->id,
                'tournament_id' => $oCategory->tournament_id,
                'animal_id' => $idAnimal
            ]);

            $oCategory->count_competitors = $oCategory->count_competitors + 1;
            $oCategory->save();

            DB::commit();
            $number = $newCatalog->number;
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

        return $number;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
