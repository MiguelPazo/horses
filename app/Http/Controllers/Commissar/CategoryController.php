<?php namespace Horses\Http\Controllers\Commissar;

use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Http\Controllers\Controller;
use Horses\Http\Requests;
use Horses\Services\Facades\CategoryFac;
use Horses\Tournament;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
    }

    public function getEnable($id)
    {
        $jResponse = CategoryFac::enable($id);

        if ($jResponse['success'] === true) {
            $oCategory = $jResponse['object'];
            $jResponse['url'] = route('admin.tournament.category', $oCategory->tournament_id);
        }

        unset($jResponse['object']);

        return response()->json($jResponse);
    }

    public function getIndex()
    {
        $lstCategory = CategoryFac::fetchAll($this->oTournament->id);

        return view('commissar.index')
            ->with('lstCategory', $lstCategory)
            ->with('oTournament', $this->oTournament);
    }

}
