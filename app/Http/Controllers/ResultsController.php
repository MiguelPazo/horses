<?php namespace Horses\Http\Controllers;

use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Services\Facades\CategoryFac;
use Horses\Tournament;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index($tournament)
    {
        $oTournament = Tournament::findOrfail($tournament);

        if ($oTournament && $oTournament->status != ConstDb::STATUS_DELETED) {
            $lstCategory = CategoryFac::listToResults($oTournament->id);

            return view('results.index')
                ->with('complete', true)
                ->with('oTournament', $oTournament)
                ->with('lstCategory', $lstCategory);
        }
    }

    public function category($tournament, $category)
    {
        $oCategory = Category::with(['juries' => function ($query) {
            $query->orderBy('id');
        }])->findorFail($category);

        if ($oCategory->status != ConstDb::STATUS_DELETED) {
            if ($oCategory->actual_stage == ConstDb::STAGE_SELECTION
                || $oCategory->actual_stage == ConstDb::STAGE_CLASSIFY_1
                || $oCategory->status == ConstDb::STATUS_FINAL
            ) {
                $oTournament = Tournament::find($tournament);
                $lstCategory = CategoryFac::listToResults($oTournament->id);

                $data = CategoryFac::results($oCategory);
                $selection = $data['selection'];
                $lenCompNum = $data['lenCompNum'];
                $showSecond = $data['showSecond'];
                $juryDiriment = $data['juryDiriment'];
                $lstCompetitorWinners = $data['lstCompetitorWinners'];
                $lstCompetitorHonorable = $data['lstCompetitorHonorable'];
                $lstCompetitorLimp = $data['lstCompetitorLimp'];

                return view('results.category')
                    ->with('limp', false)
                    ->with('assistance', false)
                    ->with('complete', true)
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
                return redirect()->route('tournament.results', $tournament);
            }
        } else {
            return redirect()->route('tournament.results', $tournament);
        }
    }
}
