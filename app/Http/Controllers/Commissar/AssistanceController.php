<?php namespace Horses\Http\Controllers\Commissar;

use Horses\Catalog;
use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class AssistanceController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
    }

    public function index($id)
    {
        $catalog = [];
        $totalPresent = 0;

        $oCategory = Category::tournament($this->oTournament->id)->status(ConstDb::STATUS_INACTIVE)->findorFail($id);
        $totalComp = $oCategory->num_begin + $oCategory->count_competitors;
        $maxCatalog = Catalog::tournament($this->oTournament->id)->max('number');
        $maxCatalog = ($maxCatalog) ? $maxCatalog : 0;
        $lstCatalog = null;
        $ids = [];

        if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
            $lstCatalog = Catalog::with('animals')->tournament($oCategory->tournament_id)->category($oCategory->id)->get();
        } else {
            $lstCatalog = Catalog::with('animals')->tournament($oCategory->tournament_id)->category($oCategory->id)->get();

        }

        if ($lstCatalog->count() > 0) {
            $lstCatalog = $lstCatalog->sortByDesc(function ($item) {
                $birth = $item->animals->birthdate;
                $birthDate = ($birth != null && $birth != '') ? \DateTime::createFromFormat('d-m-Y', $birth) : null;

                return $birthDate;
            });
        }

        if ($oCategory->actual_stage == ConstDb::STAGE_ASSISTANCE) {
            $lstCompetitors = Competitor::category($oCategory->id)->orderBy('number')->get();
            $catalogs = [];
            $pos = 1;

            foreach ($lstCompetitors as $key => $value) {
                if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
                    $catalogs[] = $value->catalog;
                } else {
                    $catalogs = array_merge($catalogs, explode(',', $value->catalog));
                }

                $dCat['number'] = $value->catalog;
                $dCat['group'] = $pos;
                $dCat['present'] = ($value->status == ConstDb::COMPETITOR_PRESENT) ? true : false;
                $totalPresent = ($value->status == ConstDb::COMPETITOR_PRESENT) ? $totalPresent + 1 : $totalPresent;
                $catalog[] = $dCat;

                $pos++;
            }

            //ids animals
            foreach ($lstCatalog as $key => $value) {
                $ids[] = $value->animal_id;
            }
        } else {
            if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
                foreach ($lstCatalog as $key => $value) {
                    if (!$value->number) {
                        $maxCatalog++;
                    }

                    $ids[] = $value->animal_id;
                    $dCat['number'] = ($value->number) ? $value->number : $maxCatalog;
                    $dCat['present'] = false;
                    $catalog[] = $dCat;
                }
            } else {
                $lstTempGroup = $lstCatalog->groupBy('group');
                $lstCatalogGroup = new Collection();
                $index = [];

                foreach ($lstTempGroup as $key => $value) {
                    $index[] = $key;
                }
                sort($index);

                foreach ($index as $key => $value) {
                    $lstCatalogGroup->put($value, $lstTempGroup->get($value));
                }

                foreach ($lstCatalogGroup as $key => $value) {
                    $strNumber = [];

                    foreach ($value as $key2 => $value2) {
                        if (!$value2->number) {
                            $maxCatalog++;
                        }

                        $strNumber[] = ($value2->number) ? $value2->number : $maxCatalog;
                        $ids[] = $value2->animal_id;
                    }

                    $dCat['number'] = implode(',', $strNumber);
                    $dCat['group'] = $key;
                    $dCat['present'] = false;
                    $catalog[] = $dCat;
                }
            }
        }

        return view('commissar.assistance')
            ->with('oTournament', $this->oTournament)
            ->with('catalog', $catalog)
            ->with('ids', implode(',', $ids))
            ->with('maxCatalog', $maxCatalog)
            ->with('totalPresent', $totalPresent)
            ->with('rpad', strlen($totalComp))
            ->with('oCategory', $oCategory);
    }

    public function save($id)
    {
        $oCategory = Category::status(ConstDb::STATUS_DELETED, false, true)->findorFail($id);
        $data = $this->request->all();
        $nCatalog = [];
        $nGroupCatalog = [];
        $totalPresent = 0;
        $idsSelected = explode(',', $data['ids_selected']);

        foreach ($data as $key => $value) {
            if (strpos($key, ConstApp::PREFIX_COMPETITOR) !== false) {
                if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
                    $posCat = strpos($key, ConstApp::PREFIX_COMPETITOR) + strlen(ConstApp::PREFIX_COMPETITOR);
                    $valCatalog = intval(substr($key, $posCat));

                    if (!in_array($valCatalog, $nCatalog)) {
                        $nCatalog[] = $valCatalog;
                    }
                } else {
                    $posCat = strpos($key, ConstApp::PREFIX_COMPETITOR) + strlen(ConstApp::PREFIX_COMPETITOR);
                    $valCatalog = substr($key, $posCat);
                    $groupCat = explode(',', $valCatalog);
                    $difCat = array_diff($groupCat, $nCatalog);
                    $nGroupCatalog[] = $difCat;
                    $nCatalog = array_merge($nCatalog, $difCat);
                }

            }
        }

        $lstCatalog = Catalog::tournament($oCategory->tournament_id)->numberIn($nCatalog)->orderBy('number')->get();
        $countCompetitors = count($nCatalog);
        $insertComp = [];
        $insertCatalog = [];
        $idsAnimalCatalogDelete = [];

        if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
            for ($i = 0; $i < $countCompetitors; $i++) {
                $numCatalog = $nCatalog[$i];
                $index = ConstApp::PREFIX_COMPETITOR . $numCatalog;

                $present = false;

                if (array_key_exists($index, $data) && is_numeric($data[$index]) && $data[$index] != '0') {
                    $present = true;
                    $totalPresent++;
                }

                $oCatalog = $lstCatalog->filter(function ($item) use ($numCatalog, $oCategory) {
                    return $item->number == $numCatalog && $item->category_id == $oCategory->id;
                })->first();

                if (!$oCatalog) {
                    $idAnimal = $idsSelected[$i];
                    $idsAnimalCatalogDelete[] = $idAnimal;

                    $insertCatalog[] = [
                        'number' => $numCatalog,
                        'category_id' => $oCategory->id,
                        'tournament_id' => $oCategory->tournament_id,
                        'animal_id' => $idAnimal,
                        'outsider' => 1
                    ];
                }

                $insertComp[] = [
                    'number' => $i + 1,
                    'category_id' => $oCategory->id,
                    'position' => ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) ? 1 : null,
                    'catalog' => $numCatalog,
                    'status' => ($present) ? ConstDb::COMPETITOR_PRESENT : ConstDb::COMPETITOR_MISSING
                ];
            }
        } else {
            $lstCat = Catalog::category($oCategory->id)->orderBy('number')->get();
            $maxGroup = $lstCat->max('group');
            $posCompetitor = 1;
            $postGroupId = 0;
            $countCompetitors = count($nGroupCatalog);

            foreach ($nGroupCatalog as $key => $value) {
                $present = false;
                $index = ConstApp::PREFIX_COMPETITOR . implode(',', $value);

                $lstTempCat = $lstCat->filter(function ($item) use ($value) {
                    return in_array($item->number, $value);
                });

                if (array_key_exists($index, $data) && is_numeric($data[$index]) && $data[$index] != '0') {
                    $present = true;
                    $totalPresent++;
                }

                if ($lstTempCat->count() > 0) {

                    foreach ($value as $key2 => $value2) {
                        $oCat = $lstTempCat->filter(function ($item) use ($value2) {
                            return $item->number == $value2;
                        })->first();

                        if (!$oCat) {
                            $idAnimal = $idsSelected[$postGroupId];
                            $idsAnimalCatalogDelete[] = $idAnimal;

                            $insertCatalog[] = [
                                'group' => $key + 1,
                                'number' => $value2,
                                'category_id' => $oCategory->id,
                                'tournament_id' => $oCategory->tournament_id,
                                'animal_id' => $idAnimal,
                                'outsider' => 1
                            ];
                        }

                        $postGroupId++;
                    }
                } else {
                    $maxGroup++;

                    foreach ($value as $key2 => $value2) {
                        $idAnimal = $idsSelected[$postGroupId];

                        $insertCatalog[] = [
                            'group' => $maxGroup,
                            'number' => $value2,
                            'category_id' => $oCategory->id,
                            'tournament_id' => $oCategory->tournament_id,
                            'animal_id' => $idAnimal,
                            'outsider' => 1
                        ];

                        $postGroupId++;
                    }
                }

                $insertComp[] = [
                    'number' => $posCompetitor,
                    'category_id' => $oCategory->id,
                    'position' => ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) ? 1 : null,
                    'catalog' => implode(',', $value),
                    'status' => ($present) ? ConstDb::COMPETITOR_PRESENT : ConstDb::COMPETITOR_MISSING
                ];

                $posCompetitor++;
            }
        }


        DB::beginTransaction();

        try {
            Catalog::category($oCategory->id)->animalIn($idsAnimalCatalogDelete)->delete();
            Competitor::category($oCategory->id)->delete();

            if (count($insertCatalog) > 0) {
                Catalog::insert($insertCatalog);
            }

            Competitor::insert($insertComp);

            if ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) {
                $oCategory->actual_stage = ConstDb::STAGE_SELECTION;
                CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_SELECTION]);
            } else {
                $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
                CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_ASSISTANCE]);
            }

            $oCategory->count_competitors = $countCompetitors;
            $oCategory->count_presents = $totalPresent;
            $oCategory->save();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

        return redirect()->to('/commissar');
    }
}
