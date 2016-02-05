<?php namespace Horses\Http\Controllers\Operator;

use Horses\Animal;
use Horses\Category;
use Horses\Constants\ConstDb;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class AnimalController extends Controller
{

    private $request;
    private $rules = [
        'description' => 'required|max:200',
        'date_begin' => 'required|date_format:d-m-Y',
        'date_end' => 'required'
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $oTournament = $this->request->session()->get('oTournament');
        $lstAnimals = Animal::with('agents')->get();

        return view('oper.animal.index')
            ->with('tournament', $oTournament->description)
            ->with('lstAnimals', $lstAnimals);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $lstCategory = Category::statusDiff(ConstDb::STATUS_DELETED)->get(['id', 'description']);

        return view('oper.animal.create')
            ->with('lstCategory', $lstCategory->toArray());
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
            $oTournament = new Tournament($request->all());
            $oTournament->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'Debe llenar todos los campos.';
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
        //
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
        //
    }

}
