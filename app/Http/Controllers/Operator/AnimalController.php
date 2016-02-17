<?php namespace Horses\Http\Controllers\Operator;

use Horses\Animal;
use Horses\Catalog;
use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
use Horses\Http\Controllers\Controller;
use Horses\Services\Facades\AnimalFac;
use Horses\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AnimalController extends Controller
{

    private $request;

    private $rules = [
        'name' => 'required|max:45',
        'owner_name' => 'max:130',
        'breeder_name' => 'max:130'
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
    }

    public function listParents()
    {
        $gender = $this->request->get('gender');
        $query = strtoupper($this->request->get('query'));
        $wPrefix = $this->request->get('prefix', true);

        $lstAnimal = Animal::with('breeder')
            ->where(function ($query) use ($gender) {
                if ($gender) {
                    return $query->where('gender', null)
                        ->orWhere('gender', $gender);
                }
                return $query;
            })->where('name', 'like', "%$query%")
            ->get(['name', 'id']);

        $data = [];
        foreach ($lstAnimal as $key => $animal) {
            $prefix = ($animal->breeder->count() == 1 && $wPrefix === true) ? "({$animal->breeder->get(0)->prefix}) " : '';
            $anData['value'] = $prefix . $animal->name;
            $anData['data'] = $animal->id;

            $data[] = $anData;
        }

        $dataFinal = [
            'suggestions' => $data
        ];

        return response()->json($dataFinal);
    }

    public function infoAnimal($id)
    {
        $jResponse = AnimalFac::getInfo($id);
        $oCatalog = Catalog::tournament($this->oTournament->id)->animal($jResponse['id'])->first(['number']);
        $jResponse['number'] = ($oCatalog) ? $oCatalog->number : null;

        return response()->json($jResponse);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = strtoupper($request->get('query'));
        $lstAnimal = DB::table('animal_tournament')
            ->where('tournament_id', $this->oTournament->id)
            ->where(function ($query) use ($search) {
                return $query->where('prefix', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('owner', 'like', "%$search%");
            })->paginate(10);

        return view('oper.animal.index')
            ->with('search', $search)
            ->with('lstAnimal', $lstAnimal)
            ->with('oTournament', $this->oTournament);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $formHeader = ['route' => 'oper.animal.store', 'id' => 'formAnimal', 'class' => 'formuppertext'];
        $lstCategory = $this->getLstCategory();

        return view('oper.animal.maintenance')
            ->with('lstCategory', $lstCategory)
            ->with('title', 'Nuevo Animal')
            ->with('formHeader', $formHeader);
    }

    public function getLstCategory()
    {
        $lstCategory = Category::tournament($this->oTournament->id)->statusDiff(ConstDb::STATUS_DELETED)
            ->orderBy('order', 'ASC')
            ->get(['id', 'description']);
        $lstData = [];

        foreach ($lstCategory as $key => $value) {
            $lstData[$value->id] = $value->description;
        }

        return $lstData;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $name = strtoupper($request->get('name'));
            $code = strtoupper($request->get('code'));
            $prefix = strtoupper($request->get('prefix'));

            $lstAnimal = $this->getVerifyAnimal($name, $code, $prefix);

            if (count($lstAnimal) == 0) {
                $data = $request->all();
                $jResponse = AnimalFac::save($data, $this->oTournament->id);
            } else {
                $jResponse['message'] = ConstMessages::ANIMAL_NAME_CODE_EXISTS;
            }
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $formHeader = ['route' => ['oper.animal.update', $id], 'method' => 'PUT', 'id' => 'formAnimal', 'class' => 'formuppertext'];
        $oAnimal = Animal::with(['catalogs', 'agents'])->find($id);

        if ($oAnimal) {
            $lstCategory = $this->getLstCategory();
            $lstCategorySelected = [];
            $oOwner = null;
            $oBreeder = null;
            $oMom = null;
            $oDad = null;

            foreach ($oAnimal->catalogs as $key => $value) {
                $lstCategorySelected[] = $value->category_id;
            }

            foreach ($oAnimal->agents as $key => $value) {
                if ($value->pivot->type == ConstDb::AGENT_OWNER) {
                    $oOwner = $value;
                } else if ($value->pivot->type == ConstDb::AGENT_BREEDER) {
                    $oBreeder = $value;
                }
            }

            //mom and dad
            if ($oAnimal->mom != '' || $oAnimal->dad != '') {
                $ids = [$oAnimal->mom, $oAnimal->dad];
                $lstParents = Animal::idsIn($ids)->get();

                $oMom = $lstParents->filter(function ($item) use ($oAnimal) {
                    return $item->id == $oAnimal->mom;
                })->first();

                $oDad = $lstParents->filter(function ($item) use ($oAnimal) {
                    return $item->id == $oAnimal->dad;
                })->first();
            }

            return view('oper.animal.maintenance')
                ->with('lstCategory', $lstCategory)
                ->with('oAnimal', $oAnimal)
                ->with('lstCategorySelected', $lstCategorySelected)
                ->with('oOwner', $oOwner)
                ->with('oBreeder', $oBreeder)
                ->with('oMom', $oMom)
                ->with('oDad', $oDad)
                ->with('title', 'Editar Animal')
                ->with('formHeader', $formHeader);
        } else {
            return redirect()->route('oper.animal.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($this->request->all(), $this->rules);

        if ($validator === true) {
            $name = strtoupper($request->get('name'));
            $code = strtoupper($request->get('code'));
            $prefix = strtoupper($request->get('prefix'));

            $lstAnimal = $this->getVerifyAnimal($name, $code, $prefix);

            if (!$lstAnimal || count($lstAnimal) <= 1) {
                $oAnimalS = (count($lstAnimal) == 0) ? null : $lstAnimal[0];
                $oAnimal = Animal::with(['agents', 'catalogs'])->findorFail($id);

                if (!$oAnimalS || $oAnimalS->id == $oAnimal->id) {
                    $jResponse = AnimalFac::save($request->all(), $this->oTournament->id, $oAnimal);
                } else {
                    $jResponse['message'] = ConstMessages::ANIMAL_NAME_CODE_EXISTS;
                }

            } else {
                $jResponse['message'] = ConstMessages::ANIMAL_NAME_CODE_EXISTS;
            }
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    public function getVerifyAnimal($name, $code, $prefix)
    {
        $lstAnimal = null;

        $lstAnimal = DB::table('animal_report')
            ->where('prefix', $prefix)
            ->where('name', $name)
            ->orWhere('code', $code)
            ->get();

        return $lstAnimal;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $oAnimal = Animal::findorFail($id);

        if ($oAnimal) {
            $oAnimal->delete();
        }

        $jResponse['success'] = true;
        $jResponse['url'] = route('oper.animal.index');

        return response()->json($jResponse);
    }
}
