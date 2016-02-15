<?php namespace Horses\Http\Controllers\GeneralCommissar;

use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Http\Controllers\Controller;
use Horses\Http\Requests;
use Horses\Services\Facades\CategoryFac;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($category = null)
    {
        $lstCategory = Category::with('juries')->tournament($this->oTournament->id)->statusIn([ConstDb::STATUS_ACTIVE, ConstDb::STATUS_FINAL])
            ->orderBy('order', 'DESC')->limit(2)->get();
        $count = $lstCategory->count();

        $oCategory = null;
        $data = null;
        $show = false;
        $suggest = 0;
        $wData = false;
        $selection = null;
        $lenCompNum = null;
        $showSecond = null;
        $juryDiriment = null;
        $lstCompetitorWinners = null;
        $lstCompetitorHonorable = null;


        if ($count > 0) {
            if ($category) {
                $oCategory = $lstCategory->filter(function ($item) use ($category) {
                    return $item->id == $category;
                })->first();

                if ($oCategory) {
                    $data = CategoryFac::results($oCategory);

                    if ($oCategory->status == ConstDb::STATUS_FINAL) {
                        if ($lstCategory->count() > 1) {
                            if (($lstCategory->get(0)->status == ConstDb::STATUS_ACTIVE
                                    || $lstCategory->get(0)->status == ConstDb::STATUS_FINAL)
                                && $lstCategory->get(0)->id != $oCategory->id
                            ) {
                                $suggest = $lstCategory->get(0)->id;
                            }
                        }
                    }
                } else {
                    return redirect()->to('/general-commissar/' . $lstCategory->get(0)->id);
                }
            } else {
                return redirect()->to('/general-commissar/' . $lstCategory->get(0)->id);
            }
        }


        if ($data) {
            $wData = true;
            $show = (count($data['lstCompetitorWinners']) > 0) ? true : false;
            $selection = $data['selection'];
            $lenCompNum = $data['lenCompNum'];
            $showSecond = $data['showSecond'];
            $juryDiriment = $data['juryDiriment'];
            $lstCompetitorWinners = $data['lstCompetitorWinners'];
            $lstCompetitorHonorable = $data['lstCompetitorHonorable'];
        }


        return view('gcommissar.home')
            ->with('oTournament', $this->oTournament)
            ->with('wData', $wData)
            ->with('show', $show)
            ->with('suggest', $suggest)
            ->with('oCategory', $oCategory)
            ->with('selection', $selection)
            ->with('lenCompNum', $lenCompNum)
            ->with('showSecond', $showSecond)
            ->with('juryDiriment', $juryDiriment)
            ->with('lstCompetitorWinners', $lstCompetitorWinners)
            ->with('lstCompetitorHonorable', $lstCompetitorHonorable);
    }
}
