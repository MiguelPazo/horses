<?php namespace Horses\Http\Controllers;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Stage;
use Horses\Tournament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $oTournament = Tournament::status(ConstDb::STATUS_ACTIVE)->first();

        if ($oTournament) {
            $lstCategory = Category::tournament($oTournament->id)->finals()->orderBy('description')->get();

            return view('results.index')
                ->with('oTournament', $oTournament)
                ->with('lstCategory', $lstCategory);
        } else {

        }
    }

    public function category($id)
    {
        $oCategory = Category::with(['juries' => function ($query) {
            $query->orderBy('id');
        }])->findorFail($id);

        $showSecond = ($oCategory->actual_stage == ConstDb::STAGE_CLASSIFY_2) ? true : false;
        $juryDiriment = CategoryUser::category($oCategory->id)->diriment(ConstDb::JURY_DIRIMENT)->first();

        $oTournament = Tournament::find($oCategory->tournament_id);
        $lstCategory = Category::tournament($oTournament->id)->finals()->orderBy('description')->get();

        $lstCompetitor = Competitor::category($oCategory->id)->classified()->with('stages.jury')->orderBy('position')->get();
        $lenCompNum = strlen(Competitor::category($oCategory->id)->max('number'));

        if ($lstCompetitor) {
            $count = $lstCompetitor->count();

            for ($i = 0; $i < $count; $i++) {
                $lstCompetitor->get($i)->stages->sortBy(function ($item) {
                    return $item->jury->id;
                });
            }

            $lstCompetitorWinners = new Collection();
            $lstCompetitorHonorable = new Collection();
            $count = 1;

            foreach ($lstCompetitor as $key => $competitor) {
                if ($count <= ConstApp::MAX_WINNERS) {
                    $lstCompetitorWinners->add($competitor);

                } else if (($count - ConstApp::MAX_WINNERS) <= ConstApp::MAX_HONORABLE) {
                    $lstCompetitorHonorable->add($competitor);
                }

                $count++;
            }

            return view('results.category')
                ->with('lenCompNum', $lenCompNum)
                ->with('showSecond', $showSecond)
                ->with('lstJury', $oCategory->juries)
                ->with('dirimentId', $juryDiriment->user_id)
                ->with('active', $oCategory->id)
                ->with('lstCategory', $lstCategory)
                ->with('oTournament', $oTournament)
                ->with('oCategory', $oCategory)
                ->with('lstCompetitorWinners', $lstCompetitorWinners)
                ->with('lstCompetitorHonorable', $lstCompetitorHonorable);
        } else {

        }
    }
}
