<?php namespace Horses\Http\Controllers\Commissar;

use Horses\Catalog;
use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class AssistanceController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
    }

    public function index($id)
    {
        $catalog = [];
        $totalPresent = 0;

        $oCategory = Category::tournament($this->oTournament->id)->status(ConstDb::STATUS_INACTIVE)->findorFail($id);
        $totalComp = $oCategory->num_begin + $oCategory->count_competitors;
        $maxCatalog = Catalog::tournament($this->oTournament->id)->max('number');
        $maxCatalog = ($maxCatalog) ? $maxCatalog : 0;
        $lstCatalog = Catalog::tournament($oCategory->tournament_id)->category($oCategory->id)->orderBy('number')->get();
        $ids = [];

        if ($oCategory->actual_stage == ConstDb::STAGE_ASSISTANCE) {
            $lstCompetitors = Competitor::category($oCategory->id)->orderBy('number')->get();
            $catalogs = [];

            foreach ($lstCompetitors as $key => $value) {
                $catalogs[] = $value->catalog;
                $dCat['number'] = $value->catalog;
                $dCat['present'] = ($value->status == ConstDb::COMPETITOR_PRESENT) ? true : false;
                $totalPresent = ($value->status == ConstDb::COMPETITOR_PRESENT) ? $totalPresent + 1 : $totalPresent;
                $catalog[] = $dCat;
            }

            //ids animals
            foreach ($lstCatalog as $key => $value) {
                $ids[] = $value->animal_id;
            }
        } else {
            foreach ($lstCatalog as $key => $value) {
                if (!$value->number) {
                    $maxCatalog++;
                }

                $ids[] = $value->animal_id;
                $dCat['number'] = ($value->number) ? $value->number : $maxCatalog;
                $dCat['present'] = false;
                $catalog[] = $dCat;
            }
        }

        return view('commissar.assistance')
            ->with('oTournament', $this->oTournament)
            ->with('catalog', $catalog)
            ->with('ids', implode(',', $ids))
            ->with('maxCatalog', $maxCatalog)
            ->with('totalPresent', $totalPresent)
            ->with('rpad', strlen($totalComp))
            ->with('oCategory', $oCategory);
    }

    public function save($id)
    {
        $oCategory = Category::status(ConstDb::STATUS_DELETED, false, true)->findorFail($id);
        $data = $this->request->all();
        $nCatalog = [];
        $totalPresent = 0;
        $idsSelected = explode(',', $data['ids_selected']);

        foreach ($data as $key => $value) {
            if (strpos($key, ConstApp::PREFIX_COMPETITOR) !== false) {
                $posCat = strpos($key, ConstApp::PREFIX_COMPETITOR) + strlen(ConstApp::PREFIX_COMPETITOR);
                $valCatalog = intval(substr($key, $posCat));

                if (!in_array($valCatalog, $nCatalog)) {
                    $nCatalog[] = $valCatalog;
                }
            }
        }

        $lstCatalog = Catalog::tournament($oCategory->tournament_id)->numberIn($nCatalog)->orderBy('number')->get();
        $count = count($nCatalog);
        $insertComp = [];
        $insertCatalog = [];
        $idsAnimalCatalogDelete = [];

        for ($i = 0; $i < $count; $i++) {
            $numCatalog = $nCatalog[$i];
            $index = ConstApp::PREFIX_COMPETITOR . $numCatalog;
            $present = false;

            if (array_key_exists($index, $data) && is_numeric($data[$index]) && $data[$index] != '0') {
                $present = true;
                $totalPresent++;
            }

            $oCatalog = $lstCatalog->filter(function ($item) use ($numCatalog, $oCategory) {
                return $item->number == $numCatalog && $item->category_id == $oCategory->id;
            })->first();

            if (!$oCatalog) {
                $idAnimal = $idsSelected[$i];
                $idsAnimalCatalogDelete[] = $idAnimal;

                $insertCatalog[] = [
                    'number' => $numCatalog,
                    'category_id' => $oCategory->id,
                    'tournament_id' => $oCategory->tournament_id,
                    'animal_id' => $idAnimal
                ];
            }

            $insertComp[] = [
                'number' => $i + 1,
                'category_id' => $oCategory->id,
                'position' => ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) ? 1 : null,
                'catalog' => $numCatalog,
                'status' => ($present) ? ConstDb::COMPETITOR_PRESENT : ConstDb::COMPETITOR_MISSING
            ];
        }

        DB::beginTransaction();

        try {
            Catalog::category($oCategory->id)->animalIn($idsAnimalCatalogDelete)->delete();
            Competitor::category($oCategory->id)->delete();

            if (count($insertCatalog) > 0) {
                Catalog::insert($insertCatalog);
            }

            Competitor::insert($insertComp);

            if ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) {
                $oCategory->actual_stage = ConstDb::STAGE_SELECTION;
                CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_SELECTION]);
            } else {
                $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
                CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_ASSISTANCE]);
            }

            $oCategory->count_competitors = $count;
            $oCategory->count_presents = $totalPresent;
            $oCategory->save();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

        return redirect()->to('/commissar');
    }
}
