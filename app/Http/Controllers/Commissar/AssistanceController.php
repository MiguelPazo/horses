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
        $catalogNumbers = [];
        $totalPresent = 0;

        $oCategory = Category::tournament($this->oTournament->id)->status(ConstDb::STATUS_INACTIVE)->findorFail($id);
        $totalComp = $oCategory->num_begin + $oCategory->count_competitors;
        $lstCatalog = Catalog::tournament($oCategory->tournament_id)->category($oCategory->id)->orderBy('number')->get();

        if ($oCategory->actual_stage == ConstDb::STAGE_ASSISTANCE) {
            $lstCompetitors = Competitor::category($oCategory->id)->orderBy('number')->get();

            foreach ($lstCompetitors as $key => $value) {
                $catalogNumbers[] = $value->catalog;
                $dCat['number'] = $value->catalog;
                $dCat['present'] = ($value->status == ConstDb::COMPETITOR_PRESENT) ? true : false;
                $totalPresent = ($value->status == ConstDb::COMPETITOR_PRESENT) ? $totalPresent + 1 : $totalPresent;
                $catalog[] = $dCat;
            }
        } else {
            foreach ($lstCatalog as $key => $value) {
                $catalogNumbers[] = $value->number;
                $dCat['number'] = $value->number;
                $dCat['present'] = false;
                $catalog[] = $dCat;
            }
        }

        return view('commissar.assistance')
            ->with('oTournament', $this->oTournament)
            ->with('catalog', $catalog)
            ->with('catalogNumbers', implode(',', $catalogNumbers))
            ->with('totalPresent', $totalPresent)
            ->with('rpad', strlen($totalComp))
            ->with('oCategory', $oCategory);
    }

    public function save($id)
    {
        $oCategory = Category::status(ConstDb::STATUS_DELETED, false, true)->findorFail($id);
        $lstCatalog = Catalog::tournament($oCategory->tournament_id)->category($oCategory->id)->orderBy('number')->get();

        $data = $this->request->all();
        $count = $lstCatalog->count();

        DB::beginTransaction();

        try {
            Competitor::category($oCategory->id)->delete();
            $totalPresent = 0;

            for ($i = 0; $i < $count; $i++) {
                $index = ConstApp::PREFIX_COMPETITOR . $lstCatalog->get($i)->number;
                $present = false;

                if (array_key_exists($index, $data) && is_numeric($data[$index]) && $data[$index] != '0') {
                    $present = true;
                    $totalPresent++;
                }

                Competitor::create([
                    'number' => $i + 1,
                    'category_id' => $oCategory->id,
                    'position' => 0,
                    'catalog' => $lstCatalog->get($i)->number,
                    'status' => ($present) ? ConstDb::COMPETITOR_PRESENT : ConstDb::COMPETITOR_MISSING
                ]);
            }

            $oCategory->count_presents = $totalPresent;
            $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
            $oCategory->save();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

        return redirect()->to('/commissar');
    }
}
