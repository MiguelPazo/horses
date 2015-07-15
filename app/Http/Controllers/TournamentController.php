<?php namespace Horses\Http\Controllers;

use Horses\Category;
use Horses\CategoryJury;
use Horses\Competitor;
use Horses\Constants\App;
use Horses\Constants\Db;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Jury;
use Horses\Stage;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $lstCompetitor = Competitor::where('categoria_id', '=', $oCategory->id)
            ->orderBy('numero')
            ->get();

        return view('tournament.selection')
            ->with('stage', App::STAGE_SELECCTION)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function classifyFirst()
    {
        $oCategory = $this->request->session()->get('category');
        $lstCompetitor = null;
        $stageStatus = $this->verifyStageClosed($oCategory->id, Db::STAGE_SELECTION);

        if ($stageStatus->valid) {
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
                    if ($count >= App::MIN_VOTE_COMPETITION) {
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
            ->with('stage', App::STAGE_CLASSIFY_1)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitor', $lstCompetitor);

    }


    public function classifySecond()
    {
        $oCategory = $this->request->session()->get('category');
        $lstCompetitor = null;
        $stageStatus = $this->verifyStageClosed($oCategory->id, Db::STAGE_CLASSIFY_1);

        if ($stageStatus->valid) {
            $oDirimente = CategoryJury::where('categoria_id', '=', $oCategory->id)
                ->where('dirimente', '=', Db::JURY_TYPE_DIRIMENTE)
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

            //first six
            $sixIds = [];
            $count = 0;

            foreach ($lstCompPoints as $compPoints) {
                if ($count != 6) {
                    $sixIds[] = $compPoints->participante_id;
                    $count++;
                } else {
                    break;
                }
            }

            $lstCompetitor = Competitor::whereIn('id', $sixIds)
                ->orderBy('numero')
                ->get();
        }

        return view('tournament.classify')
            ->with('stage', App::STAGE_CLASSIFY_2)
            ->with('valid', $stageStatus->valid)
            ->with('message', $stageStatus->message)
            ->with('lstCompetitor', $lstCompetitor);
    }

    public function saveClassify(Guard $guard)
    {
        $url = route('tournament.classify_2');
        $response = $this->save($guard, $this->request, $url, Db::STAGE_CLASSIFY_1);

        return response()->json($response);
    }

    public function saveSelection(Guard $guard)
    {
        $url = route('tournament.classify_1');
        $response = $this->save($guard, $this->request, $url, Db::STAGE_SELECTION);

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
        $closeProcess = ($process == App::PROCESS_END) ? true : false;

        Stage::where('jurado_id', '=', $guard->getUser()->id)
            ->where('descripcion', '=', $stage)
            ->delete();

        foreach ($params as $index => $value) {
            $id = str_replace(App::PREFIX_COMPETITOR, '', $index);

            if ($value != '0') {
                if (is_numeric($id)) {
                    Stage::create([
                        'participante_id' => $id,
                        'jurado_id' => $guard->getUser()->id,
                        'posicion' => $value,
                        'descripcion' => $stage,
                        'cerrado' => ($closeProcess) ? Db::STAGE_STATUS_CLOSE : Db::STAGE_STATUS_SAVE
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

        $lstCatJury = CategoryJury::where('categoria_id', '=', $idCategory)->get();
        $countJury = $lstCatJury->count();
        $lstIds = [];

        if ($countJury >= App::MIN_COUNT_JURY) {

            foreach ($lstCatJury as $stageJury) {
                $lstIds[] = $stageJury->jurado_id;
            }

            $stageStatus->lstStageJury = Stage::whereIn('jurado_id', $lstIds)
                ->where('descripcion', '=', $stage)
                ->where('cerrado', '=', Db::STAGE_STATUS_CLOSE)
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
