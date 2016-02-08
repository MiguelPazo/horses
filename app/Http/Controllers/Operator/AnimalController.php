<?php namespace Horses\Http\Controllers\Operator;

use Horses\Agent;
use Horses\Animal;
use Horses\Catalog;
use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
use Horses\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;


class AnimalController extends Controller
{

    private $request;
    private $oTournament;

    private $rules = [
        'name' => 'required|max:45',
        'owner_name' => 'max:130',
        'breeder_name' => 'max:130'
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
        $lstAnimal = Animal::with('breeder')
            ->where(function ($query) use ($gender) {
                return $query->where('gender', null)
                    ->orWhere('gender', $gender);
            })->where('name', 'like', "%$query%")
            ->get(['name', 'id']);

        $data = [];
        foreach ($lstAnimal as $key => $animal) {
            $prefix = ($animal->breeder->count() == 1) ? "({$animal->breeder->get(0)->prefix}) " : '';
            $anData['value'] = $prefix . $animal->name;
            $anData['data'] = $animal->id;

            $data[] = $anData;
        }

        $dataFinal = [
            'suggestions' => $data
        ];

        return response()->json($dataFinal);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $lstAnimals = Animal::with(['agents', 'catalogs' => function ($query) {
            return $query->where('tournament_id', $this->oTournament->id);
        }])->get();

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
            $name = strtoupper($request->get('name'));
            $code = strtoupper($request->get('code'));

            $lstAnimal = Animal::name($name)->code($code, true)->whereNull('deleted_at')->get();

            if ($lstAnimal->count() == 0) {
                $jResponse = $this->saveAnimal($request->all());
            } else {
                $jResponse['message'] = ConstMessages::ANIMAL_NAME_CODE_EXISTS;
            }
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    public function saveAnimal($data, $oAnimal = null)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $name = strtoupper($data['name']);
        $code = strtoupper($data['code']);
        $birthdate = $data['birthdate'];
        $momName = strtoupper(trim(substr($data['mom_name'], strpos($data['mom_name'], ')'))));
        $dadName = strtoupper(trim(substr($data['dad_name'], strpos($data['dad_name'], ')') + 1)));
        $prefix = strtoupper($data['prefix']);
        $next = true;
        $oOwner = null;
        $oBreeder = null;
        $oMom = null;
        $oDad = null;

        $withOwner = ($data['owner_name'] != '') ? true : false;
        $withBreeder = ($data['breeder_name'] != '') ? true : false;
        $withMom = ($momName != '') ? true : false;
        $withDad = ($dadName != '') ? true : false;

        $dataOwner = explode(',', strtoupper($data['owner_name']));
        $dataBreeder = explode(',', strtoupper($data['breeder_name']));

        $next = ($withOwner) ? ((count($dataOwner) != 2) ? false : true) : true;
        $next = ($withBreeder) ? ((count($dataBreeder) != 2) ? false : true) : true;

        if ($next) {
            $ownerNames = ($withOwner) ? trim($dataOwner[0]) : '';
            $ownerLastnames = ($withOwner) ? trim($dataOwner[1]) : '';
            $breederNames = ($withBreeder) ? trim($dataBreeder[0]) : '';
            $breederLastnames = ($withBreeder) ? trim($dataBreeder[1]) : '';

            $lstAgents = Agent::where(function ($query) use ($ownerNames, $ownerLastnames) {
                return $query->where('names', $ownerNames)
                    ->where('lastnames', $ownerLastnames);
            })->orWhere(function ($query) use ($breederNames, $breederLastnames) {
                return $query->where('names', $breederNames)
                    ->where('lastnames', $breederLastnames);
            })->get();

            DB::beginTransaction();

            try {
                //Owner
                if ($withOwner) {
                    $oOwner = $lstAgents->filter(function ($item) use ($ownerNames, $ownerLastnames) {
                        if ($item->names == $ownerNames && $item->lastnames == $ownerLastnames) {
                            return $item;
                        }
                    })->first();

                    if (!$oOwner) {
                        $oOwner = Agent::create([
                            'names' => $ownerNames,
                            'lastnames' => $ownerLastnames
                        ]);
                    }
                }

                //Breeder
                if ($withBreeder) {
                    $oBreeder = $lstAgents->filter(function ($item) use ($breederNames, $breederLastnames) {
                        if ($item->names == $breederNames && $item->lastnames == $breederLastnames) {
                            return $item;
                        }
                    })->first();

                    if (!$oBreeder) {
                        if ($oOwner->names == $breederNames && $oOwner->lastnames == $breederLastnames) {
                            $oOwner->prefix = strtoupper($data['prefix']);
                            $oOwner->save();
                            $oBreeder = $oOwner;
                        } else {
                            $oBreeder = Agent::create([
                                'prefix' => strtoupper($data['prefix']),
                                'names' => $breederNames,
                                'lastnames' => $breederLastnames
                            ]);
                        }
                    } else if ($oBreeder->prefix != $prefix) {
                        $oBreeder->prefix = $prefix;
                        $oBreeder->save();
                    }
                }

                $lstParents = Animal::name($momName)->name($dadName, true)->get();

                //Mom
                if ($withMom) {
                    $oMom = $lstParents->filter(function ($item) use ($momName) {
                        return $item->name == $momName;
                    })->first();

                    if (!$oMom) {
                        $oMom = Animal::create(['name' => $momName, 'gender' => ConstDb::GEN_FEMALE]);
                    }
                }

                //Dad
                if ($withDad) {
                    $oDad = $lstParents->filter(function ($item) use ($dadName) {
                        return $item->name == $dadName;
                    })->first();

                    if (!$oDad) {
                        $oDad = Animal::create(['name' => $dadName, 'gender' => ConstDb::GEN_MALE]);;
                    }
                }

                if ($oAnimal) {
                    $oAnimal->agents()->detach();
                    $oAnimal->catalogs()->delete();

                    $oAnimal->code = $code;
                    $oAnimal->name = $name;
                    $oAnimal->birthdate = $birthdate;
                    $oAnimal->mom = ($withMom) ? $oMom->id : null;
                    $oAnimal->dad = ($withDad) ? $oDad->id : null;
                    $oAnimal->save();
                } else {
                    $oAnimal = Animal::create([
                        'code' => $code,
                        'name' => $name,
                        'birthdate' => $birthdate,
                        'mom' => ($withMom) ? $oMom->id : null,
                        'dad' => ($withDad) ? $oDad->id : null,
                    ]);
                }

                //Agents
                $dataAttach = [];

                if ($withOwner) {
                    $dataAttach[$oOwner->id] = ['type' => ConstDb::AGENT_OWNER];
                }

                if ($withBreeder) {
                    $dataAttach[$oBreeder->id] = ['type' => ConstDb::AGENT_BREEDER];
                }

                switch (count($dataAttach)) {
                    case 1:
                        $oAnimal->agents()->attach($dataAttach);
                        break;
                    case 2:
                        if ($oOwner->id == $oBreeder->id) {
                            $oAnimal->agents()->attach([
                                $oOwner->id => ['type' => ConstDb::AGENT_OWNER]
                            ]);
                            $oAnimal->agents()->attach([
                                $oBreeder->id => ['type' => ConstDb::AGENT_BREEDER]
                            ]);
                        } else {
                            $oAnimal->agents()->attach($dataAttach);
                        }
                        break;
                }


                //categories - catalog
                $idsCat = explode(',', $data['categories']);
                $lstCategoriesIds = (count($idsCat) > 0) ? Category::idsIn($idsCat)->get(['id'])->toArray() : null;

                $catalogs = [];

                if ($lstCategoriesIds) {
                    foreach ($lstCategoriesIds as $key => $value) {
                        $catalogs[] = new Catalog([
                            'category_id' => $value['id'],
                            'tournament_id' => $this->oTournament->id
                        ]);
                    }

                    $oAnimal->catalogs()->saveMany($catalogs);
                }

                DB::commit();

                $jResponse['success'] = true;
                $jResponse['url'] = route('oper.animal.index');
            } catch (Exception $ex) {
                DB::rollback();
                throw $ex;
            }
        } else {
            $jResponse['message'] = ConstMessages::NAME_WITHOUT_COMMA;
        }

        return $jResponse;
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

            $lstAnimal = Animal::name($name)->code($code, true)->whereNull('deleted_at')->get();

            if ($lstAnimal->count() <= 1) {
                $oAnimalS = ($lstAnimal->count() == 0) ? null : $lstAnimal->get(0);
                $oAnimal = Animal::with(['agents', 'catalogs'])->findorFail($id);

                if ($oAnimalS && $oAnimalS->id != $oAnimal->id) {
                    $jResponse['message'] = ConstMessages::ANIMAL_NAME_CODE_EXISTS;
                } else {
                    $jResponse = $this->saveAnimal($request->all(), $oAnimal);
                }

            } else {
                $jResponse['message'] = ConstMessages::ANIMAL_NAME_CODE_EXISTS;
            }
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
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
