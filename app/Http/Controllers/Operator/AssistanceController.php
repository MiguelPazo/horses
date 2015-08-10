<?php namespace Horses\Http\Controllers\Operator;

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

        return view('operator.assistance')->with('oCategory', $oCategory);
    }

    public function save()
    {
        $oCategory = $this->request->session()->get('oCategory');

        foreach ($this->request->all() as $key => $value) {
            if (strpos($key, ConstApp::PREFIX_COMPETITOR) !== false) {
                $id = str_replace(ConstApp::PREFIX_COMPETITOR, '', $key);
                if ($value != '0' && is_numeric($value)) {
                    $competitor = Competitor::create([
                        'number' => $id,
                        'category_id' => $oCategory->id
                    ]);
                }
            }
        }

        $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
        $oCategory->save();

        CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_ASSISTANCE]);

        return redirect()->to('/auth/logout');
    }
}
