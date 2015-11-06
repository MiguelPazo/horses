<?php namespace Horses\Http\Controllers\Admin;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Tournament;
use Horses\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    private $rules = [
        'description' => 'required|max:200',
        'type' => 'required',
        'count_competitors' => 'required',
        'num_begin' => 'required'
    ];

    public function getDisable($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $oCategory = Category::findorFail($id);

        if ($oCategory->status != ConstDb::STATUS_IN_PROGRESS) {
            $oCategory->status = ConstDb::STATUS_INACTIVE;
            $oCategory->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.category', $oCategory->tournament_id);
        } else {
            $jResponse['message'] = 'No puede desactivar una categoría en proceso de evaluación!';
        }

        return response()->json($jResponse);
    }

    public function getEnable($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $oCategory = Category::findorFail($id);

        $catInProgress = Category::status(ConstDb::STATUS_IN_PROGRESS)->count();

        if ($catInProgress == 0) {
            if ($oCategory->count_competitors > 0) {
                if ($oCategory->juries->count() == 3) {
                    Category::status(ConstDb::STATUS_ACTIVE)->tournament($oCategory->tournament_id)->update(['status' => ConstDb::STATUS_INACTIVE]);
                    Tournament::status(ConstDb::STATUS_ACTIVE)->update(['status' => ConstDb::STATUS_INACTIVE]);
                    Tournament::find($oCategory->tournament_id)->update(['status' => ConstDb::STATUS_ACTIVE]);

                    $oCategory->status = ConstDb::STATUS_ACTIVE;
                    $oCategory->save();

                    $jResponse['success'] = true;
                    $jResponse['url'] = route('admin.tournament.category', $oCategory->tournament_id);
                } else {
                    $jResponse['message'] = 'Falta asignar jueces a la categoría.';
                }
            } else {
                $jResponse['message'] = 'No puede activar una categoría con 0 competidores.';
            }

        } else {
            $jResponse['message'] = 'Existe otra categoría en proceso, espere a que termine. Sólo puede estar activa una categoría a la vez.';
        }

        return response()->json($jResponse);
    }

    public function getIndex($id)
    {
        if (isset($id)) {
            $oTournament = Tournament::findorFail($id);
            $lstCategory = Category::tournament($oTournament->id)->statusDiff(ConstDb::STATUS_DELETED)->get();

            return view('admin.category.index')
                ->with('lstCategory', $lstCategory)
                ->with('oTournament', $oTournament);
        }
    }

    public function getCreate($id)
    {
        $oTournament = Tournament::findorFail($id);
        $lstJuries = $this->listJuries();
        $lstJury = $lstJuries[0];
        $lstJuryCategory = $lstJuries[1];

        return view('admin.category.create')
            ->with('lstJury', $lstJury)
            ->with('lstJuryCategory', $lstJuryCategory)
            ->with('oTournament', $oTournament);
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
            $oTournament = Tournament::findorFail($id);
            $oCategory = new Category();

            $oCategory->description = $request->get('description');
            $oCategory->type = ($request->get('type') == 0) ? ConstDb::TYPE_CATEGORY_WSELECTION : ConstDb::TYPE_CATEGORY_SELECTION;
            $oCategory->count_competitors = $request->get('count_competitors');
            $oCategory->num_begin = $request->get('num_begin');
            $oCategory->tournament_id = $oTournament->id;
            $oCategory->save();

            $this->registerJuries($request, $oCategory->id);

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.category', $id);
        } else {
            $jResponse['message'] = 'Debe llenar todos los campos.';
        }

        return response()->json($jResponse);
    }

    public function getEdit($id)
    {
        $oCategory = Category::findorFail($id);
        $oTournament = Tournament::findorFail($oCategory->tournament_id);
        $lstJuries = $this->listJuries($oCategory->id);
        $lstJury = $lstJuries[0];
        $lstJuryCategory = $lstJuries[1];


        return view('admin.category.edit')
            ->with('oCategory', $oCategory)
            ->with('lstJury', $lstJury)
            ->with('lstJuryCategory', $lstJuryCategory)
            ->with('oTournament', $oTournament);
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
            $oCategory->count_competitors = $request->get('count_competitors');
            $oCategory->num_begin = $request->get('num_begin');
            $oCategory->save();

            $jResponse['success'] = true;
            $jResponse['url'] = route('admin.tournament.category', $oCategory->tournament_id);
        } else {
            $jResponse['message'] = 'Debe llenar todos los campos.';
        }

        return response()->json($jResponse);
    }

    public function getDestroy($id)
    {
        $oCategory = Category::findorFail($id);
        $oCategory->delete();

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
