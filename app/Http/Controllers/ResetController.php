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
use Horses\Constants\ConstDb;
use Horses\Tournament;
use Horses\User;
use Horses\Stage;

class ResetController extends Controller
{
    public function puestaCero()
    {
        $lstTournament = Tournament::all();
        $lstUser = User::all();

        foreach ($lstTournament as $key => $tournament) {
            $tournament->delete();
        }

        foreach ($lstUser as $key => $user) {
            $user->delete();
        }

        User::create([
            'names' => 'Miguel',
            'lastname' => 'Pazo Sánchez',
            'user' => 'mpazo',
            'profile' => ConstDb::PROFILE_ADMIN,
            'password' => '123'
        ]);

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