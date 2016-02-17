<?php namespace Horses\Services;

use Horses\Catalog;
use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function results($oCategory)
    {
        $jResponse = [
            'selection' => null,
            'lenCompNum' => null,
            'showSecond' => null,
            'juryDiriment' => null,
            'lstCompetitorWinners' => null,
            'lstCompetitorHonorable' => null,
        ];

        $lenCompNum = strlen(Competitor::category($oCategory->id)->max('number'));
        $selection = false;
        $showSecond = false;
        $juryDiriment = null;
        $lstCompetitorWinners = new Collection();
        $lstCompetitorHonorable = new Collection();

        if ($oCategory->actual_stage == ConstDb::STAGE_SELECTION) {
            $selection = true;
            $lstCompetitor = Competitor::category($oCategory->id)->status(ConstDb::COMPETITOR_PRESENT)->get();
            $lstCompetitor = $this->getAnimalsDetails($lstCompetitor, $oCategory);

            if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
                $lstCompetitorWinners = $lstCompetitor->filter(function ($item) {
                    return $item->position === 0;
                });
                $lstCompetitorHonorable = $lstCompetitor->filter(function ($item) {
                    return $item->position === null;
                });
            } else {
                foreach ($lstCompetitor as $key => $lstSubCompetitor) {
                    $selected = $lstSubCompetitor->filter(function ($item) {
                        return $item->position === 0;
                    })->first();

                    if ($selected) {
                        $lstCompetitorWinners->push($lstSubCompetitor);
                    } else {
                        $lstCompetitorHonorable->push($lstSubCompetitor);
                    }
                }
            }
        } else {
            $showSecond = ($oCategory->actual_stage == ConstDb::STAGE_CLASSIFY_2) ? true : false;
            $juryDiriment = CategoryUser::category($oCategory->id)->diriment(ConstDb::JURY_DIRIMENT)->first();
            $lstCompetitor = Competitor::category($oCategory->id)->classified()->with('stages.jury')->orderBy('position')->get();
            $lstCompetitor = $this->getAnimalsDetails($lstCompetitor, $oCategory);
            $count = $lstCompetitor->count();

            for ($i = 0; $i < $count; $i++) {
                if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
                    $lstCompetitor->get($i)->stages->sortBy(function ($item) {
                        return $item->jury->id;
                    });
                } else {
                    $subCount = $lstCompetitor->get($i)->count();

                    for ($y = 0; $y < $subCount; $y++) {
                        $lstCompetitor->get($i)->get($y)->stages->sortBy(function ($item) {
                            return $item->jury->id;
                        });
                    }
                }
            }

            $count = 1;

            foreach ($lstCompetitor as $key => $competitor) {
                if ($count <= ConstApp::MAX_WINNERS) {
                    $lstCompetitorWinners->add($competitor);

                } else if (($count - ConstApp::MAX_WINNERS) <= ConstApp::MAX_HONORABLE) {
                    $lstCompetitorHonorable->add($competitor);
                }

                $count++;
            }
        }

        $jResponse['selection'] = $selection;
        $jResponse['lenCompNum'] = $lenCompNum;
        $jResponse['showSecond'] = $showSecond;
        $jResponse['juryDiriment'] = $juryDiriment;
        $jResponse['lstCompetitorWinners'] = $lstCompetitorWinners;
        $jResponse['lstCompetitorHonorable'] = $lstCompetitorHonorable;

        return $jResponse;
    }

    private function getAnimalsDetails($lstCompetitor, $oCategory)
    {
        $numbers = [];
        $numbersGroup = [];
        $idsAnimals = [];

        $count = $lstCompetitor->count();

        for ($i = 0; $i < $count; $i++) {
            if ($oCategory->mode == ConstDb::MODE_PERSONAL) {
                $numbers[] = $lstCompetitor->get($i)->catalog;
            } else {
                $dataTemp = explode(',', $lstCompetitor->get($i)->catalog);
                $numbersGroup[] = $dataTemp;
                $lstCompetitor->get($i)->catalog = $dataTemp[0];

                for ($y = 1; $y < count($dataTemp); $y++) {
                    $compTemp = clone $lstCompetitor->get($i);
                    $compTemp->catalog = $dataTemp[$y];
                    $lstCompetitor->push($compTemp);
                }

                $numbers = array_merge($dataTemp, $numbers);
            }
        }

        $lstCatalog = Catalog::category($oCategory->id)->numberIn($numbers)->get(['animal_id', 'number']);

        foreach ($lstCatalog as $key => $value) {
            $idsAnimals[] = $value->animal_id;
        }

        $animalsDetails = DB::table('animal_report')->whereIn('id', $idsAnimals)->get();

        foreach ($lstCompetitor as $key => $value) {
            $animal = null;

            $oCatalog = $lstCatalog->filter(function ($item) use ($value) {
                return $item->number == $value->catalog;
            })->first();

            if ($oCatalog) {
                foreach ($animalsDetails as $key2 => $value2) {
                    if ($oCatalog->animal_id == $value2->id) {
                        $animal = $value2;
                        break;
                    }
                }
            }

            $value->animal_details = $animal;
        }

        if ($oCategory->mode == ConstDb::MODE_GROUP) {
            $lstGroupCompetitors = new \Illuminate\Support\Collection();

            foreach ($numbersGroup as $key => $value) {
                $lstSubCompetitors = new \Illuminate\Support\Collection();

                foreach ($value as $index => $catalog) {
                    $competitor = $lstCompetitor->filter(function ($item) use ($catalog) {
                        return $item->catalog == $catalog;
                    })->first();

                    $lstSubCompetitors->push($competitor);
                }

                $lstGroupCompetitors->push($lstSubCompetitors);
            }

            return $lstGroupCompetitors;
        }

        return $lstCompetitor;
    }

    public function fetchAll($idTournament)
    {
        $lstCategory = Category::tournament($idTournament)->statusDiff(ConstDb::STATUS_DELETED)->orderBy('order', 'ASC')->get();

        return $lstCategory;
    }

    public function enable($id)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'object' => null
        ];

        $catInProgress = Category::status(ConstDb::STATUS_ACTIVE)->count();

        if ($catInProgress == 0) {
            $oCategory = Category::findorFail($id);

            if ($oCategory->count_competitors > 0) {
                if ($oCategory->juries->count() == 3) {
                    DB::beginTransaction();

                    try {
                        Category::status(ConstDb::STATUS_ACTIVE)->tournament($oCategory->tournament_id)->update(['status' => ConstDb::STATUS_INACTIVE]);

                        if ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) {
                            $oCategory->actual_stage = ConstDb::STAGE_SELECTION;
                            CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_SELECTION]);
                        } else {
                            $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
                            CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_ASSISTANCE]);
                        }

                        $oCategory->status = ConstDb::STATUS_ACTIVE;
                        $oCategory->save();

                        DB::commit();

                        $jResponse['object'] = $oCategory;
                        $jResponse['success'] = true;
                    } catch (Exception $ex) {
                        DB::rollback();
                        throw $ex;
                    }
                } else {
                    $jResponse['message'] = 'Falta asignar jueces a la categoría.';
                }
            } else {
                $jResponse['message'] = 'No puede activar una categoría con 0 competidores.';
            }

        } else {
            $jResponse['message'] = 'Existe otra categoría en proceso, espere a que termine. Sólo puede estar activa una categoría a la vez.';
        }

        return $jResponse;
    }
}