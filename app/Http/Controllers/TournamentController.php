<?php namespace Horses\Http\Controllers;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\User;
use Horses\Stage;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class TournamentController extends Controller
{
    private $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function selection()
    {
        $oCategory = $this->request->session()->get('oCategory');
        $lstCompetitor = Competitor::category($oCategory->id)
            ->orderBy('number')
            ->get();

        return view('tournament.selection')
            ->with('valid', true)
            ->with('stage', ConstApp::STAGE_SELECCTION)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function classifyFirst()
    {
        $oCategory = $this->request->session()->get('oCategory');
        $lstCompetitor = null;
        $stageStatus = $this->verifyStageClosed($oCategory, ConstDb::STAGE_SELECTION);

        if ($stageStatus->valid) {
            $lstStageJury = $stageStatus->lstStageJury;

            $lstStageJury->sortBy(function ($item) {
                return $item->competitor_id;
            });

            $idsComp = [];
            $idTemp = $lstStageJury->first()->competitor_id;
            $count = 0;

            foreach ($lstStageJury as $stageJury) {
                if ($idTemp == $stageJury->competitor_id) {
                    $count++;
                } else {
                    if ($count >= ConstApp::MIN_VOTE_COMPETITION) {
                        $idsComp[] = $idTemp;
                        $count = 1;
                    }
                }

                $idTemp = $stageJury->competitor_id;
            }

            $lstCompetitor = Competitor::idIn($idsComp)->orderBy('number')->get();
        }

        return view('tournament.classify')
            ->with('post', route('tournament.save.classify_1'))
            ->with('stage', ConstApp::STAGE_CLASSIFY_1)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitor', $lstCompetitor);

    }


    public function classifySecond()
    {
        $oCategory = $this->request->session()->get('oCategory');
        $lstCompetitor = new Collection();
        $stageStatus = $this->verifyStageClosed($oCategory, ConstDb::STAGE_CLASSIFY_1);

        if ($stageStatus->valid) {
            $lstCompetitorFinal = $this->filterCompetitorsWithJury($oCategory, $stageStatus);

            //Filter six first and update others
            $position = 1;

            foreach ($lstCompetitorFinal as $key => $competitor) {
                if ($position <= ConstApp::MAX_WINNERS) {
                    $lstCompetitor->add($competitor);
                } else {
                    //menciones honrosas
                    $competitor->position = $position;
                    $competitor->save();
                }

                $position++;
            }
        }

        return view('tournament.classify')
            ->with('post', route('tournament.save.classify_2'))
            ->with('stage', ConstApp::STAGE_CLASSIFY_2)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function calculateFinal($id, $stageStatus)
    {
        $oCategory = Category::find($id)->first();

        if ($oCategory->status == ConstDb::STATUS_ACTIVE) {
            $lstCompetitorFilter = $this->filterCompetitorsWithJury($oCategory, $stageStatus);
            $position = 1;

            foreach ($lstCompetitorFilter as $index => $competitorFinal) {
                $competitorFinal->position = $position;
                $competitorFinal->save();
                $position++;
            }

            $oCategory->status = ConstDb::STATUS_FINAL;
            $oCategory->save();
        }
    }

    public function filterCompetitorsWithJury($oCategory, $stageStatus)
    {
        $oCatJury = CategoryUser::category($oCategory->id)->diriment(ConstDb::JURY_DIRIMENT)->first();
        $lstStageJury = $stageStatus->lstStageJury;

        //group competitors by position
        $lstCompPoints = $lstStageJury->groupBy('competitor_id')->map(function ($group) {
            $acumm = $group[0];

            for ($i = 1; $i < count($group); $i++) {
                $acumm->position += $group[$i]->position;
            }

            return $acumm;
        });

        //competitors order by position
        $lstCompPoints->sortBy(function ($item) {
            return $item->position;
        });

        $lstCompOrder = $lstCompPoints->groupBy('position')->map(function ($group) use ($lstStageJury, $oCatJury) {
            $count = count($group);

            if ($count > 1) {
                $groupOrder = [];

                foreach ($group as $key => $value) {
                    $stageJury = $lstStageJury->filter(function ($item) use ($value, $oCatJury) {
                        if ($item->user_id == $oCatJury->user_id && $item->competitor_id == $value->competitor_id) {
                            return $item;
                        }
                    })->first();

                    $groupOrder[] = [
                        'orden' => ($stageJury == null) ? $count : $stageJury->position,
                        'stageComp' => $value
                    ];
                }


                //sort group by orden of jury diriment
                sort($groupOrder);
                $newGroup = [];

                for ($i = 0; $i < $count; $i++) {
                    $newGroup[] = $groupOrder[$i]['stageComp'];
                }

                $group = $newGroup;
            }

            return $group;
        });

        //get competitor list from group
        $lstCompFinal = new Collection();
        $allIds = [];

        foreach ($lstCompOrder as $i => $compOrder) {
            foreach ($compOrder as $y => $competitor) {
                $lstCompFinal->add($competitor);
                $allIds[] = $competitor->competitor_id;
            }
        }

        $lstCompetitorTemp = Competitor::idIn($allIds)->get();

        //ranking competitor final
        $lstCompetitorFinal = new Collection();

        foreach ($allIds as $competitor) {
            $competitor = $lstCompetitorTemp->filter(function ($item) use ($competitor) {
                return $item->id == $competitor;
            })->first();

            $lstCompetitorFinal->add($competitor);
        }

        return $lstCompetitorFinal;
    }


    public function saveClassify2(Guard $guard)
    {
        $url = url('/auth/logout');
        $response = $this->save($guard, $this->request, $url, ConstDb::STAGE_CLASSIFY_2);
        $oCategory = $this->request->session()->get('oCategory');

        $stageStatus = $this->verifyStageClosed($oCategory, ConstDb::STAGE_CLASSIFY_2);

        if ($stageStatus->valid) {
            $this->calculateFinal($oCategory->id, $stageStatus);
        }

        return response()->json($response);
    }

    public function saveClassify1(Guard $guard)
    {
        $url = route('tournament.classify_2');
        $response = $this->save($guard, $this->request, $url, ConstDb::STAGE_CLASSIFY_1);

        return response()->json($response);
    }

    public function saveSelection(Guard $guard)
    {
        $url = route('tournament.classify_1');
        $response = $this->save($guard, $this->request, $url, ConstDb::STAGE_SELECTION);

        return response()->json($response);
    }

    public function save($guard, $request, $url, $stage)
    {
        $response = [
            'success' => true,
            'message' => '',
            'url' => $url
        ];

        $params = $request->all();
        $process = $request->get('process');
        $oCategory = $request->session()->get('oCategory');
        $closeProcess = ($process == ConstApp::PROCESS_END) ? true : false;

        Stage::jury($guard->getUser()->id)->stage($stage)->delete();

        foreach ($params as $index => $value) {
            $id = str_replace(ConstApp::PREFIX_COMPETITOR, '', $index);

            if ($value != '0') {
                if (is_numeric($id)) {
                    Stage::create([
                        'competitor_id' => $id,
                        'user_id' => $guard->getUser()->id,
                        'position' => $value,
                        'stage' => $stage
//                        'cerrado' => ($closeProcess) ? ConstDb::STAGE_STATUS_CLOSE : ConstDb::STAGE_STATUS_SAVE
                    ]);
                }
            }
        }

        if ($closeProcess) {
            $oCatUser = CategoryUser::jury($guard->getUser()->id)->category($oCategory->id)->first();
            $oCatUser->actual_stage = $stage;
            $oCatUser->save();
        } else {
            $response['url'] = '';
        }

        return $response;
    }

    public function verifyStageClosed($oCategory, $stage)
    {
        $stageStatus = new \stdClass();
        $stageStatus->valid = true;
        $stageStatus->message = '';
        $stageStatus->lstStageJury = null;

        $lstCatJury = CategoryUser::category($oCategory->id)->get();
        $countJury = $lstCatJury->count();
        $lstIds = [];

        foreach ($lstCatJury as $catJury) {
            switch ($stage) {
                case ConstDb::STAGE_SELECTION:
                    if ($catJury->actual_stage != null &&
                        $catJury->actual_stage != ConstDb::STAGE_ASSISTANCE
                    ) {
                        $lstIds[] = $catJury->user_id;
                    }
                    break;
                case ConstDb::STAGE_CLASSIFY_1:
                    if ($catJury->actual_stage != null &&
                        $catJury->actual_stage != ConstDb::STAGE_ASSISTANCE &&
                        $catJury->actual_stage != ConstDb::STAGE_SELECTION
                    ) {
                        $lstIds[] = $catJury->user_id;
                    }
                    break;
                case ConstDb::STAGE_CLASSIFY_2:
                    if ($catJury->actual_stage != null &&
                        $catJury->actual_stage != ConstDb::STAGE_ASSISTANCE &&
                        $catJury->actual_stage != ConstDb::STAGE_SELECTION &&
                        $catJury->actual_stage != ConstDb::STAGE_CLASSIFY_1
                    ) {
                        $lstIds[] = $catJury->user_id;
                    }
                    break;
            }

        }

//        var_dump(count($lstIds));
//        dd($countJury);
        if (count($lstIds) == $countJury) {
            $oCategory->actual_stage = $stage;
            $oCategory->save();

            $stageStatus->lstStageJury = Stage::juryIn($lstIds)->stage($stage)->orderBy('user_id')->get();
        } else {
            $stageStatus->valid = false;
            $stageStatus->message = 'Todos los jueces aún no terminan la etapa anterior, espere un momento por favor.';
        }

        return $stageStatus;
    }
}
