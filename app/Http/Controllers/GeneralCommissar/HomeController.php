<?php namespace Horses\Http\Controllers\GeneralCommissar;

use Horses\Category;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Controllers\Controller;
use Horses\Http\Requests;
use Horses\Services\Facades\CategoryFac;
use Horses\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HomeController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $lstCategory = CategoryFac::listToResults($this->oTournament->id, true);

        return view('results.index')
            ->with('complete', false)
            ->with('oTournament', $this->oTournament)
            ->with('lstCategory', $lstCategory);

    }

    public function category($idCategory)
    {
        $oCategory = Category::with(['juries' => function ($query) {
            $query->orderBy('id');
        }])->findorFail($idCategory);

        if ($oCategory->status != ConstDb::STATUS_DELETED) {
            if ($oCategory->actual_stage <> '') {
                $oTournament = Tournament::find($this->oTournament->id);
                $lstCategory = CategoryFac::listToResults($oTournament->id, true);

                $data = null;
                $assistance = false;
                $selection = true;
                $lenCompNum = null;
                $showSecond = false;
                $juryDiriment = null;
                $lstCompetitorWinners = new Collection();
                $lstCompetitorHonorable = new Collection();
                $lstCompetitorLimp = new Collection();
                $limp = ($oCategory->status == ConstDb::STATUS_FINAL) ? false : true;

                if ($oCategory->actual_stage == ConstDb::STAGE_ASSISTANCE) {
                    $assistance = true;
                    $data = CategoryFac::listPresents($oCategory);

                    $lenCompNum = $data['lenCompNum'];
                    $lstCompetitorWinners = $data['lstCompetitorWinners'];
                    $lstCompetitorLimp = $data['lstCompetitorLimp'];
                } else {
                    $data = CategoryFac::results($oCategory);
                    $selection = $data['selection'];
                    $lenCompNum = $data['lenCompNum'];
                    $showSecond = $data['showSecond'];
                    $juryDiriment = $data['juryDiriment'];
                    $lstCompetitorWinners = $data['lstCompetitorWinners'];
                    $lstCompetitorHonorable = $data['lstCompetitorHonorable'];
                    $lstCompetitorLimp = $data['lstCompetitorLimp'];
                }

                return view('results.category')
                    ->with('limp', $limp)
                    ->with('assistance', $assistance)
                    ->with('complete', false)
                    ->with('lstCategory', $lstCategory)
                    ->with('oTournament', $oTournament)
                    ->with('oCategory', $oCategory)
                    ->with('selection', $selection)
                    ->with('lenCompNum', $lenCompNum)
                    ->with('showSecond', $showSecond)
                    ->with('juryDiriment', $juryDiriment)
                    ->with('lstCompetitorWinners', $lstCompetitorWinners)
                    ->with('lstCompetitorHonorable', $lstCompetitorHonorable)
                    ->with('lstCompetitorLimp', $lstCompetitorLimp);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function limpCompetitor($idCategory, $idCompetitor)
    {
        $jResponse = [
            'success' => true,
            'message' => null,
            'url' => null
        ];

        $oCategory = Category::findorFail($idCategory);
        $oCompetitor = Competitor::category($oCategory->id)->findorFail($idCompetitor);

        if ($oCategory->status != ConstDb::STATUS_FINAL) {
            if ($oCompetitor->position === null || ($oCompetitor->position >= 0 && $oCompetitor->position <= ConstApp::MAX_WINNERS)) {
                $oCompetitor->status = ConstDb::COMPETITOR_LIMP;
                $oCompetitor->save();
            } else {
                $jResponse['success'] = false;
                $jResponse['success'] = 'No puede claudicar a este ejemplar.';
            }
        } else {
            $jResponse['success'] = false;
            $jResponse['message'] = 'La evaluación de la categoría ya ha finalizado, por favor actualice la página.';
        }

        $jResponse['url'] = url('general-commissar/category/' . $oCompetitor->category_id);

        return response()->json($jResponse);
    }
}
