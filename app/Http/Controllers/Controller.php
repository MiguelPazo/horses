<?php namespace Horses\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;

abstract class Controller extends BaseController
{

    use DispatchesCommands, ValidatesRequests;

    protected $oTournament;

    public function __construct(Request $request)
    {
        $this->oTournament = $request->session()->get('oTournament');
    }

    protected function validateForms($input, $rules)
    {
        $niceNames = [
            'description' => 'Descripción',
            'date_begin' => 'Fecha de Inicio',
            'date_end' => 'Fecha de Cierre',
            'type' => 'Selección',
            'count_competitors' => 'Cantidad de Competidores',
            'names' => 'Nombres',
            'lastname' => 'Apellidos',
            'user' => 'Usuario',
            'password' => 'Contraseña',
            'profile' => 'Perfil',
            'num_begin' => 'Número del Primer Competidor'

        ];

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'min' => 'El campo :attribute no puede tener menos de :min carácteres.',
            'max' => 'El campo :attribute no puede tener más de :min carácteres.',
            'date_format' => 'El campo :attribute tiene el formato de fecha incorrecto'
        ];

        $validation = Validator::make($input, $rules, $messages);
        $validation->setAttributeNames($niceNames);

        if ($validation->fails()) {
            return $validation;
        } else {
            return true;
        }
    }

}
