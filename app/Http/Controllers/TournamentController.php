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
    private $category;
    private $lenCompNum;


    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->category = $request->session()->get('oCategory');

        $maxComp = Competitor::category($this->category->id)->max('number');
        $this->lenCompNum = strlen($maxComp);
    }

    public function selection()
    {
        $lstCompetitor = Competitor::category($this->category->id)
            ->orderBy('number')
            ->get();

        return view('tournament.selection')
            ->with('lenCompNum', $this->lenCompNum)
            ->with('count', $lstCompetitor->count())
            ->with('valid', true)
            ->with('stage', ConstApp::STAGE_SELECCTION)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function classifyFirst()
    {
        $lstCompetitor = null;
        $stageStatus = $this->verifyStageClosed($this->category, ConstDb::STAGE_SELECTION);

        if ($stageStatus->valid) {
            $lstStageJury = $stageStatus->lstStageJury;

            $lstStageJury->sortBy(function ($item) {
                return $item->competitor_id;
            });

            $idsComp = [];
            $lstCompPoints = $lstStageJury->groupBy('competitor_id');

            foreach ($lstCompPoints as $key => $value) {
                if (count($value) >= ConstApp::MIN_VOTE_COMPETITION) {
                    $idsComp[] = $key;
                }
            }

            $lstCompetitor = Competitor::idIn($idsComp)->orderBy('number')->get();
        }

        return view('tournament.classify')
            ->with('lenCompNum', $this->lenCompNum)
            ->with('post', route('tournament.save.classify_1'))
            ->with('stage', ConstApp::STAGE_CLASSIFY_1)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function classifySecond()
    {
        $lstCompetitor = new Collection();
        $stageStatus = $this->verifyStageClosed($this->category, ConstDb::STAGE_CLASSIFY_1);

        if ($stageStatus->valid) {
            $lstCompetitor = Competitor::category($this->category->id)->classified()->orderBy('position')->limit(ConstApp::MAX_WINNERS)->get();
        }

        return view('tournament.classify')
            ->with('lenCompNum', $this->lenCompNum)
            ->with('post', route('tournament.save.classify_2'))
            ->with('stage', ConstApp::STAGE_CLASSIFY_2)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function saveClassify2(Guard $guard)
    {
        $url = url('/auth/logout');
        $response = $this->save($guard, $this->request, $url, ConstDb::STAGE_CLASSIFY_2);
        $stageStatus = $this->verifyStageClosed($this->category, ConstDb::STAGE_CLASSIFY_2);

        if ($stageStatus->valid) {
            $this->calculateFinal($this->category->id, $stageStatus);
        }

        return response()->json($response);
    }

    public function saveClassify1(Guard $guard)
    {
        $url = route('tournament.classify_2');
        $response = $this->save($guard, $this->request, $url, ConstDb::STAGE_CLASSIFY_1);

        $stageStatus = $this->verifyStageClosed($this->category, ConstDb::STAGE_CLASSIFY_1);

        if ($stageStatus->valid) {
            $lstCompetitorFinal = $this->filterCompetitorsWithJury($this->category, $stageStatus);

            //Filter six first and update others
            $position = 1;

            foreach ($lstCompetitorFinal as $key => $competitor) {
                $competitor->position = $position;
                $competitor->save();

                $position++;
            }
        }

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
        $closeProcess = ($process == ConstApp::PROCESS_END) ? true : false;

        Stage::juryId($guard->getUser()->id)->stage($stage)->category($this->category->id)->delete();

        foreach ($params as $index => $value) {
            $id = str_replace(ConstApp::PREFIX_COMPETITOR, '', $index);

            if ($value != '0') {
                if (is_numeric($id)) {
                    Stage::create([
                        'competitor_id' => $id,
                        'user_id' => $guard->getUser()->id,
                        'position' => $value,
                        'stage' => $stage,
                        'category_id' => $this->category->id
                    ]);
                }
            }
        }

        if ($closeProcess) {
            $oCatUser = CategoryUser::jury($guard->getUser()->id)->category($this->category->id)->first();
            $oCatUser->actual_stage = $stage;
            $oCatUser->save();
        } else {
            $response['url'] = '';
        }

        return $response;
    }

    public function filterCompetitorsWithJury($oCategory, $stageStatus)
    {
        $oCatJury = CategoryUser::category($oCategory->id)->diriment(ConstDb::JURY_DIRIMENT)->first();
        $lstStageJury = $stageStatus->lstStageJury;
        $lstCompPoints = new Collection();

        //group competitors by position
        $lstStageJuryGroup = $lstStageJury->groupBy('competitor_id');

        foreach ($lstStageJuryGroup as $key => $group) {
            $stageJuryTemp = null;
            $sum = 0;

            foreach ($group as $sjKey => $sjValue) {
                $stageJuryTemp = $sjValue;
                $sum += $sjValue->position;
            }

            $stageJuryTemp->position = $sum;

            $lstCompPoints->add($stageJuryTemp);
        }


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
        $allIds = [];
        $idwPoints = [];

        foreach ($lstCompOrder as $i => $compOrder) {
            foreach ($compOrder as $y => $competitor) {
                $allIds[] = $competitor->competitor_id;
                $idwPoints[] = [
                    'id' => $competitor->competitor_id,
                    'points' => $competitor->position,
                ];
            }
        }

        $lstCompetitorTemp = Competitor::idIn($allIds)->get();

        //ranking competitor final
        $lstCompetitorFinal = new Collection();

        foreach ($idwPoints as $key => $value) {
            $idComp = $value['id'];

            $competitor = $lstCompetitorTemp->filter(function ($item) use ($idComp) {
                return $item->id == $idComp;
            })->first();

            $competitor->points = $value['points'];
            $competitor->save();

            $lstCompetitorFinal->add($competitor);
        }

        return $lstCompetitorFinal;
    }

    public function calculateFinal($id, $stageStatus)
    {
        $oCategory = Category::find($id);

        if ($oCategory->status == ConstDb::STATUS_IN_PROGRESS) {
            $lstCompetitorFilter = $this->filterCompetitorsWithJury($oCategory, $stageStatus);
            $position = 1;

            foreach ($lstCompetitorFilter as $index => $competitorFinal) {
                $competitorFinal->position = $position;
                $competitorFinal->save();
                $position++;
            }

            $oCategory->status = ConstDb::STATUS_FINAL;
            $oCategory->save();

            Stage::category($id)->update(['status' => ConstDb::STATUS_FINAL]);
        }
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

        if (count($lstIds) == $countJury) {
            $oCategory->actual_stage = $stage;
            $oCategory->save();

            $stageStatus->lstStageJury = Stage::juryIn($lstIds)->stage($stage)->status(ConstDb::STATUS_ACTIVE)->orderBy('competitor_id')->get();
        } else {
            $stageStatus->valid = false;
            $stageStatus->message = 'Todos los jueces aún no terminan la etapa anterior, espere un momento por favor.';
        }

        return $stageStatus;
    }
}
