<?php namespace Horses\Http\Controllers\Operator;

use Horses\Agent;
use Horses\Animal;
use Horses\Catalog;
use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
use Horses\Http\Controllers\Controller;
use Illuminate\Http\Request;


class AnimalController extends Controller
{

    private $request;
    private $oTournament;

    private $rules = [
        'name' => 'required|max:45'
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->oTournament = $this->request->session()->get('oTournament');
    }

    public function listParents()
    {
        $gender = $this->request->get('gender');
        $query = strtoupper($this->request->get('query'));
        $lstAnimal = Animal::selectRaw('name AS value, id as data')
            ->where(function ($query) use ($gender) {
                return $query->where('gender', null)
                    ->orWhere('gender', $gender);
            })->where('name', 'like', "%$query%")
            ->get();

        $data = [
            'suggestions' => $lstAnimal->toArray()
        ];

        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $lstAnimals = Animal::with('agents')->get();

        return view('oper.animal.index')
            ->with('tournament', $this->oTournament->description)
            ->with('lstAnimals', $lstAnimals);
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
        $lstCategory = Category::tournament($this->oTournament->id)->statusDiff(ConstDb::STATUS_DELETED)->get(['id', 'description']);
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
            $name = $request->get('name');
            $code = $request->get('code');
            $birthdate = $request->get('birthdate');

            $oAnimal = Animal::name($name)->code($code, true)->first();

            if (!$oAnimal) {
                $next = true;
                $oOwner = null;
                $oBreeder = null;
                $oMom = null;
                $oDad = null;

                $dataOwner = explode(',', $request->get('owner_name'));
                $dataBreeder = explode(',', $request->get('breeder_name'));

                if (count($dataOwner) != 2 || count($dataBreeder) != 2) {
                    $next = false;
                }

                if ($next) {
                    $ownerNames = trim($dataOwner[0]);
                    $ownerLastnames = trim($dataOwner[1]);
                    $breederNames = trim($dataBreeder[0]);
                    $breederLastnames = trim($dataBreeder[1]);
                    $momName = $request->get('mom_name');
                    $dadName = $request->get('dad_name');

                    $lstAgents = Agent::where(function ($query) use ($ownerNames, $ownerLastnames) {
                        return $query->where('names', $ownerNames)
                            ->where('lastnames', $ownerLastnames);
                    })->orWhere(function ($query) use ($breederNames, $breederLastnames) {
                        return $query->where('names', $breederNames)
                            ->where('lastnames', $breederLastnames);
                    })->get();

                    //Owner
                    $oOwner = $lstAgents->filter(function ($item) use ($ownerNames, $ownerLastnames) {
                        if ($item->names == $ownerNames && $item->lastnames == $ownerLastnames) {
                            return $item;
                        }
                    })->first();

                    if (!$oOwner) {
                        $oOwner = Agent::create([
                            'prefix' => $request->get('prefix'),
                            'names' => $ownerNames,
                            'lastnames' => $ownerLastnames
                        ]);
                    }

                    //Breeder
                    $oBreeder = $lstAgents->filter(function ($item) use ($breederNames, $breederLastnames) {
                        if ($item->names == $breederNames && $item->lastnames == $breederLastnames) {
                            return $item;
                        }
                    })->first();

                    if (!$oBreeder) {
                        $oBreeder = Agent::create([
                            'prefix' => $request->get('prefix'),
                            'names' => $breederNames,
                            'lastnames' => $breederLastnames
                        ]);
                    }

                    $lstParents = Animal::name($momName)->name($dadName, true)->get();

                    //Mom
                    $oMom = $lstParents->filter(function ($item) use ($momName) {
                        return $item->name == $momName;
                    })->first();

                    if (!$oMom) {
                        $oMom = Animal::create(['name' => $request->get('mom_name'), 'gender' => ConstDb::GEN_FEMALE]);
                    }

                    //Dad
                    $oDad = $lstParents->filter(function ($item) use ($dadName) {
                        return $item->name == $dadName;
                    })->first();

                    if (!$oDad) {
                        $oDad = Animal::create(['name' => $request->get('dad_name'), 'gender' => ConstDb::GEN_MALE]);;
                    }

                    $dateBirth = \DateTime::createFromFormat('d-m-Y', $birthdate);

                    $oAnimal = Animal::create([
                        'code' => $code,
                        'name' => $name,
                        'birthdate' => $dateBirth->format('Y-m-d'),
                        'mom' => $oMom->id,
                        'dad' => $oDad->id,
                    ]);

                    $oAnimal->agents()->attach([
                        $oOwner->id => ['type' => ConstDb::AGENT_OWNER],
                        $oBreeder->id => ['type' => ConstDb::AGENT_BREEDER]
                    ]);

                    $idsCat = explode(',', $request->get('categories'));
                    $lstCategoriesIds = Category::idsIn($idsCat)->get(['id'])->toArray();

                    $catalogs = [];

                    foreach ($lstCategoriesIds as $key => $value) {
                        $catalogs[] = new Catalog([
                            'category_id' => $value['id'],
                            'tournament_id' => $this->oTournament->id
                        ]);
                    }

                    $oAnimal->catalogs()->saveMany($catalogs);

                    $jResponse['success'] = true;
                    $jResponse['url'] = route('oper.animal.index');
                } else {
                    $jResponse['message'] = ConstMessages::NAME_WITHOUT_COMMA;
                }
            } else {
                if ($oAnimal->name == $name) {
                    $jResponse['message'] = ConstMessages::ANIMAL_NAME_EXISTS;
                } else {
                    $jResponse['message'] = ConstMessages::ANIMAL_CODE_EXISTS;
                }
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
        $oAnimal = Animal::with(['catalogs', 'agents'])->findorFail($id);
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
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
