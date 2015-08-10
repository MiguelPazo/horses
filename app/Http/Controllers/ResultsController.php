<?php namespace Horses\Http\Controllers;

use Horses\Category;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\Tournament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ResultsController extends Controller
{

    public function index()
    {
        $oTournament = Tournament::status(ConstDb::STATUS_ACTIVE)->first();

        if ($oTournament) {
            $oCategory = Category::tournament($oTournament->id)->status(ConstDb::STATUS_FINAL)->first();
            if ($oCategory) {
                $lstCompetitor = Competitor::category($oCategory->id)->orderBy('position')->get();
                if ($lstCompetitor) {
                    $lstCompetitorLeft = new Collection();
                    $lstCompetitorRight = new Collection();
                    $count = 1;

                    foreach ($lstCompetitor as $key => $competitor) {
                        if ($competitor->position != null) {
                            if ($count <= ConstApp::MAX_WINNERS) {
                                $lstCompetitorLeft->add($competitor);
                                $count++;
                            } else {
                                $lstCompetitorRight->add($competitor);
                            }
                        }
                    }

                    return view('tournament.result')
                        ->with('oTournament', $oTournament)
                        ->with('oCategory', $oCategory)
                        ->with('lstCompetitorLeft', $lstCompetitorLeft)
                        ->with('lstCompetitorRight', $lstCompetitorRight);
                } else {

                }
            } else {

            }
        } else {

        }
    }
}
