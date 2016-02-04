<?php namespace Horses\Http\Controllers\Commissar;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AssistanceController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $oCategory = $this->request->session()->get('oCategory');
        $totalComp = $oCategory->num_begin + $oCategory->count_competitors;

        return view('commissar.assistance')
            ->with('rpad', strlen($totalComp))
            ->with('oCategory', $oCategory);
    }

    public function save()
    {
        $oCategory = $this->request->session()->get('oCategory');

        foreach ($this->request->all() as $key => $value) {
            if (strpos($key, ConstApp::PREFIX_COMPETITOR) !== false) {
                $id = str_replace(ConstApp::PREFIX_COMPETITOR, '', $key);
                if ($value != '0' && is_numeric($value)) {

                    if ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) {
                        $competitor = Competitor::create([
                            'number' => $id,
                            'category_id' => $oCategory->id,
                            'position' => 0
                        ]);
                    } else {
                        $competitor = Competitor::create([
                            'number' => $id,
                            'category_id' => $oCategory->id
                        ]);
                    }
                }
            }
        }

        if ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) {
            $oCategory->actual_stage = ConstDb::STAGE_SELECTION;
            CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_SELECTION]);
        } else {
            $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
            CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_ASSISTANCE]);
        }

        $oCategory->status = ConstDb::STATUS_IN_PROGRESS;
        $oCategory->save();

        return redirect()->to('/auth/logout');
    }
}
