<?php namespace Horses\Http\Controllers;

use Horses\Category;
use Horses\CategoryJury;
use Horses\Competitor;
use Horses\Constants\App;
use Horses\Constants\Db;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Stage;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

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
        $lstIds = [];
        $valid = true;
        $message = '';
        $lstCompetitor = null;

        $lstCatJury = CategoryJury::where('categoria_id', '=', $oCategory->id)->get();
        $countJury = $lstCatJury->count();

        if ($countJury >= App::MIN_COUNT_JURY) {

            foreach ($lstCatJury as $stageJury) {
                $lstIds[] = $stageJury->jurado_id;
            }

            $lstStageJury = Stage::whereIn('jurado_id', $lstIds)
                ->where('descripcion', '=', Db::STAGE_SELECTION)
                ->where('cerrado', '=', Db::STAGE_STATUS_CLOSE)
                ->orderBy('jurado_id')
                ->get();

            $idTemp = 0;
            $countIds = 0;

            foreach ($lstStageJury as $stageJury) {
                if ($idTemp != $stageJury->jurado_id) {
                    $countIds++;
                }

                $idTemp = $stageJury->jurado_id;
            }

            if ($countIds == $countJury) {
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

                $lstCompetitor = Competitor::whereIn('id', $idsComp)->get();
            } else {
                $valid = false;
                $message = 'Todos los jueces aún no terminan la primera etapa, espere un momento por favor.';
            }
        } else {
            $valid = false;
            $message = 'Aún no se han registrado todos los jueces, espere un momento por favor.';
        }

        return view('tournament.classify1')
            ->with('stage', App::STAGE_CLASSIFY_1)
            ->with('valid', $valid)
            ->with('message', $message)
            ->with('lstCompetitor', $lstCompetitor);

    }

    public
    function classifySecond()
    {

    }

    public
    function saveSelection(Guard $guard)
    {
        $response = [
            'success' => true,
            'message' => '',
            'url' => route('tournament.classify_1')
        ];

        $params = $this->request->all();
        $process = $this->request->get('process');
        $closeProcess = ($process == App::PROCESS_END) ? true : false;

        Stage::where('jurado_id', '=', $guard->getUser()->id)
            ->where('descripcion', '=', Db::STAGE_SELECTION)
            ->delete();

        foreach ($params as $index => $value) {
            if ($value == '1') {
                $id = str_replace(App::PREFIX_COMPETITOR, '', $index);

                if (is_numeric($id)) {
                    Stage::create([
                        'participante_id' => $id,
                        'jurado_id' => $guard->getUser()->id,
                        'posicion' => 1,
                        'descripcion' => Db::STAGE_SELECTION,
                        'cerrado' => ($closeProcess) ? Db::STAGE_STATUS_CLOSE : Db::STAGE_STATUS_SAVE
                    ]);
                }
            }
        }

        if (!$closeProcess) {
            $response['url'] = '';
        }

        return response()->json($response);
    }
}
