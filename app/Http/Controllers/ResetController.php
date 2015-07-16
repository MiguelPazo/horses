<?php
/**
 * Created by PhpStorm.
 * User: mpazo
 * Date: 16/07/2015
 * Time: 9:42
 */

namespace Horses\Http\Controllers;


use Horses\Category;
use Horses\CategoryJury;
use Horses\Jury;
use Horses\Stage;

class ResetController extends Controller
{
    public function puestaCero()
    {
        $this->unlockJury();

        $lstCategory = CategoryJury::all();

        foreach ($lstCategory as $index => $categoryJury) {
            $categoryJury->delete();
        }

        $lstStage = Stage::all();

        foreach ($lstStage as $index => $stage) {
            $stage->delete();
        }

        $lstCategory = Category::all();

        foreach ($lstCategory as $index => $category) {
            $category->etapa_actual = '';
            $category->save();
        }

        echo 'Puesta cero completa!';
    }

    public function unlock()
    {
        $this->unlockJury();

        echo 'Usuarios desbloqueados';
    }

    public function unlockJury()
    {
        $lstJury = Jury::all();

        foreach ($lstJury as $index => $jury) {
            $jury->estado = 0;
            $jury->save();
        }
    }
}