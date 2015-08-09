<?php namespace Horses\Http\Controllers\Admin;

use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Tournament;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    private $rules = [
        'description' => 'required|max:200',
        'type' => 'required',
        'count_competitors' => 'required'
    ];

    public function getEnable($id)
    {
        Category::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);

        $oTournament = Category::findorFail($id);
        $oTournament->status = ConstDb::STATUS_ACTIVE;
        $oTournament->save();

        return redirect()->route('admin.category.index');
    }

    public function getIndex($id)
    {
        if (isset($id)) {
            $oTournament = Tournament::findorFail($id);
            $lstCategory = Category::tournament($oTournament->id)->get();
//            dd($oTournament);

            return view('admin.category.index')
                ->with('lstCategory', $lstCategory)
                ->with('oTournament', $oTournament);
        }
    }

    public function getCreate($id)
    {
        $oTournament = Tournament::findorFail($id);

        return view('admin.category.create')
            ->with('oTournament', $oTournament);
    }

    public function postStore($id, Request $request)
    {
        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oTournament = Tournament::findorFail($id);

            $oCategory = new Category();
            $oCategory->description = $request->get('description');
            $oCategory->type = ($request->get('type') == 0) ? ConstDb::TYPE_CATEGORY_WSELECTION : ConstDb::TYPE_CATEGORY_SELECTION;
            $oCategory->count_competitors = $request->get('count_competitors');
            $oCategory->tournament_id = $oTournament->id;
            $oCategory->save();

            return redirect()->route('admin.tournament.category', $id);
        } else {
            return redirect()->to('/admin/category/create/' . $id)->withErrors($validator)->withInput();
        }
    }

    public function getEdit($id)
    {
        //
    }


    public function postUpdate($id)
    {
        //
    }

    public function getDestroy($id)
    {
        //
    }

}
