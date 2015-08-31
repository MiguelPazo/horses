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
        $catInProgress = Category::status(ConstDb::STATUS_IN_PROGRESS)->count();
        $error = 0;

        if ($catInProgress == 0) {
            Tournament::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

            $oTournament = Tournament::findorFail($id);
            $oTournament->status = ConstDb::STATUS_ACTIVE;
            $oTournament->save();
        } else {
            $error = 1;
        }

        return redirect()->route('admin.tournament.index', $error);
    }

    public function disable($id)
    {
        $oTournament = Tournament::with('category')->findorFail($id);
        $catInProgress = Category::tournament($oTournament->id)->status(ConstDb::STATUS_IN_PROGRESS)->count();
        $error = 0;

        if ($catInProgress == 0) {
            $oTournament->status = ConstDb::STATUS_INACTIVE;
            $oTournament->save();
        } else {
            $error = 2;
        }

        return redirect()->route('admin.tournament.index', $error);
    }

    public function index($error = null)
    {
        $lstTournaments = Tournament::statusDif(ConstDb::STATUS_DELETED)->get();
        $errorMessage = null;

        $errorMessage = null;

        switch ($error) {
            case 1:
                $errorMessage = "Existe otro concurso con una categoria en proceso, espere a que termine. Sólo puede estar activo un concurso con una categoría a la ves.";
                break;
            case 2:
                $errorMessage = "No puede desactivar una concurso con una categoría en proceso de evaluación!";
                break;
        }

        return view('admin.tournament.index')
            ->with('errorMessage', $errorMessage)
            ->with('lstTournaments', $lstTournaments);
    }

    public function create()
    {
        return view('admin.tournament.create');
    }

    public function store(Request $request)
    {
        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oTournament = new Tournament($request->all());
            $oTournament->save();

            return redirect()->route('admin.tournament.index');
        } else {
            return redirect()->route('admin.tournament.create')->withErrors($validator)->withInput();
        }

    }

    public function edit($id)
    {
        $oTournament = Tournament::findorFail($id);

        return view('admin.tournament.edit')->with('oTournament', $oTournament);
    }

    public function update($id, Request $request)
    {
        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oTournament = Tournament::findorFail($id);
            $oTournament->description = $request->get('description');
            $oTournament->date_begin = $request->get('date_begin');
            $oTournament->date_end = $request->get('date_end');

            $oTournament->save();

            return redirect()->route('admin.tournament.index');
        } else {
            return redirect()->route('admin.tournament.show', $id)->withErrors($validator);
        }

    }

    public function destroy($id)
    {
        $oTournament = Tournament::findorFail($id);
        $oTournament->status = ConstDb::STATUS_DELETED;
        $oTournament->save();

        return redirect()->route('admin.tournament.index');
    }

}
