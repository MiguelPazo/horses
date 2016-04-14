<?php namespace Horses\Http\Controllers\Admin;

use Horses\Animal;
use Horses\Catalog;
use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Http\Controllers\Controller;
use Horses\Services\Facades\AnimalFac;
use Horses\Tournament;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class CatalogController extends Controller
{

    public function report($idTournament)
    {
        $oTournament = Tournament::findorFail($idTournament);

        $lstCatalogReport = DB::table('catalog_report')
            ->where('tournament_id', $oTournament->id)
            ->get();

        $lstCatalogGroup = [];
        $posCat = -1;
        $desPass = null;

        foreach ($lstCatalogReport as $key => $value) {
            if ($desPass != $value->description) {
                $posCat++;
                $desPass = $value->description;
            }

            $lstCatalogGroup[$posCat][] = $value;
        }

        return view('admin.tournament.catalog')
            ->with('oTournament', $oTournament)
            ->with('lstCatalogGroup', $lstCatalogGroup);
    }

    public function verify($idTournament)
    {
        $max = Catalog::tournament($idTournament)->max('number');
        $catGen = ($max) ? (($max > 0) ? true : false) : false;

        return response()->json($catGen);
    }

    public function infoCatalog($idTournament, $numCatalog)
    {
        $jResponse = [
            'success' => false
        ];

        $oCatalog = Catalog::tournament($idTournament)->number($numCatalog)->first(['animal_id', 'number']);

        if ($oCatalog) {
            $jResponse = AnimalFac::getInfo($oCatalog->animal_id);
            $jResponse['number'] = $oCatalog->number;
        }

        return response()->json($jResponse);
    }

    public function assignCatalog($idTournament)
    {
        $jResponse = [
            'success' => false
        ];

        $lstCategory = Category::with(['animals' => function ($query) {
            return $query->orderBy('birthdate', 'DESC');
        }])->tournament($idTournament)->status(ConstDb::STATUS_DELETED, false, true)
            ->orderBy('order')
            ->get();

        $dataUpdate = [];
        $catNumber = 1;

        foreach ($lstCategory as $key => $oCategory) {
            foreach ($oCategory->animals as $key2 => $oAnimal) {
                $ids = array_column($dataUpdate, 'animal_id');

                if (!in_array($oAnimal->id, $ids)) {
                    $dataUpdate[] = [
                        'animal_id' => $oAnimal->id,
                        'number' => $catNumber
                    ];
                    $catNumber++;
                }
            }
        }
        DB::beginTransaction();

        try {
            Catalog::tournament($idTournament)->update(['number' => null]);

            foreach ($dataUpdate as $key => $value) {
                Catalog::tournament($idTournament)->animal($value['animal_id'])
                    ->update(['number' => $value['number']]);
            }

            DB::commit();

            $jResponse['success'] = true;
        } catch (Exception $ex) {
            DB::rollback();
        }

        return response()->json($jResponse);
    }
}
