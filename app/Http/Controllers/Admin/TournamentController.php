<?php namespace Horses\Http\Controllers\Admin;

use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{

    private $rules = [
        'description' => 'required|max:200',
        'date_begin' => 'required|date_format:d-m-Y',
        'date_end' => 'required'
    ];

    public function enable($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $catInProgress = Category::status(ConstDb::STATUS_IN_PROGRESS)->count();

        if ($catInProgress == 0) {
            Tournament::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

            $oTournament = Tournament::findorFail($id);
            $oTournament->status = ConstDb::STATUS_ACTIVE;
            $oTournament->save();

            Category::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'Existe otro concurso con una categoria en proceso, espere a que termine. Sólo puede estar activo un concurso con una categoría a la vez.';
        }

        return response()->json($jResponse);
    }

    public function disable($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $oTournament = Tournament::with('category')->findorFail($id);
        $catInProgress = Category::tournament($oTournament->id)->status(ConstDb::STATUS_IN_PROGRESS)->count();

        if ($catInProgress == 0) {
            $oTournament->status = ConstDb::STATUS_INACTIVE;
            $oTournament->save();

            Category::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'No puede desactivar una concurso con una categoría en proceso de evaluación!';
        }

        return response()->json($jResponse);
    }

    public function index()
    {
        $lstTournaments = Tournament::statusDif(ConstDb::STATUS_DELETED)->get();

        return view('admin.tournament.index')
            ->with('lstTournaments', $lstTournaments);
    }

    public function create()
    {
        return view('admin.tournament.create');
    }

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

    public function edit($id)
    {
        $oTournament = Tournament::findorFail($id);

        return view('admin.tournament.edit')->with('oTournament', $oTournament);
    }

    public function update($id, Request $request)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oTournament = Tournament::findorFail($id);
            $oTournament->description = $request->get('description');
            $oTournament->date_begin = $request->get('date_begin');
            $oTournament->date_end = $request->get('date_end');

            $oTournament->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'Debe llenar todos los campos.';
        }

        return response()->json($jResponse);
    }

    public function destroy($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $oTournament = Tournament::findorFail($id);

        if ($oTournament->status != ConstDb::STATUS_ACTIVE) {
            $oTournament->status = ConstDb::STATUS_DELETED;
            $oTournament->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'No puede eliminar un concurso activo, primero debe desactivarlo!';
        }

        return response()->json($jResponse);
    }

}
