<?php namespace Horses\Http\Controllers\Admin;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Services\Facades\CategoryFac;
use Horses\Tournament;
use Horses\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $rules = [
        'description' => 'required|max:200',
        'type' => 'required',
        'num_begin' => 'required'
    ];

    public function getIndex($id)
    {
        $lstCategory = CategoryFac::fetchAll($this->oTournament->id);

        return view('admin.category.index')
            ->with('lstCategory', $lstCategory)
            ->with('oTournament', $this->oTournament);
    }

    public function getCreate($id)
    {
        $lstJuries = $this->listJuries();
        $lstJury = $lstJuries[0];
        $lstJuryCategory = $lstJuries[1];
        $formHeader = ['url' => ['/admin/category/store', $this->oTournament->id], 'id' => 'formCategory', 'class' => 'formuppertext'];

        return view('admin.category.maintenance')
            ->with('lstJury', $lstJury)
            ->with('lstJuryCategory', $lstJuryCategory)
            ->with('oTournament', $this->oTournament)
            ->with('title', 'Nueva Categoría para ' . $this->oTournament->description)
            ->with('formHeader', $formHeader);
    }

    public function postStore($id, Request $request)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oCategory = new Category();

            $oCategory->description = $request->get('description');
            $oCategory->type = ($request->get('type') == 0) ? ConstDb::TYPE_CATEGORY_WSELECTION : ConstDb::TYPE_CATEGORY_SELECTION;
            $oCategory->num_begin = $request->get('num_begin');
            $oCategory->tournament_id = $this->oTournament->id;
            $oCategory->save();

            $this->registerJuries($request, $oCategory->id);

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.category', $id);
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    public function getEdit($id)
    {
        $oCategory = Category::findorFail($id);
        $lstJuries = $this->listJuries($oCategory->id);
        $lstJury = $lstJuries[0];
        $lstJuryCategory = $lstJuries[1];
        $formHeader = ['url' => ['/admin/category/update', $oCategory->id], 'method' => 'PUT', 'id' => 'formCategory', 'class' => 'formuppertext'];

        return view('admin.category.maintenance')
            ->with('oCategory', $oCategory)
            ->with('lstJury', $lstJury)
            ->with('lstJuryCategory', $lstJuryCategory)
            ->with('oTournament', $this->oTournament)
            ->with('title', 'Editar Categoría de ' . $this->oTournament->description)
            ->with('formHeader', $formHeader);
    }

    public function putUpdate($id, Request $request)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oCategory = Category::findorFail($id);

            $this->registerJuries($request, $oCategory->id);

            $oCategory->description = $request->get('description');
            $oCategory->type = ($request->get('type') == 0) ? ConstDb::TYPE_CATEGORY_WSELECTION : ConstDb::TYPE_CATEGORY_SELECTION;
            $oCategory->num_begin = $request->get('num_begin');
            $oCategory->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.category', $oCategory->tournament_id);
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    public function getDestroy($id)
    {
        $oCategory = Category::findorFail($id);
        $oCategory->status = ConstDb::STATUS_DELETED;
        $oCategory->save();

        return redirect()->route('admin.tournament.category', $oCategory->tournament_id);
    }

    public function listJuries($idCategory = null)
    {
        $listJury = [];

        $lstCategoryUser = CategoryUser::category($idCategory)->orderBy('dirimente', 'DESC')->get();
        $lstJury = User::profile(ConstDb::PROFILE_JURY)->status(ConstDb::STATUS_ACTIVE)->get();
        $lstJuryCategory = new Collection();

        foreach ($lstCategoryUser as $key => $catUser) {
            foreach ($lstJury as $key => $jury) {
                if ($jury->id == $catUser->user_id) {
                    $lstJuryCategory->add($jury);
                }
            }
        }

        $lstJury = $lstJury->diff($lstJuryCategory);

        $listJury[] = $lstJury;
        $listJury[] = $lstJuryCategory;

        return $listJury;
    }

    public function registerJuries($request, $idCategory)
    {
        $idsJury = $this->filterIds($request->all());
        $lstJury = User::users($idsJury)->get();

        CategoryUser::category($idCategory)->delete();

        foreach ($lstJury as $key => $jury) {
            $dirimet = ConstDb::JURY_NORMAL;

            if ($idsJury[0] == $jury->id) {
                $dirimet = ConstDb::JURY_DIRIMENT;
            }

            $oCatJury = CategoryUser::create([
                'user_id' => $jury->id,
                'dirimente' => $dirimet,
                'category_id' => $idCategory
            ]);
        }
    }

    public function filterIds($data)
    {
        $ids = [];

        foreach ($data as $key => $value) {
            if (strpos($key, ConstApp::PREFIX_JURY) !== false) {
                $id = str_replace(ConstApp::PREFIX_JURY, '', $key);

                if ($value != '0' && is_numeric($value)) {
                    $ids[] = $id;
                }
            }
        }

        return $ids;
    }

}
