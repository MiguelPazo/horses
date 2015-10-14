<?php
/**
 * Created by PhpStorm.
 * User: mpazo
 * Date: 16/07/2015
 * Time: 9:42
 */

namespace Horses\Http\Controllers;


use Horses\Category;
use Horses\CategoryUser;
use Horses\Competitor;
use Horses\Constants\ConstApp;
use Horses\Constants\ConstDb;
use Horses\Tournament;
use Horses\User;
use Horses\Stage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetController extends Controller
{
    public function puestaCero()
    {
        DB::statement("SET foreign_key_checks=0");

        Stage::truncate();
        CategoryUser::truncate();
        User::truncate();
        Competitor::truncate();
        Category::truncate();
        Tournament::truncate();

        DB::statement("SET foreign_key_checks=1");

        User::create([
            'names' => 'Miguel',
            'lastname' => 'Pazo Sánchez',
            'user' => 'admin',
            'profile' => ConstDb::PROFILE_ADMIN,
            'password' => Hash::make(ConstApp::PASS_DEFAULT)
        ]);

        echo 'Puesta cero completa!';
    }
}