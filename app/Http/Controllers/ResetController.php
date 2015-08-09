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
use Horses\Constants\ConstDb;
use Horses\User;
use Horses\Stage;

class ResetController extends Controller
{
    public function puestaCero()
    {
        $this->unlockUsers();

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
        $this->unlockUsers();

        echo 'Usuarios desbloqueados';
    }

    public function unlockUsers()
    {
        $lstUser = User::all();

        foreach ($lstUser as $index => $user) {
            $user->login = ConstDb::USER_DISCONNECTED;
            $user->save();
        }
    }
}