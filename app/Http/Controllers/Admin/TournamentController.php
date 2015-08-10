<?php namespace Horses\Http\Controllers\Admin;

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
        Tournament::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

        $oTournament = Tournament::findorFail($id);
        $oTournament->status = ConstDb::STATUS_ACTIVE;
        $oTournament->save();

        return redirect()->route('admin.tournament.index');
    }

    public function disable($id)
    {
        $oTournament = Tournament::findorFail($id);
        $oTournament->status = ConstDb::STATUS_INACTIVE;
        $oTournament->save();
        return redirect()->route('admin.tournament.index');
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
