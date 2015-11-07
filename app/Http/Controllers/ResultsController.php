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

    public function index($tournament)
    {
        $oTournament = Tournament::findOrfail($tournament);

        if ($oTournament && $oTournament->status != ConstDb::STATUS_DELETED) {
            $lstCategory = $this->getCategories($oTournament->id);


            return view('results.index')
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
                $lstCategory = $this->getCategories($oTournament->id);
                $lenCompNum = strlen(Competitor::category($oCategory->id)->max('number'));
                $selection = false;
                $showSecond = false;
                $juryDiriment = null;
                $lstCompetitorWinners = null;
                $lstCompetitorHonorable = null;

                if ($oCategory->actual_stage == ConstDb::STAGE_SELECTION) {
                    $selection = true;
                    $lstCompetitorWinners = Competitor::category($oCategory->id)->selected()->orderBy('number')->get();
                } else {
                    $showSecond = ($oCategory->actual_stage == ConstDb::STAGE_CLASSIFY_2) ? true : false;
                    $juryDiriment = CategoryUser::category($oCategory->id)->diriment(ConstDb::JURY_DIRIMENT)->first();
                    $lstCompetitor = Competitor::category($oCategory->id)->classified()->with('stages.jury')->orderBy('position')->get();
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
                }

                return view('results.category')
                    ->with('selection', $selection)
                    ->with('oCategory', $oCategory)
                    ->with('lstCategory', $lstCategory)
                    ->with('oTournament', $oTournament)
                    ->with('lenCompNum', $lenCompNum)
                    ->with('showSecond', $showSecond)
                    ->with('juryDiriment', $juryDiriment)
                    ->with('lstCompetitorWinners', $lstCompetitorWinners)
                    ->with('lstCompetitorHonorable', $lstCompetitorHonorable);
            } else {
                return redirect()->route('tournament.results', $tournament);
            }
        } else {
            return redirect()->route('tournament.results', $tournament);
        }
    }

    public function getCategories($idTournament)
    {
        $lstCategory = Category::tournament($idTournament)->statusDiff(ConstDb::STATUS_DELETED)->showable(ConstDb::TYPE_CATEGORY_SELECTION)->get();
        $lstCategoryWSelect = Category::tournament($idTournament)->statusDiff(ConstDb::STATUS_DELETED)->showable(ConstDb::TYPE_CATEGORY_WSELECTION)->get();

        $lstCombine = $lstCategory->merge($lstCategoryWSelect);

        $lstCombine->sortByDesc(function ($item) {
            return $item->order;
        });

        return $lstCombine;
    }
}
