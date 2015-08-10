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
        $oCategory = $this->request->session()->get('category');

        switch ($oCategory->etapa_actual) {
            case ConstDb::STAGE_FINAL:
                return redirect()->route('tournament.result');
                break;
            case ConstDb::STAGE_CLASSIFY_1:
                return redirect()->route('tournament.classify_1');
                break;
            case ConstDb::STAGE_CLASSIFY_2:
                return redirect()->route('tournament.classify_2');
                break;
            default:
                $lstCompetitor = Competitor::where('categoria_id', '=', $oCategory->id)
                    ->orderBy('numero')
                    ->get();

                return view('tournament.selection')
                    ->with('stage', ConstApp::STAGE_SELECCTION)
                    ->with('lstCompetitor', $lstCompetitor);
                break;
        }
    }

    public function classifyFirst()
    {
        $oCategory = $this->request->session()->get('category');
        $lstCompetitor = null;
        $stageStatus = $this->verifyStageClosed($oCategory->id, ConstDb::STAGE_SELECTION);

        if ($stageStatus->valid) {
            $oCategoryDb = Category::find($oCategory->id)->first();
            $oCategoryDb->etapa_actual = ConstDb::STAGE_CLASSIFY_1;
            $oCategoryDb->save();

            $lstStageJury = $stageStatus->lstStageJury;

            $lstStageJury->sortBy(function ($item) {
                return $item->participante_id;
            });

            $idsComp = [];
            $idTemp = $lstStageJury->first()->participante_id;
            $count = 0;

            foreach ($lstStageJury as $stageJury) {
                if ($idTemp == $stageJury->participante_id) {
                    $count++;
                } else {
                    if ($count >= ConstApp::MIN_VOTE_COMPETITION) {
                        $idsComp[] = $idTemp;
                        $count = 1;
                    }
                }

                $idTemp = $stageJury->participante_id;
            }

            $lstCompetitor = Competitor::whereIn('id', $idsComp)
                ->orderBy('numero')
                ->get();
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
        $oCategory = $this->request->session()->get('category');
        $lstCompetitor = new Collection();
        $stageStatus = $this->verifyStageClosed($oCategory->id, ConstDb::STAGE_CLASSIFY_1);

        if ($stageStatus->valid) {
            $oCategoryDb = Category::find($oCategory->id)->first();
            $oCategoryDb->etapa_actual = ConstDb::STAGE_CLASSIFY_2;
            $oCategoryDb->save();

            $lstCompetitorFinal = $this->filterCompetitorsWithJury($oCategory, $stageStatus);

            //Filter six first and update others
            $position = 1;

            foreach ($lstCompetitorFinal as $key => $competitor) {
                if ($position <= ConstApp::MAX_WINNERS) {
                    $lstCompetitor->add($competitor);
                } else {
                    $competitor->puesto = $position;
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

    public function result()
    {
        $oCategory = $this->request->session()->get('category');
        $lstCompetitorRight = new Collection();
        $lstCompetitorLeft = new Collection();
        $stageStatus = $this->verifyStageClosed($oCategory->id, ConstDb::STAGE_CLASSIFY_2);

        if ($stageStatus->valid) {
            $oCategoryDb = Category::find($oCategory->id)->first();

            if ($oCategoryDb->etapa_actual != ConstDb::STAGE_FINAL) {
                $lstCompetitorFilter = $this->filterCompetitorsWithJury($oCategory, $stageStatus);
                $position = 1;

                foreach ($lstCompetitorFilter as $index => $competitorFinal) {
                    $competitorFinal->puesto = $position;
                    $competitorFinal->save();
                    $position++;
                }

                $oCategoryDb->etapa_actual = ConstDb::STAGE_FINAL;
                $oCategoryDb->save();
            }

            $lstCompetitorFinal = Competitor::where('categoria_id', '=', $oCategory->id)
                ->where('puesto', '<>', '')
                ->orderBy('puesto')
                ->get();

            $count = 0;

            foreach ($lstCompetitorFinal as $index => $competitor) {
                if ($count < ConstApp::MAX_WINNERS) {
                    $lstCompetitorLeft->add($competitor);
                    $count++;
                } else {
                    $lstCompetitorRight->add($competitor);
                }
            }

        }

        return view('tournament.result')
            ->with('post', route('tournament.save.classify_2'))
            ->with('stage', ConstApp::STAGE_RESULTS)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitorRight', $lstCompetitorRight)
            ->with('lstCompetitorLeft', $lstCompetitorLeft);
    }

    public function filterCompetitorsWithJury($oCategory, $stageStatus)
    {
        $oDirimente = CategoryUser::where('categoria_id', '=', $oCategory->id)
            ->where('dirimente', '=', ConstDb::JURY_DIRIMENT)
            ->first();

        $lstStageJury = $stageStatus->lstStageJury;

        //group competitors by position
        $lstCompPoints = $lstStageJury->groupBy('participante_id')->map(function ($group) {
            $acumm = $group[0];

            for ($i = 1; $i < count($group); $i++) {
                $acumm->posicion += $group[$i]->posicion;
            }

            return $acumm;
        });


        //competitors order by position
        $lstCompPoints->sortBy(function ($item) {
            return $item->posicion;
        });

        $lstCompOrder = $lstCompPoints->groupBy('posicion')->map(function ($group) use ($lstStageJury, $oDirimente) {
            $count = count($group);

            if ($count > 1) {
                $groupOrder = [];

                foreach ($group as $key => $value) {
                    $stageJury = $lstStageJury->filter(function ($item) use ($value, $oDirimente) {
                        if ($item->jurado_id == $oDirimente->jurado_id && $item->participante_id == $value->participante_id) {
                            return $item;
                        }
                    })->first();

                    $groupOrder[] = [
                        'orden' => ($stageJury == null) ? $count : $stageJury->posicion,
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

        foreach ($lstCompOrder as $i => $compOrder) {
            foreach ($compOrder as $y => $competitor) {
                $lstCompFinal->add($competitor);
            }
        }

        //get competitor details
        $allIds = [];
        foreach ($lstCompFinal as $compFinal) {
            $allIds[] = $compFinal->participante_id;
        }

        $lstCompetitorTemp = Competitor::whereIn('id', $allIds)
            ->get();

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
        $url = route('tournament.result');
        $response = $this->save($guard, $this->request, $url, ConstDb::STAGE_CLASSIFY_2);

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
        $closeProcess = ($process == ConstApp::PROCESS_END) ? true : false;

        Stage::where('jurado_id', '=', $guard->getUser()->id)
            ->where('descripcion', '=', $stage)
            ->delete();

        foreach ($params as $index => $value) {
            $id = str_replace(ConstApp::PREFIX_COMPETITOR, '', $index);

            if ($value != '0') {
                if (is_numeric($id)) {
                    Stage::create([
                        'participante_id' => $id,
                        'jurado_id' => $guard->getUser()->id,
                        'posicion' => $value,
                        'descripcion' => $stage,
                        'cerrado' => ($closeProcess) ? ConstDb::STAGE_STATUS_CLOSE : ConstDb::STAGE_STATUS_SAVE
                    ]);
                }
            }
        }

        if (!$closeProcess) {
            $response['url'] = '';
        }

        return $response;
    }

    public function verifyStageClosed($idCategory, $stage)
    {
        $stageStatus = new \stdClass();
        $stageStatus->valid = true;
        $stageStatus->message = '';
        $stageStatus->lstStageJury = null;

        $lstCatJury = CategoryUser::where('categoria_id', '=', $idCategory)->get();
        $countJury = $lstCatJury->count();
        $lstIds = [];

        if ($countJury >= ConstApp::MIN_COUNT_JURY) {

            foreach ($lstCatJury as $stageJury) {
                $lstIds[] = $stageJury->jurado_id;
            }

            $stageStatus->lstStageJury = Stage::whereIn('jurado_id', $lstIds)
                ->where('descripcion', '=', $stage)
                ->where('cerrado', '=', ConstDb::STAGE_STATUS_CLOSE)
                ->orderBy('jurado_id')
                ->get();

            $idTemp = 0;
            $countIds = 0;

            foreach ($stageStatus->lstStageJury as $stageJury) {
                if ($idTemp != $stageJury->jurado_id) {
                    $countIds++;
                }

                $idTemp = $stageJury->jurado_id;
            }

            if ($countIds != $countJury) {
                $stageStatus->valid = false;
                $stageStatus->message = 'Todos los jueces aún no terminan la etapa anterior, espere un momento por favor.';
            }
        } else {
            $stageStatus->valid = false;
            $stageStatus->message = 'Aún no se han registrado todos los jueces, espere un momento por favor.';
        }

        return $stageStatus;
    }
}
