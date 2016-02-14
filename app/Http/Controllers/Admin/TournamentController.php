<?php namespace Horses\Http\Controllers\Admin;

use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
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

    public function beginJournal($idTournament, $begin)
    {
        $oTournament = Tournament::findorFail($idTournament);

        $oTournament->status = ($begin == 1) ? ConstDb::STATUS_JOURNAL : ConstDb::STATUS_FINAL;
        $oTournament->save();

        return redirect()->route('admin.tournament.index');
    }

    public function enable($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $catInProgress = Tournament::status(ConstDb::STATUS_JOURNAL)->count();

        if ($catInProgress == 0) {
            Tournament::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);
            Category::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

            $oTournament = Tournament::findorFail($id);
            $oTournament->status = ConstDb::STATUS_ACTIVE;
            $oTournament->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'Existe otro concurso que ha inicado su jornada, debe finalizarla primero para activar este concurso.';
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

        $oTournament = Tournament::findorFail($id);

        if ($oTournament->status == ConstDb::STATUS_ACTIVE) {
            $oTournament->status = ConstDb::STATUS_INACTIVE;
            $oTournament->save();

            Category::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.index');
        } else {
            $jResponse['message'] = 'No puede desactivar un concurso con la jornada ya iniciada.';
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
        $formHeader = ['route' => 'admin.tournament.store', 'class' => 'formValid formuppertext'];

        return view('admin.tournament.maintenance')
            ->with('title', 'Nuevo Concurso')
            ->with('formHeader', $formHeader);
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
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    public function edit($id)
    {
        $oTournament = Tournament::findorFail($id);
        $formHeader = ['route' => ['admin.tournament.update', $oTournament->id], 'method' => 'PUT', 'class' =>
            'formValid formuppertext'];
        return view('admin.tournament.maintenance')
            ->with('oTournament', $oTournament)
            ->with('title', 'Editar Concurso')
            ->with('formHeader', $formHeader);
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
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
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
