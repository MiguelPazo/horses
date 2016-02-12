<?php namespace Horses\Services;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Constants\ConstDb;
use Horses\Tournament;
use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;

class CategoryService
{
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

        $oCategory = Category::findorFail($id);

        $catInProgress = Category::status(ConstDb::STATUS_IN_PROGRESS)->count();

        if ($catInProgress == 0) {
            if ($oCategory->count_competitors > 0) {
                if ($oCategory->juries->count() == 3) {
                    DB::beginTransaction();
                    try {
                        Category::status(ConstDb::STATUS_ACTIVE)->tournament($oCategory->tournament_id)->update(['status' => ConstDb::STATUS_INACTIVE]);

                        $oCategory->status = ConstDb::STATUS_ACTIVE;
                        $oCategory->save();

                        if ($oCategory->type == ConstDb::TYPE_CATEGORY_WSELECTION) {
                            $oCategory->actual_stage = ConstDb::STAGE_SELECTION;
                            CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_SELECTION]);
                        } else {
                            $oCategory->actual_stage = ConstDb::STAGE_ASSISTANCE;
                            CategoryUser::category($oCategory->id)->update(['actual_stage' => ConstDb::STAGE_ASSISTANCE]);
                        }

                        $oCategory->status = ConstDb::STATUS_IN_PROGRESS;
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