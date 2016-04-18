<?php namespace Horses\Http\Controllers\Admin;

use Horses\Audit;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
use Horses\Http\Requests;
use Horses\Http\Controllers\Controller;

use Horses\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    private $rules = [
        'names' => 'required|max:50',
        'lastname' => 'required|max:80',
        'user' => 'required|max:15',
        'password' => 'required',
        'profile' => 'required',
    ];

    public function audit()
    {
        $lstAudit = DB::table('audit_user')
            ->get();

        return view('admin.user.audit')->with('lstAudit', $lstAudit);
    }

    public function unlock($id)
    {
        $oUser = User::findorFail($id);
        $oUser->login = ConstDb::USER_DISCONNECTED;
        $oUser->save();

        return redirect()->route('admin.user.index');
    }

    public function index()
    {
        $lstUser = User::status(ConstDb::STATUS_ACTIVE)->get();

        return view('admin.user.index')->with('lstUser', $lstUser);
    }

    public function create()
    {
        $formHeader = ['route' => 'admin.user.store', 'class' => 'formValid'];

        return view('admin.user.maintenance')
            ->with('passRequired', true)
            ->with('title', 'Nuevo Usuario')
            ->with('formHeader', $formHeader);
    }

    public function store(Request $request)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oUserS = User::user($request->get('user'))->status(ConstDb::STATUS_ACTIVE)->first();

            if ($oUserS == null) {
                $oUser = new User();
                $oUser->names = $request->get('names');
                $oUser->lastname = $request->get('lastname');
                $oUser->user = $request->get('user');
                $oUser->password = Hash::make($request->get('password'));
                $oUser->profile = $request->get('profile');
                $oUser->save();

                $jResponse['success'] = true;
                $jResponse['url'] = route('admin.user.index');
            } else {
                $jResponse['message'] = 'El usuario ya existe, pruebe uno diferente.';
            }

        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }

    public function edit($id)
    {
        $oUser = User::findorFail($id);
        $formHeader = ['route' => ['admin.user.update', $oUser->id], 'method' => 'PUT', 'class' => 'formValid'];

        return view('admin.user.maintenance')
            ->with('passRequired', false)
            ->with('oUser', $oUser)
            ->with('title', 'Editar Usuario')
            ->with('formHeader', $formHeader);
    }

    public function update($id, Request $request)
    {
        unset($this->rules['password']);

        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $validator = $this->validateForms($request->all(), $this->rules);

        if ($validator === true) {
            $oUserS = User::user($request->get('user'))->first();
            $process = false;
            $oUser = User::findorFail($id);

            if ($oUserS == null) {
                $process = true;
            } else if ($oUserS->id == $id) {
                $process = true;
            }

            if ($process) {
                $oUser->names = $request->get('names');
                $oUser->lastname = $request->get('lastname');
                $oUser->user = $request->get('user');
                $oUser->password = (trim($request->get('password')) != '') ? Hash::make($request->get('password')) : $oUser->password;
                $oUser->profile = $request->get('profile');

                $oUser->save();

                $jResponse['success'] = true;
                $jResponse['url'] = route('admin.user.index');
            } else {
                $jResponse['message'] = 'El usuario ya existe, puebe uno diferente.';
            }
        } else {
            $jResponse['message'] = ConstMessages::FORM_INCORRECT;
        }

        return response()->json($jResponse);
    }


    public function destroy($id)
    {
        $oUser = User::findOrFail($id);

        $oUser->status = ConstDb::STATUS_INACTIVE;
        $oUser->save();

        return redirect()->route('admin.user.index');
    }

}
